<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\EmailVerification;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function forgotPassword(Request $request)
    {
        if ($request->has('staff_email_address')) {
            $request->validate([
                'staff_email_address' => ['required', 'email'],
            ]);

            $staff = Staff::whereHas('information', function ($query) use ($request) {
                $query->where('email_address', $request->staff_email_address);
            })
                ->first();

            if (!$staff) {
                throw ValidationException::withMessages([
                    'message' => 'The email address is invalid.',
                ]);
            }

            $user = User::where('id', $staff->user_id)
                ->where('is_default', 1)
                ->first();

            if ($user) {
                throw ValidationException::withMessages([
                    'message' => 'Your email address is not allowed to forgot password at this time.',
                ]);
            }

            $otp = rand(100000, 999999);

            $this->emailVerification($staff->information, $otp);

            Mail::to($request->staff_email_address)->send(new OtpMail($otp));
        } elseif ($request->has('student_email_address')) {
            $request->validate([
                'student_email_address' => ['required', 'email'],
            ]);

            $student = Student::whereHas('information', function ($query) use ($request) {
                $query->where('email_address', $request->student_email_address);
            })
                ->first();

            if (!$student) {
                throw ValidationException::withMessages([
                    'message' => 'The email address is invalid.',
                ]);
            }

            $user = User::where('id', $student->user_id)
                ->where('is_default', 1)
                ->first();

            if ($user) {
                throw ValidationException::withMessages([
                    'message' => 'Your email address is not allowed to forgot password at this time.',
                ]);
            }

            $otp = rand(100000, 999999);

            $this->emailVerification($student->information, $otp);

            Mail::to($request->student_email_address)->send(new OtpMail($otp));
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

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required'],
        ]);

        $verification = EmailVerification::whereHas('information', function ($query) use ($request) {
            $query->where('email_address', $request->email_address);
        })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($verification->otp !== $request->otp) {
            throw ValidationException::withMessages([
                'message' => 'OTP is invalid.',
            ]);
        }

        if (Carbon::now()->isAfter($verification->expired_at)) {
            throw ValidationException::withMessages([
                'message' => 'OTP expired.',
            ]);
        }

        EmailVerification::where('info_id', $verification->information->id)
            ->delete();
    }

    public function createNewPassword(Request $request)
    {
        if ($request->has('staff_email_address')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $staff = Staff::whereHas('information', callback: function ($query) use ($request) {
                $query->where('email_address', $request->staff_email_address);
            })
                ->first();

            User::where('id', $staff->user_id)
                ->update([
                    'password' => bcrypt($request->password),
                ]);
        } elseif ($request->has('student_email_address')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $student = Student::whereHas('information', function ($query) use ($request) {
                $query->where('email_address', $request->student_email_address);
            })
                ->first();

            User::where('id', $student->user_id)
                ->update([
                    'password' => bcrypt($request->password),
                ]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();
    }

    public function emailVerification($info, $otp)
    {
        EmailVerification::create([
            'info_id' => $info->id,
            'otp' => $otp,
            'expired_at' => now()->addMinutes(3),
        ]);
    }
}
