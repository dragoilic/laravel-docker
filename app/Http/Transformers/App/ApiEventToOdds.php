<?php

namespace App\Http\Transformers\App;

use App\Domain\ApiEvent;
use App\Domain\ApiEventOdds;
use App\Tournament\Enums\GamePeriod;
use League\Fractal\TransformerAbstract;

class ApiEventToOdds extends TransformerAbstract
{
    protected $defaultIncludes = ["odds"];

    public function transform(ApiEvent $apiEvent)
    {
        return [
            "external_id" => $apiEvent->getApiId(),
            "count" => $apiEvent->getTotalOddsCount()
        ];
    }

    public function includeOdds(ApiEvent $apiEvent)
    {
        /** @var ApiEventOdds[] $odds */
        $odds = $apiEvent->getAllOdds();
        return $this->collection($odds, new ApiEventOddsTransformer());
    }
}
