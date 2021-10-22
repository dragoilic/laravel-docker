<?php

namespace App\Http\Transformers\App;

use App\Domain\ApiEventOdds;
use League\Fractal\TransformerAbstract;

class ApiEventOddsTransformer extends TransformerAbstract
{
    public function transform(ApiEventOdds $apiEventOdds)
    {
        return [
            "oddId" => $apiEventOdds->getId(),
            "period" => $apiEventOdds->getPeriod(),
            "odd" => $apiEventOdds->getOdds(),
            "betType" => $apiEventOdds->getBetType(),
            "handicap" => $apiEventOdds->getHandicap(),
        ];
    }
}
