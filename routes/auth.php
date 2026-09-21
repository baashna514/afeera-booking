<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// Guest Middleware (Sirf non-logged in users ke liye)
Route::middleware('guest')->group(function () {

    // Register Routes
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('register', function () {
        $attributes = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'in:super_admin,owner,user'],
        ]);

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'role' => $attributes['role'] ?? 'user',
        ]);

        event(new Registered($user));

        auth()->login($user);

        if ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    });

    // Login Routes
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('login', function () {
        $attributes = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($attributes)) {
            throw ValidationException::withMessages([
                'email' => 'Sorry, those credentials do not match.',
            ]);
        }

        request()->session()->regenerate();

        // Check role and redirect accordingly
        if (auth()->user()->role === 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif (auth()->user()->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    });
});

// Authenticated Routes (Logged in users ke liye)
Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
