<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Assessment;
use App\Models\User;
use App\Models\ReviewRating;
use Illuminate\Support\Facades\DB;


class ReviewController extends Controller
{
    public function store(Request $request, $assessmentId)
    {

        $user = auth()->user();
        $errors = [];

        // Manual input validation
        $revieweeIds = $request->input('reviewee_id', []);
        $reviewTexts = $request->input('review_text', []);

        // Validate that 'reviewee_id' is an array with at least 1 entry
        if (!is_array($revieweeIds) || count($revieweeIds) < 1) {
            $errors[] = "At least one reviewee is required.";
        }

        // Validate that each 'reviewee_id' exists in the 'users' table
        foreach ($revieweeIds as $revieweeId) {
            $exists = DB::table('users')->where('id', $revieweeId)->exists();
            if (!$exists) {
                $errors[] = "Reviewee with ID {$revieweeId} does not exist.";
            }
        }

        // Validate that 'review_text' is an array with at least 1 entry
        if (!is_array($reviewTexts) || count($reviewTexts) < 1) {
            $errors[] = "At least one review text is required.";
        }

        // Validate that each 'review_text' has at least 5 characters
        foreach ($reviewTexts as $index => $reviewText) {
            if (strlen($reviewText) < 5) {
                $errors[] = "Review text at index {$index} must be at least 5 characters long.";
            }
        }

        // Ensure each reviewee is unique
        if (count($revieweeIds) !== count(array_unique($revieweeIds))) {
            $errors[] = "Each review must be for a different student.";
        }

        // If there are validation errors, redirect back with the errors
        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Loop through each review to save them manually using raw SQL queries
        foreach ($revieweeIds as $index => $revieweeId) {
            // Check if the user already reviewed this student for this assessment using raw SQL
            $existingReview = DB::table('reviews')
                                ->where('reviewer_id', $user->id)
                                ->where('assessment_id', $assessmentId)
                                ->where('reviewee_id', $revieweeId)
                                ->first();

            if ($existingReview) {
                return redirect()->back()->with('error', 'You have already reviewed this student.');
            }

            // Store the review using raw SQL query
            DB::table('reviews')->insert([
                'reviewer_id' => $user->id,
                'reviewee_id' => $revieweeId,
                'assessment_id' => $assessmentId,
                'review_text' => $reviewTexts[$index],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('assessments.student_show', $assessmentId)->with('success', 'Review submitted successfully.');
    }

    public function showStudentReviews($assessmentId, $studentId)
    {
        // Retrieve the assessment and student
        $assessment = Assessment::findOrFail($assessmentId);
        $student = User::findOrFail($studentId);

        // Fetch the reviews submitted by the student for this assessment
        $reviews = Review::where('assessment_id', $assessmentId)
                        ->where('reviewer_id', $studentId)
                        ->get();

        $reviewsReceived = Review::where('assessment_id', $assessmentId)
                        ->where('reviewee_id', $studentId)
                        ->get();

    
        // Fetch the enrolled students for this course
        $enrolledStudents = $assessment->course->enrolledStudents()->get();

        // Pass the data to the view
        return view('reviews.show', compact('assessment', 'student', 'reviews', 'reviewsReceived', 'enrolledStudents'));
    }

    public function showAssignReviewersForm(Assessment $assessment)
    {
        // Get all students enrolled in the course associated with the assessment
        $students = $assessment->enrolledStudents()->get();

        // Determine the group size based on the required reviews
        $groupSize = $assessment->required_reviews + 1; // e.g., if required_reviews = 3, groupSize = 4

        // Shuffle the students to randomize the groups
        $students = $students->shuffle();

        // Calculate the total number of students
        $totalStudents = $students->count();

        // Calculate the number of full groups and the remainder
        $numFullGroups = intdiv($totalStudents, $groupSize); // Full groups count
        $remainder = $totalStudents % $groupSize; // Number of extra students

        // Create groups
        $groups = [];
        $start = 0;

        // Loop through the full groups and assign students to each group
        for ($i = 0; $i < $numFullGroups; $i++) {
            $groups[] = $students->slice($start, $groupSize);
            $start += $groupSize;
        }

        // If there are remaining students, distribute them evenly across the existing groups
        if ($remainder > 0) {
            $remainingStudents = $students->slice($start, $remainder);
            foreach ($remainingStudents as $index => $student) {
                // Add the remaining student to the appropriate group
                $groups[$index % $numFullGroups]->push($student);
            }
        }

        return view('reviews.teacher-assign', compact('students', 'groups', 'assessment'));
    }



    public function assignReviewers(Request $request, Assessment $assessment)
    {
        // Validate incoming request data
        $request->validate([
            'groups' => 'required|array',
        ]); 

        // Retrieve groups from the form submission
        $groups = $request->input('groups');

        // Now assign reviews for each student in the groups
        foreach ($groups as $group) {
            foreach ($group as $reviewerId) {
                // Initialize a counter for the number of reviews assigned to this reviewer
                $reviewCount = 0;

                // Get reviewer by ID
                $reviewer = User::find($reviewerId);

                if (!$reviewer) {
                    continue; // Skip if the reviewer is not found
                }
        
                // Randomly pick reviewees from the group excluding the reviewer
                // Filter the group to exclude the reviewer
                $reviewees = array_filter($group, function ($id) use ($reviewerId) {
                    return $id != $reviewerId; // Exclude the reviewer
                });
        
                // Shuffle the reviewees array
                $reviewees = collect($reviewees)->shuffle();
        
                foreach ($reviewees as $revieweeId) {
                    if ($reviewCount < $assessment->required_reviews) {
                        // Check if the review assignment already exists
                        $existingAssignment = Review::where('reviewer_id', $reviewer->id)
                            ->where('reviewee_id', $revieweeId)
                            ->where('assessment_id', $assessment->id)
                            ->first();
        
                        if (!$existingAssignment) {
                            // Create a new review assignment
                            Review::create([
                                'reviewer_id' => $reviewer->id,
                                'reviewee_id' => $revieweeId,
                                'assessment_id' => $assessment->id,
                                'review_text' => '', // Empty for now
                            ]);
        
                            // Increment the review count for this reviewer
                            $reviewCount++;
                        }
                    }
                }
        
                // Handle cases where the group is smaller than the required size
                if ($reviewCount < $assessment->required_reviews) {
                    foreach ($groups as $otherGroup) {
                        // Skip the current group to avoid reviewing within the same group
                        if ($otherGroup === $group) {
                            continue;
                        }

                        foreach ($otherGroup as $additionalRevieweeId) {
                            if ($reviewCount < $assessment->required_reviews) {
                                // Get the additional reviewee
                                $additionalReviewee = User::find($additionalRevieweeId);

                                // Check if the review assignment already exists
                                $existingAssignment = Review::where('reviewer_id', $reviewer->id)
                                    ->where('reviewee_id', $additionalReviewee->id)
                                    ->where('assessment_id', $assessment->id)
                                    ->first();

                                if (!$existingAssignment) {
                                    // Create a new review assignment
                                    Review::create([
                                        'reviewer_id' => $reviewer->id,
                                        'reviewee_id' => $additionalReviewee->id,
                                        'assessment_id' => $assessment->id,
                                        'review_text' => '', // Empty for now
                                    ]);

                                    // Increment the review count for this reviewer
                                    $reviewCount++;
                                }
                            }
                        }
                    }
                }
            }
        // Redirect to a success page
        return redirect()->route('assessments.teacher_show', compact('assessment'))->with('success', 'Reviewers assigned successfully in groups.');
    }
    }
    
    public function update(Request $request, $assessmentId)
    {
        $user = auth()->user();
        
        // Validate the incoming request
        $request->validate([
            'reviewee_id.*' => 'required|exists:users,id',
            'review_text.*' => 'required|min:5',
        ]);

        // Loop through each review to update
        foreach ($request->reviewee_id as $key => $revieweeId) {
            // Find the review by reviewer and reviewee ID
            $review = Review::where('reviewer_id', $user->id)
                            ->where('assessment_id', $assessmentId)
                            ->where('reviewee_id', $revieweeId)
                            ->first();

            if ($review) {
                $review->review_text = $request->review_text[$key];
                $review->save();
            }
        }

        return redirect()->route('assessments.student_show', $assessmentId)->with('success', 'Assigned reviews submitted successfully.');
    }

    public function storeReviewRatings(Request $request, $assessmentId)
    {
        $user = auth()->user();
        $ratings = $request->input('clarity_rating'); // Ratings for each review

        foreach ($ratings as $reviewId => $clarityRating) {
            // Create or retrieve the review rating for the specified review
            $reviewRating = ReviewRating::firstOrNew(['review_id' => $reviewId]);
    
            // Assign values to the fields
            $reviewRating->reviewee_id = $user->id; // User ID
            $reviewRating->clarity_rating = $clarityRating; // Clarity rating
            $reviewRating->constructiveness_rating = $request->input('constructiveness_rating')[$reviewId]; // Constructiveness
            $reviewRating->specificity_rating = $request->input('specificity_rating')[$reviewId]; // Specificity
            $reviewRating->tone_rating = $request->input('tone_rating')[$reviewId]; // Tone rating

            // Check if reviewer_id is set
            if ($request->has('reviewer_id') && array_key_exists($reviewId, $request->input('reviewer_id'))) {
                $reviewRating->reviewer_id = $request->input('reviewer_id')[$reviewId];
            } else {
                \Log::warning("Reviewer ID not found for review ID: $reviewId"); // Log warning for debugging
                continue; // Skip this review if reviewer_id is not found
            }

            //$reviewRating->reviewee_id = $request->input('reviewee_id')[$reviewId];

            //dd($reviewRating);
    
            // Save the review rating
            $reviewRating->save();
        }

        return redirect()->back()->with('success', 'Ratings submitted successfully');
    }

    public function calculateRIS($reviewerId)
    {
        // Fetch all ratings where the current user (reviewer) received the rating
        $ratings = ReviewRating::where('reviewer_id', $reviewerId)->get();

        // Calculate average score for each dimension
        $clarityAvg = $ratings->avg('clarity_rating');
        $constructivenessAvg = $ratings->avg('constructiveness_rating');
        $specificityAvg = $ratings->avg('specificity_rating');
        $toneAvg = $ratings->avg('tone_rating');

        // Combine to form the Review Impact Score (RIS)
        $ris = ($clarityAvg + $constructivenessAvg + $specificityAvg + $toneAvg) / 4;

        return $ris;
    }

    public function topReviewers()
    {
        $topReviewers = User::where('role', 'student')
                            ->withCount(['receivedRatings as ris' => function ($query) {
                                $query->select(DB::raw('avg(clarity_rating + constructiveness_rating + specificity_rating + tone_rating) / 4'))
                                    ->from('review_ratings')
                                    ->whereColumn('review_ratings.reviewer_id', 'users.id');
                            }])
                            ->orderBy('ris', 'desc')
                            ->limit(10)
                            ->get();

        //dd($topReviewers); // Debug the topReviewers variable

        return view('reviews.top-reviewer', compact('topReviewers'));
    }
}