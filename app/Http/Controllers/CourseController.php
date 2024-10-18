<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Support\Facades\Storage;



class CourseController extends Controller
{
    public function index()
    {
        // Get the currently logged-in user
        $user = auth()->user();

        // Initialize $courses variable
        $courses = collect(); // Start with an empty collection

        // Retrieve courses based on user role
        if ($user->role === 'student') {
            // For students, get enrolled courses along with assessments
            $courses = $user->coursesEnrolled()->with('assessments')->get();

            // For students, get enrolled courses
            //$courses = $user->coursesEnrolled;
        } elseif ($user->role === 'teacher') {
            // For teachers, get courses they are teaching along with assessments
            $courses = Course::where('teacher_id', $user->id)->with('assessments')->get();

            // For teachers, get courses they are teaching
            //$courses = $user->coursesTeaching;
            //$courses = Course::where('teacher_id', $user->id)->get();
        } else {
            // In case of an unknown role, set courses to an empty collection
            $courses = collect();
        }

        // Debug: Check the user and courses data
        // dd($user, $courses);  // This will show both the user and the courses

        // Return the view with the courses data
        return view('courses.index', compact('user', 'courses'));
    }

    public function show($id)
    {
        $course = Course::find($id);
        $courseId = $course->id;
        // Fetch the course by ID, load teacher and assessments
        // This throw a 404 if not found
        $course = Course::with(['teacher', 'assessments'])->findOrFail($id);

        // Get the currently logged-in user
        $user = auth()->user();

        // Redirect to login if no user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this course.');
        }

        // Fetch the list of students who are not enrolled in this course
        $students = User::where('role', 'student')
            ->whereNotIn('id', $course->students()->pluck('student_id'))
            ->get();

        // Return the view with the course details
        return view('courses.show', compact('course', 'user', 'students', 'courseId'));
    }

    public function enrollStudent(Request $request, $courseId)
    {
        $student = User::findOrFail($request->student_id);
        $course = Course::findOrFail($courseId);
        
        // Enroll student if not already enrolled
        if (!$course->students()->where('student_id', $student->id)->exists()) {
            $course->students()->attach($student->id);
        
        // Flash a success message to the session
            session()->flash('success', "{$student->name} has been successfully enrolled in the course.");
        } else {
            // Optionally, flash an error message if already enrolled
            session()->flash('error', "{$student->name} is already enrolled in this course.");
        }

        return redirect()->route('courses.show', $course->id);
    }

    //file upload 
    public function uploadCourse(Request $request)
    {
        // Validate the file
        $request->validate([
            'courseFile' => 'required|file|mimes:txt',
        ]);

        // Store and read the uploaded file
        $filePath = $request->file('courseFile')->store('uploads');
        $fileContent = Storage::get($filePath);
        $lines = explode("\n", $fileContent);

        // Extract course details
        $courseCode = $this->extractLineData($lines, 'Course Code:');
        $courseName = $this->extractLineData($lines, 'Course Name:');
        $teacherId= $this->extractLineData($lines, 'TeacherId:');
        
        // Find the teacher by ID
        $teacher = User::where('id', $teacherId)->where('role', 'teacher')->first();

        if (!$teacher) {
            // Handle the case where the teacher ID does not exist or the role is not 'teacher'
            return back()->with('error', 'Teacher not found in the system.');
        }

        // Retrieve the teacher's name
        $teacherName = $teacher->name;
        
        // Check if course with the same course code already exists
        if (Course::where('course_code', $courseCode)->exists()) {
            return back()->with('error', 'Course with this course code already exists.');
        }

        // Create the course with the given teacher
        $course = Course::create([
            'course_code' => $courseCode,
            'name' => $courseName,
            'teacher_id' => $teacherId, // Associate course with the teacher's ID
        ]);

        // Add assessments
        $assessmentLines = $this->extractBlockData($lines, 'Assessments:');
        foreach ($assessmentLines as $assessmentLine) {
            //$assessmentData = explode('|', $assessmentLine);
            $assessmentData = explode('|', $assessmentLine);
                if (count($assessmentData) < 6) {
                    return back()->with('error', 'Assessment data is incomplete. Please ensure all fields are provided.');
            }
            $title = trim($assessmentData[0]);
            $dueDate = trim(str_replace('Due Date:', '', $assessmentData[1]));
            $maxScore = trim(str_replace('Max Score:', '', $assessmentData[2]));
            $instruction = trim(str_replace('Instruction:', '', $assessmentData[3]));
            $type = trim(str_replace('Type:', '', $assessmentData[4]));
            $requiredReviews = (int) trim(str_replace('Required Reviews:', '', $assessmentData[5]));

            if ($type == "teacherassign") {
                $type = "teacher-assign";
            } elseif ($type == "studentselect") {
                $type = "student-select";
            }
            
            // Validate the type to ensure it's one of the allowed values
            if (!in_array($type, ['student-select', 'teacher-assign'])) {
                return back()->with('error', 'Invalid assessment type provided.');
            }

            Assessment::create([
                'course_id' => $course->id,
                'title' => $title,
                'max_score' => $maxScore,
                'due_date' => $dueDate,
                'instruction' => $instruction,
                'type' => $type,
                'required_reviews' => $requiredReviews,
            ]);
        }

        // Enroll students
        $studentIds = $this->extractBlockData($lines, 'Students:');
        foreach ($studentIds as $studentNumber) {
            $student = User::firstOrCreate(
                ['s_number' => $studentNumber],
                ['role' => 'student'] // Ensure only students are added
            );
            $course->students()->attach($student->id); // Enroll students in the course
        }

        return back()->with('success', 'Course and associated data uploaded successfully!');
    }

    private function extractLineData($lines, $prefix)
    {
        foreach ($lines as $line) {
            if (strpos($line, $prefix) === 0) {
                return trim(str_replace($prefix, '', $line));
            }
        }
        return null;
    }

    private function extractBlockData($lines, $blockHeader)
    {
        $blockData = [];
        $blockStarted = false;

        foreach ($lines as $line) {
            if (strpos($line, $blockHeader) === 0) {
                $blockStarted = true;
                continue;
            }

            if ($blockStarted) {
                if (trim($line) === '') {
                    break; // End of block
                }
                $blockData[] = trim(str_replace('-', '', $line));
            }
        }

        return $blockData;
    }

}
