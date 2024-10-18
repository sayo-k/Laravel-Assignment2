<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    //public function store(Request $request, $courseId)
    public function store(Request $request)
    {
        $errors = [];

        // Manual input validation
        $courseId = $request->input('course_id');
        $title = $request->input('title');
        $instruction = $request->input('instruction');
        $requiredReviews = $request->input('required_reviews');
        $maxScore = $request->input('max_score');
        $dueDate = $request->input('due_date');
        $type = $request->input('type');

        // Validate 'course_id'
        if (!is_numeric($courseId)) {
            $errors[] = "Course ID must be a valid integer.";
        }

        // Validate 'title' (required, string, max 20 characters)
        if (empty($title) || !is_string($title) || strlen($title) > 20) {
            $errors[] = "Title is required and must be a string with a maximum of 20 characters.";
        }

        // Validate 'instruction' (required, string)
        if (empty($instruction) || !is_string($instruction)) {
            $errors[] = "Instruction is required and must be a valid string.";
        }

        // Validate 'required_reviews' (required, integer, min 1)
        if (!is_numeric($requiredReviews) || $requiredReviews < 1) {
            $errors[] = "Required reviews must be an integer greater than or equal to 1.";
        }

        // Validate 'max_score' (required, integer between 1 and 100)
        if (!is_numeric($maxScore) || $maxScore < 1 || $maxScore > 100) {
            // Add custom error message if greater than 100
            if ($maxScore > 100) {
                $errors[] = "Max score must not exceed 100.";
            } else {
                $errors[] = "Max score must be an integer between 1 and 100.";
            }
        }

        // Validate 'due_date' (required, valid date)
        if (!strtotime($dueDate)) {
            $errors[] = "Due date must be a valid date.";
        }

        // Validate 'type' (required, either 'student-select' or 'teacher-assign')
        if (!in_array($type, ['student-select', 'teacher-assign'])) {
            $errors[] = "Type must be either 'student-select' or 'teacher-assign'.";
        }

        // If there are validation errors, redirect back with errors
        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        
        


        // Insert the new assessment
        $assessment = new Assessment();
        $assessment->course_id = $courseId;
        $assessment->title = $title;
        $assessment->instruction = $instruction;
        $assessment->required_reviews = $requiredReviews;
        $assessment->max_score = $maxScore;
        $assessment->due_date = $dueDate;
        $assessment->type = $type;
        $assessment->created_at = now();
        $assessment->updated_at = now();
        $assessment->save();

        // Redirect back to the course page with a success message
        return redirect()->route('courses.show', $courseId)->with('success', 'Peer Review Assessment added successfully!');
    }

    // retrieves the assessment data and passes it to a view
    public function update(Request $request, $id)
    {
        $errors = [];

        // fetch the assessment by ID
        $assessment = Assessment::find($id);

        if (!$assessment) {
            return redirect()->back()->with('error', 'Assessment not found.');
        }

        // Check if any reviews have been submitted for this assessment
        $reviewExists = Review::where('assessment_id', $id)->exists();

        if ($reviewExists) {
            return redirect()->back()->with('error', 'Assessment cannot be updated as there are existing reviews.');
        }

        // Manual input validation
        $title = $request->input('title');
        $instruction = $request->input('instruction');
        $requiredReviews = $request->input('required_reviews');
        $maxScore = $request->input('max_score');
        $dueDate = $request->input('due_date');
        $type = $request->input('type');

        // Validate 'title' (required, string, max 20 characters)
        if (empty($title) || !is_string($title) || strlen($title) > 20) {
            $errors[] = "Title is required and must be a string with a maximum of 20 characters.";
        }

        // Validate 'instruction' (required, string)
        if (empty($instruction) || !is_string($instruction)) {
            $errors[] = "Instruction is required and must be a valid string.";
        }

        // Validate 'required_reviews' (required, integer, min 1)
        if (!is_numeric($requiredReviews) || $requiredReviews < 1) {
            $errors[] = "Required reviews must be an integer greater than or equal to 1.";
        }

        // Validate 'max_score' (required, integer between 1 and 100)
        if (!is_numeric($maxScore) || $maxScore < 1 || $maxScore > 100) {
            $errors[] = "Max score must be an integer between 1 and 100.";
        }

        // Validate 'due_date' (required, valid date)
        if (!strtotime($dueDate)) {
            $errors[] = "Due date must be a valid date.";
        }

        // Validate 'type' (required, either 'student-select' or 'teacher-assign')
        if (!in_array($type, ['student-select', 'teacher-assign'])) {
            $errors[] = "Type must be either 'student-select' or 'teacher-assign'.";
        }

        // If there are validation errors, redirect back with errors
        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Update the assessment
        $assessment = Assessment::find($id);

        if ($assessment) {
            $assessment->update([
                'title' => $title,
                'instruction' => $instruction,
                'required_reviews' => $requiredReviews,
                'max_score' => $maxScore,
                'due_date' => $dueDate,
                'type' => $type,
                'updated_at' => now(),
            ]);

        // Redirect back to the course page with a success message
        return redirect()->route('courses.show', $assessment->course_id)->with('success', 'Peer Review Assessment updated successfully!');
    }
    }

    public function studentShow($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $user = auth()->user();
        
        // Fetch students in the course for "student-select" assessments
        $enrolledStudents = $assessment->enrolledStudents()->get();

        // Fetch reviews submitted by this student for the assessment
        $submittedReviews = Review::where('reviewer_id', $user->id)
                                  ->where('assessment_id', $assessmentId)
                                  ->get();

        // Fetch reviews received by this student for the assessment
        $receivedReviews = Review::where('reviewee_id', $user->id)
                                 ->where('assessment_id', $assessmentId)
                                 ->with('reviewer') // Load reviewer details
                                 ->get();
        
        $revieweeId = $user->id;
        $reviewer = $user;


        //Fetch teacher-assigned reviews for the student (if assessment type is teacher-assign)
        //$assignedReviews = collect(); // Initialize as a collection
        //    if ($assessment->type === 'teacher-assign') {
        //        $assignedReviews = Review::where('reviewer_id', $user->id)
        //                                ->where('assessment_id', $assessmentId)
        //                                ->whereNull('review_text') // Only fetch reviews that haven't been completed yet
        //                                ->with('reviewee') // Load reviewee details
        //                                ->get();
        //}

        //return view('assessments.student_show', compact('assessment', 'enrolledStudents', 'submittedReviews', 'receivedReviews', 'assignedReviews'));
        return view('assessments.student_show', compact('assessment', 'enrolledStudents', 'submittedReviews', 'receivedReviews', 'assessmentId', 'revieweeId', 'reviewer'));
    }

    public function teacherShow($id)
    {
        // Check if the user is authenticated and has the role of a teacher
        if (!Auth::check() || !Auth::user()->isTeacher()) {
            return redirect()->route('home')->with('error', 'Unauthorized access.'); // Redirect if not authorized
        }

        $assessment = Assessment::findOrFail($id);
    
        // Fetch the enrolled students for this course with pagination
        $enrolledStudents = $assessment->course->enrolledStudents()->paginate(10);
    
        // Fetch reviews for each student (submitted and received) and the score for each student
        foreach ($enrolledStudents as $student) {
            // Count the reviews submitted by this student
            $student->submittedReviewsCount = Review::where('reviewer_id', $student->id)
                                                    ->where('assessment_id', $id)
                                                    ->count();

            // Count the reviews received by this student
            $student->receivedReviewsCount = Review::where('reviewee_id', $student->id)
                                                    ->where('assessment_id', $id)
                                                    ->count();

            // Retrieve the student's score for the assessment (assuming you have a method for this)
            $student->score = $student->getScoreForAssessment($id);
        }
    
        // Return view with the data
        return view('assessments.teacher_show', compact('assessment', 'enrolledStudents'));
    }

    public function getScoreForAssessment($assessmentId)
    {
        // Fetch the record from the student_assessments table for this student and assessment
        $studentAssessment = StudentAssessment::where('student_id', $this->id)
            ->where('assessment_id', $assessmentId)
            ->first();

        // Return the score if it exists, otherwise return 0
        return $studentAssessment ? $studentAssessment->score : 0;
    }
    
    public function updateScore(Request $request, $assessmentId, $studentId)
    {
        // Retrieve the assessment to get the maximum score
        $assessment = Assessment::find($assessmentId);
        
        $errors = [];

        // Manual validation for 'score'
        $score = $request->input('score');

         // Validate the score against max_score
        if (is_null($score) || !is_numeric($score) || (int)$score < 0 || (int)$score > $assessment->max_score) {
            $errors[] = "Score is required and must be an integer between 0 and " . $assessment->max_score . ".";
        }

        // If there are validation errors, redirect back with errors
        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // user is a student
        $student = User::where('id', $studentId)
            ->where('role', 'student') // Ensure the user is a student
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'The specified user is not a student.');
        }

        // Find the record in the student_assessments table
        $studentAssessment = DB::table('student_assessments')
            ->where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->first();

        // Check if the student_assessment exists, if not create a new record
        if ($studentAssessment) {
            // Update the score if the record exists
            DB::table('student_assessments')
                ->where('student_id', $studentId)
                ->where('assessment_id', $assessmentId)
                ->update(['score' => $score, 'updated_at' => now()]);
        } else {
            // Create a new record if it doesn't exist
            DB::table('student_assessments')->insert([
                'student_id' => $studentId,
                'assessment_id' => $assessmentId,
                'score' => $score,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Score updated successfully.');
    }
}