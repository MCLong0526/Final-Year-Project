<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens; // Use HasApiTokens trait

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function authenticate(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->validated();

        if (Auth::attempt($credentials)) {
            $loginRequest->session()->regenerate();

            $user = User::with('roles')->find(Auth::id());
            $token = hash('sha256', Str::random(80));

            //create a new token
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Incorrect username or password.'], 401);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login')->with('success', 'Logout successful');
    }

    public function getCurrentLoggedUser()
    {
        $user = User::with('roles')->find(Auth::id());

        // Add the avatar path to the user data
        $user['avatar'] = asset('storage/'.$user->avatar);

        return $this->success(
            data: $user,
            message: 'User found'
        );
    }

    public function getUserByToken(Request $request)
    {
        $user = User::with('roles')->where('api_token', $request->bearerToken())->first();

        // Add the avatar path to the user data
        $user['avatar'] = asset('storage/'.$user->avatar);

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }

    public function generatePassword()
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $specialChars = '!@#$%^&*()-_=+[{]}|;:,<.>/?';
        $numbers = '0123456789';

        $allChars = $uppercase.$lowercase.$specialChars.$numbers;

        $password = '';
        $password .= substr(str_shuffle($uppercase), 0, 1); // at least one uppercase letter
        $password .= substr(str_shuffle($lowercase), 0, 1); // at least one lowercase letter
        $password .= substr(str_shuffle($specialChars), 0, 1); // at least one special character
        $password .= substr(str_shuffle($numbers), 0, 1); // at least one digit

        $passwordLength = 8;
        //must be at least 8 characters
        for ($i = 0; $i < $passwordLength - 4; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    public function forgotPassword(Request $request)
    {
        // Validate the request->email, check if the email exists in the database
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a new password, the new password style is at least one uppercase, lowercase, special character and digit with min 8 chars
        $newPassword = $this->generatePassword();

        $hashedPassword = Hash::make($newPassword);

        // Update user's password in the database
        $user->password = $hashedPassword;
        $user->save();

        // Send the new password to the user email
        try {
            Mail::to($user->email)->send(new ForgotPasswordMail($newPassword));

        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => 'Failed to send email'], 500);
        }

        // If everything is successful, return success response
        return response()->json(['message' => 'New password sent to your email']);
    }
}
