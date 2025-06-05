<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profileImage = asset('Images/defaultpfp.png');

        return view('profile', [
            'user' => $user,
            'profileImage' => $profileImage,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|max:30|confirmed',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        return response()->json(['user' => $user]);
    }
}