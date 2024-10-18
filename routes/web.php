<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ReviewController;

// a shortcut method to automatically generate all of these routes without writing them manually
// registers the routes needed for user authentication
// Laravel’s built-in controller
// Login (/login), Register (/register), Logout (/logout), Password Reset (/password/reset), Email Verification (/email/verify), etc.
Auth::routes();

// Home page route handled by CourseController@index
Route::get('/', [CourseController::class, 'index'])->middleware('auth')->name('home');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/assessments/{id}', [AssessmentController::class, 'show'])->name('assessments.show');
Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollStudent'])->name('courses.enroll');
Route::post('/assessments/store', [AssessmentController::class, 'store'])->name('assessments.store');
Route::get('assessments/{id}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
Route::post('assessments/{id}', [AssessmentController::class, 'update'])->name('assessments.update');
Route::get('/assessments/{assessment}/students', [AssessmentController::class, 'studentShow'])->name('assessments.student_show');
Route::get('/assessments/{assessment}/teachers', [AssessmentController::class, 'teacherShow'])->name('assessments.teacher_show');
Route::post('/reviews/{assessmentId}/store', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/assessments/{assessment}/students/{student}/reviews', [ReviewController::class, 'showReviews'])->name('students.reviews'); // this is for showing detail asssessment page to check reciewved reviews
Route::get('/assessments/{assessment}/students/{student}/reviews', [ReviewController::class, 'showStudentReviews'])->name('students.reviews');
Route::patch('/scores/update/{assessment}/{student}', [AssessmentController::class, 'updateScore'])->name('scores.update');
Route::post('/courses/upload', [CourseController::class, 'uploadCourse'])->name('courses.upload'); // file uplaod
Route::get('/assessments/{assessment}/assign-reviewers', [ReviewController::class, 'showAssignReviewersForm'])->name('reviews.teacher-assign');
Route::post('/assessments/{assessment}/assign-reviewers', [ReviewController::class, 'assignReviewers'])->name('reviews.teacher-assign');
Route::put('/assessments/{assessment}/students', [ReviewController::class, 'update'])->name('reviews.update');
Route::post('/assessments/{assessment}/students', [ReviewController::class, 'storeReviewRatings'])->name('assessments.student_rate');
Route::get('/reviewers/top', [ReviewController::class, 'topReviewers'])->name('reviews.top-reviewer');



//restrict access to routes based on the role
// Route::group(['middleware' => ['auth', 'teacher']], function () {
//     Route::get('/teacher/courses', [CourseController::class, 'teacherCourses']);
// });