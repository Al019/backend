<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function user(Request $request)
    {
        $user = $request->user();

        $data = null;

        if ($user->role === 'admin' || $user->role === 'staff' || $user->role === 'cashier') {
            $data = User::with('staff.information')
                ->find($user->id);
        } elseif ($user->role === 'student') {
            $data = User::with('student.information')
                ->find($user->id);
        }

        return response()->json($data);
    }
}
