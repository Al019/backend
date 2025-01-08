<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->has('staff_email_address')) {
            $user = User::whereHas('staff.information', function ($query) use ($request) {
                $query->where('email_address', $request->staff_email_address);
            })
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'message' => 'The provided credentials are incorrect.',
                ]);
            }

            return response()->json([
                'token' => $user->createToken('token')->plainTextToken
            ]);
        } elseif ($request->has('student_email_address')) {
            $user = User::whereHas('student.information', function ($query) use ($request) {
                $query->where('email_address', $request->student_email_address);
            })
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'message' => 'The provided credentials are incorrect.',
                ]);
            }

            return response()->json([
                'token' => $user->createToken('token')->plainTextToken
            ]);
        } elseif ($request->has('id_number')) {
            $user = User::whereHas('student', function ($query) use ($request) {
                $query->where('id_number', $request->id_number);
            })
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'message' => 'The provided credentials are incorrect.',
                ]);
            }

            return response()->json([
                'token' => $user->createToken('token')->plainTextToken
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $user_id = $request->user()->id;

        $user = User::find($user_id);

        if ($user->is_default === 1) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
                'is_default' => 0
            ]);
        } else {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'message' => 'The current password is incorrect.',
                ]);
            }

            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();
    }
}
