<?php
namespace App\Http\Controllers\App\Api;

use App\Domain\Reward;
use App\Domain\UserReward;
use App\Reward\RewardStatus;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\DoctrineRewardTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;

class RewardController extends Controller
{

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(Request $request)
    {
        $this->entityManager->beginTransaction();

        /** @var Reward $reward */
        $rewards = $this->entityManager->getRepository(Reward::class)->findBy(array('status' => RewardStatus::ACTIVE()));
        
        return fractal()
            ->collection($rewards, new DoctrineRewardTransformer())
            ->toArray();
    }

    public function userRewards(Request $request)
    {
        $this->entityManager->beginTransaction();

        /** @var Reward $reward */
        $rewards = $this->entityManager->getRepository(UserReward::class)->findBy(array('status' => RewardStatus::ACTIVE()));
        
        return fractal()
            ->collection($rewards, new DoctrineRewardTransformer())
            ->toArray();
    }
}
