<?php
namespace App\Http\Controllers\App\Api;

use App\Domain\TournamentPlayer as TournamentPlayerEntity;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\DoctrineTournamentPlayerTransformer;
use App\Http\Transformers\App\MeBetTransformer;
use App\Tournament\Enums\TournamentState;
use Illuminate\Http\Request;
use Doctrine\ORM\EntityManager;

class PlayerBetsController extends Controller
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(Request $request)
    {
        $user = $request->user();
        $qb = $this->entityManager->getRepository(TournamentPlayerEntity::class)->createQueryBuilder('p');
        $qb -> join('p.tournament', 't')
            -> join('p.user', 'u')
            -> where('u.id = :userId')
            -> andWhere('t.state IN (:t_state)')
            -> setParameter('userId', $user->id)
            -> setParameter('t_state', [TournamentState::COMPLETED(), TournamentState::CANCELED()]);
        $tournamentPlayers = $qb->getQuery()->getResult();
        return fractal()
            ->collection($tournamentPlayers, new DoctrineTournamentPlayerTransformer())
            ->toArray();
    }
}
