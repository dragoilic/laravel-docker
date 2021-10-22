<?php
namespace App\Http\Controllers\App\View;

use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    public function reset_password()
    {
        return view('auth.reset_password');
    }
}