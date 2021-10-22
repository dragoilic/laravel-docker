<?php

namespace App\Betting;

use App\Betting\SportEvent\Event;
use App\Betting\SportEvent\League;
use App\Betting\SportEvent\Line;
use App\Betting\SportEvent\LineCollection;
use App\Betting\SportEvent\Offer;
use App\Betting\SportEvent\OfferCollection;
use App\Betting\SportEvent\Result;
use App\Betting\SportEvent\Sport;
use App\Betting\SportEvent\Update;
use App\Betting\SportEvent\UpdateCollection;
use App\Models\ApiEvent;
use App\Tournament\Enums\GamePeriod;
use App\Tournament\Enums\TournamentState;
use Carbon\Carbon;
use Decimal\Decimal;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;

class TestData implements BettingProvider
{
    public const PROVIDER_NAME = "testdata";
    public const PROVIDER_DESCRIPTION = 'Test data';
    private EntityManager $entityManager;
    private array $tags = [
        [Offer::MONEYLINE, Offer::HOME, Offer::FULL_TIME],
        [Offer::MONEYLINE, Offer::AWAY, Offer::FULL_TIME],
        [Offer::SPREAD, Offer::HOME, Offer::FULL_TIME],
        [Offer::SPREAD, Offer::AWAY, Offer::FULL_TIME],
        [Offer::TOTAL, Offer::OVER, Offer::FULL_TIME],
        [Offer::TOTAL, Offer::UNDER, Offer::FULL_TIME],

        /* TODO: Uncomment when we are getting FirstHalf and Second Half lines from Lsports 
        [Offer::MONEYLINE, Offer::HOME, Offer::FIRST_HALF],
        [Offer::MONEYLINE, Offer::AWAY, Offer::FIRST_HALF],
        [Offer::SPREAD, Offer::HOME, Offer::FIRST_HALF],
        [Offer::SPREAD, Offer::AWAY, Offer::FIRST_HALF],
        [Offer::TOTAL, Offer::OVER, Offer::FIRST_HALF],
        [Offer::TOTAL, Offer::UNDER, Offer::FIRST_HALF],
        [Offer::MONEYLINE, Offer::HOME, Offer::SECOND_HALF],
        [Offer::MONEYLINE, Offer::AWAY, Offer::SECOND_HALF],
        [Offer::SPREAD, Offer::HOME, Offer::SECOND_HALF],
        [Offer::SPREAD, Offer::AWAY, Offer::SECOND_HALF],
        [Offer::TOTAL, Offer::OVER, Offer::SECOND_HALF],
        [Offer::TOTAL, Offer::UNDER, Offer::SECOND_HALF],
        */
    ];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEvents(int $page): Pagination
    {
        $page--;
        $perPage = 25;
        $his = explode(',', (new \DateTime())->format('h,i,s'));
        $startId =  + intval(($his[0] * 60 * 60 + $his[1] * 60 + $his[2]) / 15);
        srand(0);

        $results = collect(range(($page * $perPage) + $startId, (($page + 1) * $perPage) + $startId, 1))
            ->map(fn (int $id) => new Event(
                $id,
                (new \DateTime())->add(new \DateInterval('PT' . ($id - $startId) * 15 . 'S')),
                rand(1, 4) * 1000,
                rand(1, 4) * 100,
                'Home team ' . $id,
                'Away team ' . $id,
                static::PROVIDER_NAME,
                null,
                null
            )
        )
        ->all();

        return new Pagination($results, 5760 - $startId, $perPage);
    }

    public function getSports(): array
    {
        return [
            new Sport(1000, 'Laser Tag', self::PROVIDER_NAME),
            new Sport(2000, 'Air Hockey',self::PROVIDER_NAME),
            new Sport(3000, 'VR Dodgeball', self::PROVIDER_NAME),
            new Sport(4000, 'Jousting', self::PROVIDER_NAME),
        ];
    }

    public function getLeagues(): array
    {
        return [
            new League(100, 'NLT', 1000, self::PROVIDER_NAME),
            new League(200, 'NAH', 2000, self::PROVIDER_NAME),
            new League(300, 'NVRDHL', 3000, self::PROVIDER_NAME),
            new League(400, 'NJL', 4000, self::PROVIDER_NAME),
        ];
    }

