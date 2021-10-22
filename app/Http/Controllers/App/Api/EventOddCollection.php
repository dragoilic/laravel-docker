<?php
namespace App\Http\Controllers\App\Api;

use App\Betting\TimeStatus;
use App\Domain\ApiEvent;
use App\Domain\ApiEventOdds;
use App\Http\Controllers\Controller;
use App\Http\Transformers\App\ApiEventOddsTransformer;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class EventOddCollection extends Controller
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get($eventExternalId)
    {
        $entities = $this->entityManager->getRepository(ApiEvent::class)->matching(
            Criteria::create()->where(Criteria::expr()->eq('apiId', $eventExternalId))->andWhere(Criteria::expr()->in('timeStatus', [TimeStatus::NOT_STARTED(), TimeStatus::IN_PLAY()]))
        );
        $event = $entities->first();
        $eventOdds = [];
        if ($event) {
            $eventOdds = $event->getOddsAllLines();
            $eventOdds = $eventOdds->toArray();
            usort($eventOdds, array($this, "sortEventOdds"));
        }
        
        return fractal()
            ->collection($eventOdds, new ApiEventOddsTransformer())
            ->toArray();
    }

    public function sortEventOdds(ApiEventOdds $a, ApiEventOdds $b)  { 
        $periodOrder = ["100","1","2"];
        $betTypeOrder = ["moneyline_away", "moneyline_home", "spread_away", "spread_home", "total_under", "total_over" ];
        if ($a->getPeriod() != $b->getPeriod())
        {
            return array_search($a->getPeriod(), $periodOrder) <=> array_search($b->getPeriod(), $periodOrder);
        }
        if ($a->getHandicap() && $b->getHandicap() && $a->getHandicap()->abs() != $b->getHandicap()->abs()) 
        {
            return $a->getHandicap()->abs() <=> $b->getHandicap()->abs();
        }
        if ($a->getBetType() != $b->getBetType())
        {
            return array_search($a->getBetType(), $betTypeOrder) <=> array_search($b->getBetType(), $betTypeOrder);
        }
    }
}
