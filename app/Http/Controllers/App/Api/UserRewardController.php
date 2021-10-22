<?php
namespace App\Http\Controllers\App\Api;

use Auth;
use App\Domain\User;
use App\Domain\Reward;
use App\Domain\UserReward as UserRewardEntity;
use App\Mail\UserReward;
use App\Models\Notifications;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\DoctrineUserRewardTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class UserRewardController extends Controller
{

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function post(Request $request)
    {
        $prizeId = $request->prizeid;
        $prizeCost = $request->prizecost;
        $fullname = $request->fullname ?? "";
        $address = $request->address;
        $this->entityManager->beginTransaction();
        $user = $this->entityManager->getRepository(User::class)->find($request->user()->id);
        $user->debitRedeemedCredits($prizeCost);
        $this->entityManager->flush();

        $reward = $this->entityManager->getRepository(Reward::class)->find($prizeId);
        $userReward = new UserRewardEntity($user, $reward, $prizeCost);
        $this->entityManager->persist($userReward);
        $this->entityManager->flush();
        $this->entityManager->commit();
        
        Mail::to($user->getEmail())->send(
            new UserReward($user->getFullname(), $fullname, $prizeCost, $address)
        );

        $notifications = new Notifications();
        $user_id = Auth::user()->id;

        $notifications->fullname = $fullname;
        $notifications->user_id = $user_id;
        $notifications->title = $fullname . " Reward recorded successfully";
        $notifications->subject = "Reward congratulations  email for" . $fullname;
        $notifications->body = "Congratulations 
        You will be receiving a prize worth $ " . $prizeCost . " to the following address.";

        $notifications->save();

        return "User Reward recorded successfully";
    }

    public function get(Request $request) {
        $userId = $request->user()->id;
        $userReward = $this->entityManager->getRepository(UserRewardEntity::class)->findBy(
            array('userId' => $userId) );
        return fractal()
            ->collection($userReward, new DoctrineUserRewardTransformer())
            ->toArray();
    }
}
