<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $user = Auth::user();
            $token = hash('sha256', Str::random(80));

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Incorrect username or password. Please try again.'], 401);
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        return $this->success(message: 'Logout successful');
    }
}
