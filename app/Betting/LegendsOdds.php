<?php

namespace App\Betting;

use App\Betting\SportEvent\Event;
use App\Betting\SportEvent\League;
use App\Betting\SportEvent\LineCollection;
use App\Betting\SportEvent\Offer;
use App\Betting\SportEvent\Line;
use App\Betting\SportEvent\OfferCollection;
use App\Betting\SportEvent\Result;
use App\Betting\SportEvent\Sport;
use App\Betting\SportEvent\Update;
use App\Betting\SportEvent\UpdateCollection;
use App\Domain\Odds;
use Decimal\Decimal;

class LegendsOdds implements BettingProvider
{
    public const PROVIDER_NAME = "legends-odds";
    public const PROVIDER_DESCRIPTION = 'Legends Odds';

    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getSports(): array
    {
        return [
            //new Sport('6046', 'Football', self::PROVIDER_NAME),
            //new Sport('54094', 'Tennis', self::PROVIDER_NAME),
            //new Sport('530129', 'Hockey', self::PROVIDER_NAME),
            new Sport('131506', 'American Football', self::PROVIDER_NAME),
            new Sport('154914', 'Baseball', self::PROVIDER_NAME),
            new Sport('48242', 'Basketball', self::PROVIDER_NAME),
            new Sport('35232', 'Ice Hockey', self::PROVIDER_NAME),
        ];
    }

    public function getLeagues(): array
    {
        $leagues = [];
        $data = $this->apiClient->getLeagues();
        foreach($data as $item) {
            $league = new League(
                $item['Id'],
                $item['Name'],
                $item['SportId'],
                self::PROVIDER_NAME,
            );
            $leagues[] = $league;
        }
        return $leagues;
    }

    public function getEvents(int $page = 0): Pagination
    {
        $results = $this->apiClient->getOddsData();
        $events = [];
        foreach ($results as $result) {
            if ($result['status'] !== 'upcoming') {
                continue;
            }
            $leagueId = 0;
            if (isset($result['leagueId'])) {
                $leagueId = $result['leagueId'];
            }
               $events[] = new Event(
                $result['id'],
                $result['startDate'],
                $result['sportId'],
                $leagueId,
                $result['homeTeam'],
                $result['awayTeam'],
                self::PROVIDER_NAME,
                $result['homePitcher'] ?? null,
                $result['awayPitcher'] ?? null,
            );
        }
        usort($events, fn (Event $a, Event $b) => $a->getStartsAt() <=> $b->getStartsAt());

        $total = count($events);
        return new Pagination($events, $total, $total);
    }

    public function getUpdates(): UpdateCollection
    {
        $data = $this->apiClient->getOddsData();

        foreach($data as $item) {
            $result = new Result(
                $item['id'],
                self::PROVIDER_NAME,
                TimeStatus::fromApiStatus($item['status']),
                $item['startDate'],
                $item['homeScore'],
                $item['awayScore'],
                $item['homePitcher'],
                $item['awayPitcher'],
            );

            $lines = [];
            $lineOffers = [];
            if (isset($item['lines'])) {
                foreach ($item['lines'] as $lineId => $line) {
                    if (isset($line['period'])) {
                        $period = $line['period'];
                        $name = $line['name'];
                        $type = $line['type'];
                    } else {
                        // provide backward compatability with old version of legendodds
                        $period = '100';
                        $name = '';
                        $type = '';
                        if ($item['moneylineHomeId'] == $lineId) {
                            $name = "home";
                            $type = "moneyline";
                        } else if ($item['moneylineAwayId'] == $lineId) {
                            $name = "away";
                            $type = "moneyline";
                        } else if ($item['spreadHomeId'] == $lineId) {
                            $name = "home";
                            $type = "spread";
                        } else if ($item['spreadAwayId'] == $lineId) {
                            $name = "away";
                            $type = "spread";
                        } if ($item['overId'] == $lineId) {
                            $name = "over";
                            $type = "total";
                        } else if ($item['underId'] == $lineId) {
                            $name = "under";
                            $type = "total";
                        }
                    }
                    $lines[] = new Line(
                        $lineId,
                        $period,
                        $name,
                        $type,
                        Odds::decimalToAmerican($line['price']),
                        isset($line['line']) ? new Decimal(explode(' ', $line['line'])[0]) : null,
                        Settlement::fromApiSettlement($line['settlement'])
                    );
                }
                $firstHalfAvailable = array_key_exists('moneylineHomeId_firsthalf', $item);
                $secondHalfAvailable = array_key_exists('moneylineHomeId_secondhalf', $item);

                $lineOffers[] = new Offer(
                    $item['moneylineHomeId'],
                    Offer::MONEYLINE, Offer::HOME, Offer::FULL_TIME
                );

                $lineOffers[] = new Offer(
                    $item['moneylineAwayId'],
                    Offer::MONEYLINE, Offer::AWAY, Offer::FULL_TIME
                );

                $lineOffers[] = new Offer(
                    $item['spreadHomeId'],
                    Offer::SPREAD, Offer::HOME, Offer::FULL_TIME
                );

                $lineOffers[] = new Offer(
                    $item['spreadAwayId'],
                    Offer::SPREAD, Offer::AWAY, Offer::FULL_TIME
                );

                $lineOffers[] = new Offer(
                    $item['overId'],
                    Offer::TOTAL, Offer::OVER, Offer::FULL_TIME
                );

                $lineOffers[] = new Offer(
                    $item['underId'],
                    Offer::TOTAL, Offer::UNDER, Offer::FULL_TIME
                );

                if ($firstHalfAvailable) {
                    $lineOffers[] = new Offer(
                        $item['moneylineHomeId_firsthalf'],
                        Offer::MONEYLINE, Offer::HOME, Offer::FIRST_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['moneylineAwayId_firsthalf'],
                        Offer::MONEYLINE, Offer::AWAY, Offer::FIRST_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['spreadHomeId_firsthalf'],
                        Offer::SPREAD, Offer::HOME, Offer::FIRST_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['spreadAwayId_firsthalf'],
                        Offer::SPREAD, Offer::AWAY, Offer::FIRST_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['overId_firsthalf'],
                        Offer::TOTAL, Offer::OVER, Offer::FIRST_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['underId_firsthalf'],
                        Offer::TOTAL, Offer::UNDER, Offer::FIRST_HALF
                    );
                }

                if ($secondHalfAvailable) {

                    $lineOffers[] = new Offer(
                        $item['moneylineHomeId_secondhalf'],
                        Offer::MONEYLINE, Offer::HOME, Offer::SECOND_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['moneylineAwayId_secondhalf'],
                        Offer::MONEYLINE, Offer::AWAY, Offer::SECOND_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['spreadHomeId_secondhalf'],
                        Offer::SPREAD, Offer::HOME, Offer::SECOND_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['spreadAwayId_secondhalf'],
                        Offer::SPREAD, Offer::AWAY, Offer::SECOND_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['overId_secondhalf'],
                        Offer::TOTAL, Offer::OVER, Offer::SECOND_HALF
                    );

                    $lineOffers[] = new Offer(
                        $item['underId_secondhalf'],
                        Offer::TOTAL, Offer::UNDER, Offer::SECOND_HALF
                    );
                }
            }

            $updates[] = new Update(
                $item['id'],
                $result,
                new LineCollection(...$lines),
                new OfferCollection(...$lineOffers)
            );
        }

        return new UpdateCollection(self::PROVIDER_NAME, ...$updates);
    }
}