    public function getUpdates(): UpdateCollection
    {
        /** @var \App\Domain\ApiEvent[]|Collection $apiEvents */
        $qb = $this->entityManager->createQueryBuilder();
        $apiEvents = $qb->select('a')
            ->from(\App\Domain\ApiEvent::class, 'a')
            ->where($qb->expr()->eq('a.provider', '?1'))
            ->andWhere($qb->expr()->notIn('a.timeStatus', '?2'))
            ->getQuery()
            ->execute([1 => static::PROVIDER_NAME, 2 => [TimeStatus::ENDED(), TimeStatus::CANCELED()]]);

        $updates = [];

        foreach ($apiEvents as $apiEvent) {
            srand($apiEvent->getApiId());
            $result = $this->generateResult($apiEvent);

            $total = new Decimal((string) (((int) rand(1, 5)) + .5));
            $spreadHome = new Decimal((string) (((int) rand(-3, 0)) - .5));
            $spreadAway = new Decimal((string) (((int) rand(0, 3)) + .5));

            $lines = [];
            $lineOffers = [];
            $settlements = [];

            if ($result->getTimeStatus()->equals(TimeStatus::ENDED())) {
                $settlements = $this->calculateSettlements($result, $total, $spreadHome, $spreadAway);
            }

            foreach ($this->tags as $tagset) {

                $line = null;
                $lineName = null;
                $lineType = null;
                $linePeriod = GamePeriod::FULL_TIME();
                switch (true) {
                    case in_array(Offer::TOTAL, $tagset):
                        $line = $total;
                        break;
                    case in_array(Offer::SPREAD, $tagset) && in_array(Offer::HOME, $tagset):
                        $line = $spreadHome;
                        break;
                    case in_array(Offer::SPREAD, $tagset) && in_array(Offer::AWAY, $tagset):
                        $line = $spreadAway;
                        break;
                }

                if (in_array(Offer::HOME, $tagset)) {
                    $lineName  = Offer::HOME;
                } else if (in_array(Offer::AWAY, $tagset)) {
                    $lineName  = Offer::AWAY;
                } else if (in_array(Offer::OVER, $tagset)) {
                    $lineName  = Offer::OVER;
                } else if (in_array(Offer::UNDER, $tagset)) {
                    $lineName  = Offer::UNDER;
                }

                if (in_array(Offer::FIRST_HALF, $tagset)) {
                    $linePeriod  = GamePeriod::FIRST_HALF();
                } else if (in_array(Offer::SECOND_HALF, $tagset)) {
                    $linePeriod  = GamePeriod::SECOND_HALF();
                }

                if (in_array(Offer::MONEYLINE, $tagset)) {
                    $lineType  = Offer::MONEYLINE;
                } else if (in_array(Offer::SPREAD, $tagset)) {
                    $lineType  = Offer::SPREAD;
                } else if (in_array(Offer::TOTAL, $tagset)) {
                    $lineType  = Offer::TOTAL;
                }

                $lineId = implode('::', $tagset);
                $lines[] = new Line(
                    $lineId,
                    $linePeriod,
                    $lineName,
                    $lineType,
                    rand(-200, 500),
                    $line,
                    $settlements[$lineId] ?? null
                );


                /* extra lines */
                /*
                if (in_array(Offer::SPREAD, $tagset) && in_array(Offer::HOME, $tagset))  {
                    for ($extraLine = -0.5; $extraLine >= -3.5; $extraLine=($extraLine-1.5)) {
                        if ($extraLine == $line) {
                            continue;
                        }
                        $extraLineId = implode('::', [$lineId, $extraLine]);
                        $lines[] = new Line(
                            $extraLineId,
                            $linePeriod,
                            $lineName,
                            $lineType,
                            rand(-200, 500),
                            new Decimal((string) $extraLine),
                            $settlements[$lineId] ?? null
                        );
                    }
                }

                else if (in_array(Offer::SPREAD, $tagset) && in_array(Offer::AWAY, $tagset))  {
                    for ($extraLine = 0.5; $extraLine <= 3.5; $extraLine=($extraLine+1.5)) {
                        if ($extraLine == $line) {
                            continue;
                        }
                        $extraLineId = implode('::', [$lineId, $extraLine]);
                        $lines[] = new Line(
                            $extraLineId,
                            $linePeriod,
                            $lineName,
                            $lineType,
                            rand(-200, 500),
                            new Decimal((string) $extraLine),
                            $settlements[$lineId] ?? null
                        );
                    }
                }

                else if (in_array(Offer::TOTAL, $tagset) && in_array(Offer::UNDER, $tagset))  {
                    for ($extraLine = 0.5; $extraLine <= 5; $extraLine=($extraLine+1.5)) {
                        if ($extraLine == $line) {
                            continue;
                        }
                        $extraLineId = implode('::', [$lineId, $extraLine]);
                        $lines[] = new Line(
                            $extraLineId,
                            $linePeriod,
                            $lineName,
                            $lineType,
                            rand(-200, 500),
                            new Decimal((string) $extraLine),
                            $settlements[$lineId] ?? null
                        );
                    }
                }

                else if (in_array(Offer::TOTAL, $tagset) && in_array(Offer::OVER, $tagset))  {

                    for ($extraLine = 0.5; $extraLine <= 5; $extraLine=($extraLine+1.5)) {
                        if ($extraLine == $line) {
                            continue;
                        }
                        $extraLineId = implode('::', [$lineId, $extraLine]);
                        $lines[] = new Line(
                            $extraLineId,
                            $linePeriod,
                            $lineName,
                            $lineType,
                            rand(-200, 500),
                            new Decimal((string) $extraLine),
                            $settlements[$lineId] ?? null
                        );
                    }
                }
            */

                $lineOffers[] = new Offer($lineId, ...$tagset);
            }

            $updates[] = new Update(
                $apiEvent->getApiId(),
                $result,
                new LineCollection(...$lines),
                new OfferCollection(...$lineOffers)
            );
        }

        return new UpdateCollection(self::PROVIDER_NAME, ...$updates);
    }

