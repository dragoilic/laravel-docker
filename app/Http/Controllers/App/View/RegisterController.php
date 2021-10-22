<?php
namespace App\Http\Controllers\App\View;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/lobby';

    public function showRegistrationForm(Request $request)
    {
        if ($request->has('ref')) {
            session(['referrer' => $request->query('ref')]);
        }

        return view('auth.register');
    }

    public function showReferralLinkForm(Request $request)
    {
        return view('auth.referralLink');
    }
}