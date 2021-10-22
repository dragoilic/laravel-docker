<?php
namespace App\Http\Transformers\App;

use App\Domain\TournamentBet;
use League\Fractal\TransformerAbstract;

class DoctrineUserBetTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ["events"];

    public function transform(TournamentBet $bet)
    {
        $tournament = $bet->getTournament();
        return [
            "id" => $bet->getId(),
            "user_name" => $bet->getTournamentPlayer()->getUser()->getName(),
            "chips_wager" => $bet->getChipsWager(),
            "chips_win" => $bet->getChipsWon(),
            "tournament_id" => $tournament->getId(),
            "status" => $bet->getStatus(),
        ];
    }

    public function includeEvents(TournamentBet $bet)
    {
        return $this->collection($bet->getEvents(), new DoctrineTournamentBetEventTransformer());
    }
}