    private function generateResult(\App\Domain\ApiEvent $apiEvent): Result
    {
        $timeStatus = TimeStatus::NOT_STARTED();
        $home = $away = null;
        $finalHome = (int) rand(0, 5);
        $finalAway = (int) rand(0, 5);

        if (Carbon::now() >= $apiEvent->getStartsAt()) {
            $timeStatus = TimeStatus::IN_PLAY();
            $home = (int) $finalHome / 2;
            $away = (int) $finalAway / 2;
        }

        if (Carbon::now()->subMinutes(10) >= $apiEvent->getStartsAt()) {
            $timeStatus = TimeStatus::ENDED();
            $home = $finalHome;
            $away = $finalAway;
        }

        return new Result(
            $apiEvent->getApiId(),
            static::PROVIDER_NAME,
            $timeStatus,
            (new Carbon($apiEvent->getStartsAt()))->toString(),
            $home,
            $away,
            null,
            null
        );
    }

    private function calculateSettlements(Result $result, Decimal $total, Decimal $spreadHome, Decimal $spreadAway): array
    {
        $settlements = [];

        if ($result->getHome() > $result->getAway()) {
            $settlements[implode('::', $this->tags[0])] = Settlement::WON();
            $settlements[implode('::', $this->tags[1])] = Settlement::LOST();
        } elseif ($result->getHome() < $result->getAway()) {
            $settlements[implode('::', $this->tags[0])] = Settlement::LOST();
            $settlements[implode('::', $this->tags[1])] = Settlement::WON();
        } else {
            $settlements[implode('::', $this->tags[0])] = Settlement::PUSH();
            $settlements[implode('::', $this->tags[1])] = Settlement::PUSH();
        }

        $settlements[implode('::', $this->tags[2])] = $this->getSettlementHomeSpread($result->getHome(), $result->getAway(), $spreadHome );
        $settlements[implode('::', $this->tags[3])] = $this->getSettlementAwaySpread($result->getHome(), $result->getAway(), $spreadAway );
        
        /* TODO: Add Settlements for Extra Lines */
        $totalResult = $result->getHome() + $result->getAway();
        if ($totalResult > $total) {
            $settlements[implode('::', $this->tags[4])] = Settlement::WON();
            $settlements[implode('::', $this->tags[5])] = Settlement::LOST();
        } elseif ($totalResult < $total) {
            $settlements[implode('::', $this->tags[4])] = Settlement::LOST();
            $settlements[implode('::', $this->tags[5])] = Settlement::WON();
        } else {
            $settlements[implode('::', $this->tags[4])] = Settlement::PUSH();
            $settlements[implode('::', $this->tags[5])] = Settlement::PUSH();
        }

        return $settlements;
    }

    private function getSettlementHomeSpread($homeResult, $awayResult, $spread)
    {
        $spreadHomeResult = $homeResult + $spread - $awayResult;
        if ($spreadHomeResult > 0) {
            return Settlement::WON();
        } elseif ($spreadHomeResult < 0) {
            return Settlement::LOST();
        } else {
            return Settlement::PUSH();
        }
    }

    private function getSettlementAwaySpread($homeResult, $awayResult, $spread)
    {
        $spreadAwayResult = $awayResult + $spread - $homeResult;
        if ($spreadAwayResult > 0) {
            return Settlement::WON();
        } elseif ($spreadAwayResult < 0) {
            return Settlement::LOST();
        } else {
            return Settlement::PUSH();
        }
    }
}
