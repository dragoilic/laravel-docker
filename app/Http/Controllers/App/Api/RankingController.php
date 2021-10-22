<?php

namespace App\Http\Controllers\App\Api;

use App\Domain\TournamentPlayer as TournamentPlayerEntity;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\DoctrineRankingPlayerTransformer;
use Doctrine\ORM\EntityManager;

class RankingController extends Controller
{
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRankingList() 
    {
        $qb = $this->entityManager->getRepository(TournamentPlayerEntity::class)->createQueryBuilder('p');
        $qb -> join('p.tournament', 't')
            -> join ('p.user', 'u');

        $tournamentPlayers = $qb->getQuery()->getResult();

        return fractal()
            ->collection($tournamentPlayers, new DoctrineRankingPlayerTransformer())
            ->toArray();
    }
    
}
