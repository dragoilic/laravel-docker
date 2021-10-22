<?php
namespace App\Http\Controllers\App\Api;

use App\Domain\UserCredits;
use App\Domain\Tournament as TournamentEntity;
use App\Domain\TournamentPayout as TournamentPayoutEntity;
use App\Domain\TournamentPlayer as TournamentPlayerEntity;
use App\Domain\TournamentBet as TournamentBetEntity;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\DoctrineUserCreditsTransformer;
use App\Http\Transformers\App\DoctrineUserBetTransformer;
use App\Tournament\Enums\TournamentState;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;

class MeStatisticsController extends Controller
{

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(Request $request)
    {
        $userId = $request->user()->id;
        $totalChipsWon = 0;
        $largestTournamentTakeDown = 0;
        $bestTournamentFinish = 0;
        $largestBetWon = 0;
        $inMoneyRate = 0;
        $bestTournament_1_name = "";
        $bestTournament_2_name = "";
        $bestTournament_3_name = "";
        $bestTournament_1_rank = 0;
        $bestTournament_2_rank = 0;
        $bestTournament_3_rank = 0;

        $qb = $this->entityManager->getRepository(TournamentPlayerEntity::class)->createQueryBuilder('p');
        $qb -> join('p.tournament', 't')
            -> join('p.user', 'u')
            -> where('u.id = :userId')
            -> andWhere('t.state IN (:t_state)')
            -> orderBy('p.rank' , "ASC" )
            -> setParameter('userId', $userId)
            -> setParameter('t_state', [TournamentState::COMPLETED(), TournamentState::CANCELED()]);
        $tournamentPlayers = $qb->getQuery()->getResult();
        $totalTournamentsPlayed = count($tournamentPlayers);

        foreach ($tournamentPlayers as $player) {
            $playerChips = $player->getChips();
            $totalChipsWon += $playerChips ;
            $bets  = $player->getSortedBetsByWin();
            if (sizeof($bets) > 0) {
                $bet = $bets->first();
                $chipsWon = $bet->getActualChipsWon();
                if ($chipsWon > $largestBetWon) {
                    $largestBetWon = $chipsWon;
                }
            }
        }
        if ($totalTournamentsPlayed > 0) {
            $bestTournamentFinish = $tournamentPlayers[0]->getRank();
            if ($bestTournamentFinish == 1) {
                $playersCount = sizeof($tournamentPlayers[0]->getTournament()->getPlayers());
                $largestTournamentTakeDown = $playersCount;
            }
            $bestTournament_1_name = $tournamentPlayers[0]->getTournament()->getName();
            $bestTournament_1_rank = $tournamentPlayers[0]->getRank();
            if ($totalTournamentsPlayed > 1) {
                $bestTournament_2_name = $tournamentPlayers[1]->getTournament()->getName();
                $bestTournament_2_rank = $tournamentPlayers[1]->getRank();
                if ($totalTournamentsPlayed > 2) {
                    $bestTournament_3_name = $tournamentPlayers[2]->getTournament()->getName();
                    $bestTournament_3_rank = $tournamentPlayers[2]->getRank();
                }
            }
        }

        $qb = $this->entityManager->getRepository(TournamentPayoutEntity::class)->createQueryBuilder('p');
        $qb -> where ('p.userId = :userId')
            -> orderBy('p.rank' , "ASC" )
            -> setParameter('userId', $userId);
        $tournamentPayouts = $qb->getQuery()->getResult();
        $totalPayouts = count($tournamentPayouts);
        if ($totalPayouts > 0) {
            $inMoneyRate = floor($totalPayouts * 100 / $totalTournamentsPlayed);
        }

        $statistics = [
                "totalChipsWon" => $totalChipsWon,
                "largestTournamentTakeDown" => $largestTournamentTakeDown,
                "bestTournamentFinish" => $bestTournamentFinish,
                "largestBetWon" => $largestBetWon,
                "inMoneyRate" => $inMoneyRate,
                "firstBestTournament" => $bestTournament_1_name,
                "firstBestTournamentRank" => $bestTournament_1_rank,
                "secondBestTournament" => $bestTournament_2_name,
                "secondBestTournamentRank" => $bestTournament_2_rank,
                "thirdBestTournament" => $bestTournament_3_name,
                "thirdBestTournamentRank" => $bestTournament_3_rank,
        ];
        return $statistics;
    }

    public function getLegendaryWins(Request $request)
    {
        $user = $request->user();
        $legendaryBets = [];
        
        array_push($legendaryBets, ...$this->queryLegendaryBets());
        $userBets = $this->queryLegendaryBets($user->id);
        if (count($userBets) > 0) {
            $legendaryBets[] = $userBets[0];
        }

        return fractal()
            ->collection($legendaryBets, new DoctrineUserBetTransformer())
            ->toArray();

    }
    private function queryLegendaryBets(?int $userId=-1) {
        $qb = $this->entityManager->getRepository(TournamentBetEntity::class)->createQueryBuilder('b');
        if ($userId == -1) {
            $qb -> join('b.tournament', 't')
                -> where('t.state IN (:t_state)')
                -> setParameter('t_state', [TournamentState::COMPLETED(), TournamentState::CANCELED()]);
        } else {
            $qb -> join('b.tournament', 't')
                -> join('b.tournamentPlayer', 'p')
                -> join('p.user', 'u')
                -> where('u.id = :userId')
                -> andWhere('t.state IN (:t_state)')
                -> setParameter('userId', $userId)
                -> setParameter('t_state', [TournamentState::COMPLETED(), TournamentState::CANCELED()]);
        }
        $bets = $qb->getQuery()->getResult();
        usort($bets, fn (TournamentBetEntity $a, TournamentBetEntity $b) => $b->getActualChipsWon() <=> $a->getActualChipsWon());
        return array_slice($bets, 0, 3);
    }

    public function creditsHistory(Request $request) {
        $userId = $request->user()->id;
        $userCredits = $this->entityManager->getRepository(UserCredits::class)->findBy(
            array('userId' => $userId) );
        return fractal()
            ->collection($userCredits, new DoctrineUserCreditsTransformer())
            ->toArray();
    }
}
