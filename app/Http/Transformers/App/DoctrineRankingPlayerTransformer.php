<?php
namespace App\Http\Transformers\App;

use App\Domain\TournamentPlayer;
use League\Fractal\TransformerAbstract;

class DoctrineRankingPlayerTransformer extends TransformerAbstract
{
    public function transform(TournamentPlayer $player)
    {
        return [
            "id" => $player->getId(),
            "name" => $player->getUser()->getName(),
            "chips" => $player->getChips(),
            "time_frame" => $player->getTournament()->getTimeFrame(),
            "rank" => $player->getRank() ? :"" ,
        ];
    }
}
