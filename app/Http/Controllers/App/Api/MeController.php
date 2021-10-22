<?php
namespace App\Http\Controllers\App\Api;

use App\Domain\User;
use App\Mail\Referral;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\MeTransformer;
use App\Models\Notifications;
use Doctrine\ORM\EntityManager;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use Socialite;
use Auth;
use Exception;

class MeController extends Controller
{
    public function get(Request $request)
    {
        return fractal()
            ->item($request->user(), new MeTransformer())
            ->toArray();
    }

    public function forgotPassword(Request $request) {
        $credentials = $request->validate([
            'email' => 'required | email'
        ]);
        
        Password::sendResetLink($credentials);
        
        return 'Reset password link sent on your email id.';
    }

    public function ReferUser(Request $request) {
        $credentials = $request->validate([
            'email' => 'required | email'
        ]);

        $user = $request->user();
        Mail::to($request->get('email'))->send(
            new Referral($request->get('email'), $user->getReferralLinkAttribute())
        );

        $user_id = Auth::user()->id;

        $notifications = new Notifications();
        $email = $request->get('email');
        $notifications->user_id = $user_id;
        $notifications->fullname = $email;
        $notifications->title = "User Referrence link sent to " . $email;
        $notifications->subject = "Referral email for" . $email;
        $notifications->body = "Come Join Pick Wins and compete in DFS style tournaments for FREE PRIZES
        Click below register";

        $notifications->save();
        
        return 'User Referrence link sent on your email id.';
    }

    public function getReferralLink(Request $request) {

        $user = $request->user();
        $referlLink = env('APP_URL'). "/referralLink?ref=". $user->name;
        return $referlLink;
    }

    public function resetPassword(Request $request) {
        $credentials = $request->validate([
            'email' => 'required | email',
            'token' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
       
        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["message" => "Invalid token provided"], 400);
            return response()->json(
                ['message'=> 'The given data was invalid', 'errors' => ['token' => 'Invalid token']],
                400
            );
        }

        if ($reset_password_status != Password::PASSWORD_RESET) {
            return response()->json(["message" => "Reset Password failed. The given data was invalid"], 400);
        }

        return 'Reset password succeded';
    }

    public function handleUserInfoChanges(Request $request, EntityManager $entityManager, HashManager $hashManager) {
        $userId = $request->user()->id;
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['numeric', 'digits_between:10,11', 'nullable'],
        ]);

        $entityManager->beginTransaction();
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($userId);
        $user->updateProfileFields(
            $request->get('firstname'),
            $request->get('lastname'),
            $request->get('phone')
        );

        $entityManager->flush();
        $entityManager->commit();

        return "User Profile Updated Successfully";
    }

    public function changePassword(Request $request, EntityManager $entityManager, HashManager $hashManager)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $entityManager->beginTransaction();
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($request->user()->id);

        if (!$hashManager->check($request->get('current_password'), $user->getPassword())) {
            return response()->json(
                ['message'=> 'The given data was invalid', 'errors' => ['current_password' => 'Invalid password']],
                403
            );
        }

        $user->updatePassword($hashManager->make($request->get('password')));

        $entityManager->flush();
        $entityManager->commit();

        return '';
    }

    public function changeEmail(Request $request, EntityManager $entityManager, HashManager $hashManager)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $entityManager->beginTransaction();
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($request->user()->id);

        if (!$hashManager->check($request->get('current_password'), $user->getPassword())) {
            return response()->json(
                ['message'=> 'The given data was invalid', 'errors' => ['current_password' => 'Invalid password']],
                403
            );
        }

        $user->updateEmail($request->get('email'));

        $entityManager->flush();
        $entityManager->commit();

        return '';
    }

    public function changeProfile(Request $request, EntityManager $entityManager)
    {
        $userId = $request->user()->id;
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
        ]);

        $entityManager->beginTransaction();
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($userId);

        $user->updateProfile(
            $request->get('name'),
            $request->get('firstname'),
            $request->get('lastname')
        );

        $entityManager->flush();
        $entityManager->commit();

        return '';
    }
}
