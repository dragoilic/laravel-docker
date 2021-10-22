<?php

namespace App\Http\Controllers\App\Api;

use App\Domain\UserCredits;
use App\Domain\User as UserEntity;
use App\Http\Controllers\Controller;
use App\Mail\Welcome;
use App\Mail\Withdrawal;
use App\Models\User;
use App\Models\Notifications;
use App\Models\Config;
use Doctrine\ORM\EntityManager;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function post(Request $request, AuthManager $authManager)
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:18', 'unique:users'],
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'dob' => ['required', 'date', 'before_or_equal:-18 years'],
                'country' => ['string', 'max:3', 'nullable'],
                'phone' => ['numeric', 'digits_between:10,11', 'nullable'],
            ],
            ['dob.before_or_equal' => 'You must be over 18 years old to register an account']
        );

        $user = $this->create($request->all());
        event(new Registered($user));

       $authManager->guard()->login($user);

       Mail::to($user->email)->send(
           new Welcome($user->firstname . ' ' . $user->lastname)
       );

        $notifications = new Notifications();
        $fullname = $request["firstname"] . " " . $request["lastname"];
        $notifications->user_id = $user->id;
        $notifications->fullname = $fullname;
        $notifications->title = $fullname . " signed up to our platform";
        $notifications->subject = "Welcom email for " . $fullname;
        $notifications->body = "Welcome to your LegendsBet account. Win cash prizes!
        Jump right in and experience an all new tournament style
        format which will keep you on the edge of your seat.
        Do you have what it takes to be a legend?";

        $notifications->save();
        return new Response('', Response::HTTP_CREATED);
    }

    private function create(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->date_of_birth = $data['dob'];
        $user->is_bot = 0;
        $user->balance = 0;
        $user->country_code = $data['country'];
        $user->phone = $data['phone'];

        if(isset($data['referrer_name']) && trim($data['referrer_name']) !== '') {
            $referrer = User::whereName($data['referrer_name'])->first();
            $user->referrer_id = $referrer ? $referrer->id : null;
        }

        $user->save();

        $this->addCredits($user);
        return $user;
    }

    private function getCreditsOnSignup(): int
    {
        //@Todo replace with doctrine model once refactoring is done
        $config = Config::first();
        return $config->config['credits']['registration'];
    }

    private function getCreditsOnReferral(): int
    {
        //@Todo replace with doctrine model once refactoring is done
        $config = Config::first();
        return $config->config['credits']['referral'];
    }

    private function addCredits($userModel) {

        $creditsOnRegister = $this->getCreditsOnSignup();
        $user = $this->entityManager->getRepository(UserEntity::class)->find($userModel->id);
        $user->creditRegistrationBonus($creditsOnRegister);
        $this->entityManager->persist($user);

        $userCredit = new UserCredits($user, $creditsOnRegister, "registration");
        $this->entityManager->persist($userCredit);

        $referrerId = $userModel->referrer_id;
        if ($referrerId != null) {
            $creditsOnReferral= $this->getCreditsOnReferral();
            $referrer = $this->entityManager->getRepository(UserEntity::class)->find($referrerId);
            $referrerCredits = new UserCredits($referrer, $creditsOnReferral, "referral");
            $this->entityManager->persist($referrerCredits);

            $referrer->creditReferralBonus($creditsOnReferral);
            $this->entityManager->persist($referrer);
        }
        $this->entityManager->flush();
    }

    /*
    protected function registered(Request $request, $user)
    {
        if ($user->referrer !== null) {
            Notification::send($user->referrer, new ReferrerBonus($user));
        }

        return redirect($this->redirectPath());
    }
    */
}
