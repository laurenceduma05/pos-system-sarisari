<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show secret admin registration form
     */
    public function showRegisterForm()
    {
        return view('admin.secret-register');
    }

    /**
     * Secret admin registration route
     * Not visible on login page - only accessible via direct URL
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstName'  => 'required|string|max:255',
            'lastName'   => 'required|string|max:255',
            'sex'        => 'required|in:male,female,other',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'       => $validated['firstName'] . ' ' . $validated['lastName'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => RoleType::ADMIN,
        ]);

        return response()->json([
            'message' => 'Admin user created successfully',
        ], 201);
    }
}

