<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function mainDashboard()
    {
        return view('admin.index');
    }

    public function changePassword()
    {
        return view('admin.pages.change_password');
    }

    public function storeNewPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            $message = [
                'message' => 'Current password is incorrect',
                'alert-type' => 'error'
            ];
            return back()->with($message);
        }
        $user = User::find(Auth::id());
        $user->password = Hash::make($request->password);
        $message = [
            'message' => 'Password changed successfully',
            'alert-type' => 'success'
        ];
        $user->save();
        return back()->with($message);
    }

    public function editProfile()
    {
        $user = User::find(Auth::id());
        return view('admin.pages.edit_profile', compact('user'));
    }

    public function storeUpdateProfile(Request $request)
    {
        $user = User::find(Auth::id());
        $user->name = $request->name ? $request->name : $user->name;
        $message = [
            'message' => 'Profile updated successfully',
            'alert-type' => 'success'
        ];
        $user->save();
        return back()->with($message);
    }
}
