<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Mail\ForgotPasswordMail;
use App\Mail\VerificationCodeMail;
use App\Models\Role;
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

        return response()->json(['error' => 'Incorrect email or password.'], 401);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

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

    public function verifyCode(Request $request)
    {
        // Validate the request->email, check if the email exists in the database
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a new verification code
        $verificationCode = rand(100000, 999999);

        // Update user's verification code in the database
        $user->verification_code = $verificationCode;
        $user->save();

        // Send the verification code to the user email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => 'Failed to send email'], 500);
        }

        // If everything is successful, return success response
        return response()->json(['message' => 'Verification code sent to your email']);
    }

    public function checkVerificationCode(Request $request)
    {
        // Validate the request->email, check if the email exists in the database
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the verification code is correct
        if ($user->verification_code != $request->verification_code) {
            return response()->json(['message' => 'Verification code is incorrect'], 400);
        }

        // If everything is successful, return success response
        return response()->json(['message' => 'Verification code is correct']);
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

    public function checkEmailExists(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $emailExists = User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $emailExists,
        ]);
    }

    public function checkUsernameExists(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);

        $usernameExists = User::where('username', $request->username)->exists();

        return response()->json([
            'exists' => $usernameExists,
        ]);
    }

    public function register(UserRequest $request)
    {
        // Set default avatar path
        $defaultAvatarPath = 'images/avatars/avatar-1.png';

        $data = $request->validated();

        // Set default avatar path
        $data['avatar'] = $defaultAvatarPath;

        $user = User::create($data);

        // attach role_id = 2 and 3 if the request isSeller is true
        if ($request->isSeller) {
            $user->roles()->attach(Role::whereIn('role_id', [2, 3])->get());
        } else {
            $user->roles()->attach(Role::where('role_id', 2)->first());
        }

        return $this->success(data: $user, message: 'User registered successfully');
    }

    public function registerSeller()
    {
        //save auth user as seller, which is 3
        $user = User::find(Auth::id());

        $user->roles()->attach(Role::where('role_id', 3)->first());

        return $this->success(data: $user, message: 'User registered as seller successfully');
    }
}
