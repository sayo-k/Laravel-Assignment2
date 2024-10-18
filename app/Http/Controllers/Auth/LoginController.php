<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    // Override the method to use 's_number' instead of email
    public function username()
    {
        return 's_number';
    }

    public function login(Request $request)
    {
        // Step 1: Validate the request inputs
        $this->validate($request, [
            's_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Step 2: Retrieve the user by their student number
        $user = User::where($this->username(), $request->s_number)->first();

        // Step 3: Check if the user exists
        if (!$user) {
            // If the user is not found, log and return the specific error message
            Log::warning('User not found for s_number: ' . $request->s_number);
            return back()->withErrors([
                'login_error' => 'Invalid student number.',
            ])->withInput();
        }

        // Step 4: If user exists, check if the password matches
        $passwordMatches = Hash::check($request->password, $user->password);

        if (!$passwordMatches) {
            // If the password doesn't match, log and return the specific error message
            Log::warning('Password mismatch for user: ' . $request->s_number);
            return back()->withErrors([
                'login_error' => 'Invalid password.',
            ])->withInput();
        }

        // Step 5: If both student number and password are correct, log the user in
        Log::info('User successfully logged in: ' . $request->s_number);
        Auth::login($user);

        return redirect()->intended($this->redirectTo); // Redirect to intended URL
    }
}
