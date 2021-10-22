<?php

namespace Unit\Betting;

use App\Betting\ApiClient;
use App\Betting\LegendsOdds;
use App\Betting\Pagination;
use App\Betting\Settlement;
use App\Betting\SportEvent\Sport;
use App\Betting\SportEvent\Event;
use App\Betting\SportEvent\Line;
use App\Betting\SportEvent\LineCollection;
use App\Betting\SportEvent\Offer;
use App\Betting\SportEvent\OfferCollection;
use App\Betting\SportEvent\Result;
use App\Betting\SportEvent\Update;
use App\Betting\SportEvent\UpdateCollection;
use App\Betting\TimeStatus;
use App\Domain\Odds;
use App\Http\Controllers\App\Api\OddCollection;
use App\Tournament\Enums\GamePeriod;
use Decimal\Decimal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Betting\LegendsOdds
 * @uses \App\Betting\SportEvent\Event
 * @uses \App\Betting\Pagination
 */
class LegendsOddsTest extends TestCase
{
    private const TEST_DATA = [
        'upcoming_event_no_odds' => [
            'id' => 5253972,
            'homeScore' => NULL,
            'awayScore' => NULL,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'upcoming',
            'awayTeam' => 'Boise State',
            'homeTeam' => 'BYU',
            'awayPitcher' => NULL,
            'homePitcher' => NULL,
            'sportId' => 131506,
            'startDate' => '2020-11-06 17:00:00',
        ],
        'upcoming_event_no_odds_later' => [
            'id' => 5253973,
            'homeScore' => NULL,
            'awayScore' => NULL,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'upcoming',
            'awayTeam' => 'Boise State',
            'homeTeam' => 'BYU',
            'awayPitcher' => NULL,
            'homePitcher' => NULL,
            'sportId' => 131506,
            'startDate' => '2020-11-07 17:00:00',
        ],
        'inplay_event_no_odds' => [
            'id' => 5253974,
            'homeScore' => NULL,
            'awayScore' => NULL,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'inplay',
            'awayTeam' => 'Boise State',
            'homeTeam' => 'BYU',
            'awayPitcher' => NULL,
            'homePitcher' => NULL,
            'sportId' => 131506,
            'startDate' => '2020-11-06 17:00:00',
        ],
        'upcoming_event_with_lines_no_offers' => [
            'id' => 6071474,
            'homeScore' => null,
            'awayScore' => null,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'upcoming',
            'awayTeam' => 'Tampa Bay Rays',
            'homeTeam' => 'Los Angeles Dodgers',
            'awayPitcher' => 'T Gonsolin',
            'homePitcher' => 'B Snell',
            'sportId' => 154914,
            'startDate' => '2020-10-22 00:08:00',
            'moneylineHomeId' => NULL,
            'moneylineAwayId' => NULL,
            'spreadHomeId' => NULL,
            'spreadAwayId' => NULL,
            'overId' => NULL,
            'underId' => NULL,
            'lines' => [
                20950525776071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'moneyline',
                        'price' => '9.25',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                20950525746071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'moneyline',
                        'price' => '1.04',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                6043715436071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '1.197',
                        'line' => '1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6101595766071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.108',
                        'line' => '2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                11877983306071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.242',
                        'line' => '-1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                10609991996071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '1.48',
                        'line' => '-2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6990234886071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.606',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                10753644746071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '2.4',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                8401859896071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '2.05',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
                10753643796071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.714',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
            ],
        ],
        'upcoming_event_with_lines' => [
            'id' => 6071474,
            'homeScore' => null,
            'awayScore' => null,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'upcoming',
            'awayTeam' => 'Tampa Bay Rays',
            'homeTeam' => 'Los Angeles Dodgers',
            'awayPitcher' => 'T Gonsolin',
            'homePitcher' => 'B Snell',
            'sportId' => 154914,
            'startDate' => '2020-10-22 00:08:00',
            'moneylineHomeId' => 20950525776071474,
            'moneylineAwayId' => 20950525746071474,
            'spreadHomeId' => 6043715436071474,
            'spreadAwayId' => 11877983306071474,
            'overId' => 6990234886071474,
            'underId' => 10753644746071474,
            'lines' => [
                20950525776071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'moneyline',
                        'price' => '9.25',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                20950525746071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'moneyline',
                        'price' => '1.04',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                6043715436071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '1.197',
                        'line' => '1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6101595766071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.108',
                        'line' => '2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                11877983306071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.242',
                        'line' => '-1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                10609991996071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '1.48',
                        'line' => '-2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6990234886071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.606',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                10753644746071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '2.4',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                8401859896071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '2.05',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
                10753643796071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.714',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
            ],
        ],
        'inplay_event_with_lines' => [
            'id' => 6071474,
            'homeScore' => 4,
            'awayScore' => 6,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'inplay',
            'awayTeam' => 'Tampa Bay Rays',
            'homeTeam' => 'Los Angeles Dodgers',
            'awayPitcher' => 'T Gonsolin',
            'homePitcher' => 'B Snell',
            'sportId' => 154914,
            'startDate' => '2020-10-22 00:08:00',
            'moneylineHomeId' => 20950525776071474,
            'moneylineAwayId' => 20950525746071474,
            'spreadHomeId' => 6043715436071474,
            'spreadAwayId' => 11877983306071474,
            'overId' => 6990234886071474,
            'underId' => 10753644746071474,
            'lines' => [
                20950525776071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'moneyline',
                        'price' => '9.25',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                20950525746071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'moneyline',
                        'price' => '1.04',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                6043715436071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '1.197',
                        'line' => '1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6101595766071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.108',
                        'line' => '2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                11877983306071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.242',
                        'line' => '-1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                10609991996071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '1.48',
                        'line' => '-2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6990234886071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.606',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                10753644746071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '2.4',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                8401859896071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '2.05',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
                10753643796071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.714',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
            ],
        ], //@TODO
        'ended_event_with_lines' => [
            'id' => 6071474,
            'homeScore' => 4,
            'awayScore' => 6,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'ended',
            'awayTeam' => 'Tampa Bay Rays',
            'homeTeam' => 'Los Angeles Dodgers',
            'awayPitcher' => 'T Gonsolin',
            'homePitcher' => 'B Snell',
            'sportId' => 154914,
            'startDate' => '2020-10-22 00:08:00',
            'moneylineHomeId' => 20950525776071474,
            'moneylineAwayId' => 20950525746071474,
            'spreadHomeId' => 6043715436071474,
            'spreadAwayId' => 11877983306071474,
            'overId' => 6990234886071474,
            'underId' => 10753644746071474,
            'lines' => [
                20950525776071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'moneyline',
                        'price' => '9.25',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                20950525746071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'moneyline',
                        'price' => '1.04',
                        'line' => NULL,
                        'settlement' => NULL,
                    ],
                6043715436071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '1.197',
                        'line' => '1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6101595766071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.108',
                        'line' => '2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                11877983306071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.242',
                        'line' => '-1.5 (0-0)',
                        'settlement' => NULL,
                    ],
                10609991996071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '1.48',
                        'line' => '-2.5 (0-0)',
                        'settlement' => NULL,
                    ],
                6990234886071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.606',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                10753644746071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '2.4',
                        'line' => '9.5',
                        'settlement' => NULL,
                    ],
                8401859896071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '2.05',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
                10753643796071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.714',
                        'line' => '8.5',
                        'settlement' => NULL,
                    ],
            ],
        ], //@TODO
        'ended_event_with_lines_and_settlements' => [
            'id' => 6071474,
            'homeScore' => 4,
            'awayScore' => 6,
            'moneylineHome' => NULL,
            'moneylineAway' => NULL,
            'spreadHome' => NULL,
            'spreadAway' => NULL,
            'handicapHome' => NULL,
            'handicapAway' => NULL,
            'over' => NULL,
            'under' => NULL,
            'total' => NULL,
            'moneylineHome_firsthalf' => NULL,
            'moneylineAway_firsthalf' => NULL,
            'spreadHome_firsthalf' => NULL,
            'spreadAway_firsthalf' => NULL,
            'handicapHome_firsthalf' => NULL,
            'handicapAway_firsthalf' => NULL,
            'over_firsthalf' => NULL,
            'under_firsthalf' => NULL,
            'total_firsthalf' => NULL,
            'moneylineHome_secondhalf' => NULL,
            'moneylineAway_secondhalf' => NULL,
            'spreadHome_secondhalf' => NULL,
            'spreadAway_secondhalf' => NULL,
            'handicapHome_secondhalf' => NULL,
            'handicapAway_secondhalf' => NULL,
            'over_secondhalf' => NULL,
            'under_secondhalf' => NULL,
            'total_secondhalf' => NULL,
            'status' => 'ended',
            'awayTeam' => 'Tampa Bay Rays',
            'homeTeam' => 'Los Angeles Dodgers',
            'awayPitcher' => 'T Gonsolin',
            'homePitcher' => 'B Snell',
            'sportId' => 154914,
            'startDate' => '2020-10-22 00:08:00',
            'moneylineHomeId' => 20950525776071474,
            'moneylineAwayId' => 20950525746071474,
            'spreadHomeId' => 6043715436071474,
            'spreadAwayId' => 11877983306071474,
            'overId' => 6990234886071474,
            'underId' => 10753644746071474,
            'lines' => [
                20950525776071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'moneyline',
                        'price' => '9.25',
                        'line' => NULL,
                        'settlement' => 'Won',
                    ],
                20950525746071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'moneyline',
                        'price' => '1.04',
                        'line' => NULL,
                        'settlement' => 'Lost',
                    ],
                6043715436071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '1.197',
                        'line' => '1.5 (0-0)',
                        'settlement' => 'Won',
                    ],
                6101595766071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.108',
                        'line' => '2.5 (0-0)',
                        'settlement' => 'Lost',
                    ],
                11877983306071474 =>
                    [
                        'period' => '100',
                        'name' => 'away',
                        'type' => 'spread',
                        'price' => '1.242',
                        'line' => '-1.5 (0-0)',
                        'settlement' => 'Lost',
                    ],
                10609991996071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '1.48',
                        'line' => '-2.5 (0-0)',
                        'settlement' => 'Won',
                    ],
                6990234886071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.606',
                        'line' => '9.5',
                        'settlement' => 'Won',
                    ],
                10753644746071474 =>
                    [
                        'period' => '100',
                        'name' => 'under',
                        'type' => 'total',
                        'price' => '2.4',
                        'line' => '9.5',
                        'settlement' => 'Lost',
                    ],
                8401859896071474 =>
                    [
                        'period' => '100',
                        'name' => 'home',
                        'type' => 'spread',
                        'price' => '2.05',
                        'line' => '8.5',
                        'settlement' => 'Won',
                    ],
                10753643796071474 =>
                    [
                        'period' => '100',
                        'name' => 'over',
                        'type' => 'total',
                        'price' => '1.714',
                        'line' => '8.5',
                        'settlement' => 'Lost',
                    ],
            ],
        ] //@TODO
    ];

    public function testGetSports()
    {
        $mockApiClient = \Mockery::mock(ApiClient::class);
        $sut = new LegendsOdds($mockApiClient);

        $sports = $sut->getSports();
        self::assertIsArray($sports);
        self::assertCount(4, $sports);

        foreach ($sports as $sport) {
            self::assertInstanceOf(Sport::class, $sport);
        }
    }

    /** @dataProvider provideGetEvents */
    public function testGetEvents(array $apiData, Pagination $expected)
    {
        $mockApiClient = \Mockery::mock(ApiClient::class);
        $mockApiClient->shouldReceive('getOddsData')->andReturn($apiData);
        $sut = new LegendsOdds($mockApiClient);

        self::assertEquals($expected, $sut->getEvents());
    }

    public function provideGetEvents()
    {
        $upcomingEventNoOdds = new Event(
            5253972,
            '2020-11-06 17:00:00',
            131506,
            'BYU',
            'Boise State',
            LegendsOdds::PROVIDER_NAME,
            null,
            null,
        );
        $upcomingEventNoOddsLater = new Event(
            5253973,
            '2020-11-07 17:00:00',
            131506,
            'BYU',
            'Boise State',
            LegendsOdds::PROVIDER_NAME,
            null,
            null,
        );

        return [
            [
                [self::TEST_DATA['upcoming_event_no_odds']],
                new Pagination([$upcomingEventNoOdds],1,1)
            ],
            [
                [self::TEST_DATA['upcoming_event_no_odds_later'], self::TEST_DATA['upcoming_event_no_odds']],
                new Pagination([$upcomingEventNoOdds, $upcomingEventNoOddsLater],2,2)
            ],
            [[self::TEST_DATA['inplay_event_no_odds']], new Pagination([],0,0)]
        ];
    }

    /** @dataProvider provideGetUpdates */
    public function testGetUpdates(array $apiData, UpdateCollection $expected)
    {
        $mockApiClient = \Mockery::mock(ApiClient::class);
        $mockApiClient->shouldReceive('getOddsData')->andReturn([$apiData]);
        $sut = new LegendsOdds($mockApiClient);

        self::assertEquals($expected, $sut->getUpdates());
    }

    public function provideGetUpdates()
    {
        $periodFullTime = GamePeriod::FULL_TIME();
        $unsettledLines = [
            new Line(20950525776071474, $periodFullTime, Offer::HOME, Offer::MONEYLINE, Odds::decimalToAmerican('9.25'), null, null),
            new Line(20950525746071474, $periodFullTime, Offer::AWAY, Offer::MONEYLINE, Odds::decimalToAmerican('1.04'), null, null),
            new Line(6043715436071474, $periodFullTime, Offer::HOME, Offer::SPREAD, Odds::decimalToAmerican('1.197'), new Decimal('1.5'), null),
            new Line(6101595766071474, $periodFullTime, Offer::AWAY, Offer::SPREAD, Odds::decimalToAmerican('1.108'), new Decimal('2.5'), null),
            new Line(11877983306071474, $periodFullTime, Offer::AWAY, Offer::SPREAD, Odds::decimalToAmerican('1.242'), new Decimal('-1.5'), null),
            new Line(10609991996071474, $periodFullTime, Offer::UNDER, Offer::TOTAL, Odds::decimalToAmerican('1.48'), new Decimal('-2.5'), null),
            new Line(6990234886071474, $periodFullTime, Offer::OVER, Offer::TOTAL, Odds::decimalToAmerican('1.606'), new Decimal('9.5'), null),
            new Line(10753644746071474, $periodFullTime, Offer::UNDER, Offer::TOTAL, Odds::decimalToAmerican('2.4'), new Decimal('9.5'), null),
            new Line(8401859896071474, $periodFullTime, Offer::HOME, Offer::SPREAD, Odds::decimalToAmerican('2.05'), new Decimal('8.5'), null),
            new Line(10753643796071474, $periodFullTime, Offer::OVER, Offer::TOTAL, Odds::decimalToAmerican('1.714'), new Decimal('8.5'), null),
        ];
        $settledLines = [
            new Line(20950525776071474, $periodFullTime, Offer::HOME, Offer::MONEYLINE, Odds::decimalToAmerican('9.25'), null, Settlement::WON()),
            new Line(20950525746071474, $periodFullTime, Offer::AWAY, Offer::MONEYLINE, Odds::decimalToAmerican('1.04'), null, Settlement::LOST()),
            new Line(6043715436071474, $periodFullTime, Offer::HOME, Offer::SPREAD, Odds::decimalToAmerican('1.197'), new Decimal('1.5'), Settlement::WON()),
            new Line(6101595766071474, $periodFullTime, Offer::AWAY, Offer::SPREAD, Odds::decimalToAmerican('1.108'), new Decimal('2.5'), Settlement::LOST()),
            new Line(11877983306071474, $periodFullTime, Offer::AWAY, Offer::SPREAD, Odds::decimalToAmerican('1.242'), new Decimal('-1.5'), Settlement::LOST()),
            new Line(10609991996071474, $periodFullTime, Offer::UNDER, Offer::TOTAL, Odds::decimalToAmerican('1.48'), new Decimal('-2.5'), Settlement::WON()),
            new Line(6990234886071474, $periodFullTime, Offer::OVER, Offer::TOTAL, Odds::decimalToAmerican('1.606'), new Decimal('9.5'), Settlement::WON()),
            new Line(10753644746071474, $periodFullTime, Offer::UNDER, Offer::TOTAL, Odds::decimalToAmerican('2.4'), new Decimal('9.5'), Settlement::LOST()),
            new Line(8401859896071474, $periodFullTime, Offer::HOME, Offer::SPREAD, Odds::decimalToAmerican('2.05'), new Decimal('8.5'), Settlement::WON()),
            new Line(10753643796071474, $periodFullTime, Offer::OVER, Offer::TOTAL, Odds::decimalToAmerican('1.714'), new Decimal('8.5'), Settlement::LOST()),
        ];
        $nullOffers = [
            new Offer(null, Offer::MONEYLINE, Offer::HOME, Offer::FULL_TIME),
            new Offer(null, Offer::MONEYLINE, Offer::AWAY, Offer::FULL_TIME),
            new Offer(null, Offer::SPREAD, Offer::HOME, Offer::FULL_TIME),
            new Offer(null, Offer::SPREAD, Offer::AWAY, Offer::FULL_TIME),
            new Offer(null, Offer::TOTAL, Offer::OVER, Offer::FULL_TIME),
            new Offer(null, Offer::TOTAL, Offer::UNDER, Offer::FULL_TIME),
            /*
            new Offer(null, Offer::MONEYLINE, Offer::HOME, Offer::FIRST_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::AWAY, Offer::FIRST_HALF),
            new Offer(null, Offer::SPREAD, Offer::HOME, Offer::FIRST_HALF),
            new Offer(null, Offer::SPREAD, Offer::AWAY, Offer::FIRST_HALF),
            new Offer(null, Offer::TOTAL, Offer::OVER, Offer::FIRST_HALF),
            new Offer(null, Offer::TOTAL, Offer::UNDER, Offer::FIRST_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::HOME, Offer::SECOND_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::AWAY, Offer::SECOND_HALF),
            new Offer(null, Offer::SPREAD, Offer::HOME, Offer::SECOND_HALF),
            new Offer(null, Offer::SPREAD, Offer::AWAY, Offer::SECOND_HALF),
            new Offer(null, Offer::TOTAL, Offer::OVER, Offer::SECOND_HALF),
            new Offer(null, Offer::TOTAL, Offer::UNDER, Offer::SECOND_HALF),
            */
        ];
        $fullOffers = [
            new Offer(20950525776071474, Offer::MONEYLINE, Offer::HOME, Offer::FULL_TIME),
            new Offer(20950525746071474, Offer::MONEYLINE, Offer::AWAY, Offer::FULL_TIME),
            new Offer(6043715436071474, Offer::SPREAD, Offer::HOME, Offer::FULL_TIME),
            new Offer(11877983306071474, Offer::SPREAD, Offer::AWAY, Offer::FULL_TIME),
            new Offer(6990234886071474, Offer::TOTAL, Offer::OVER, Offer::FULL_TIME),
            new Offer(10753644746071474, Offer::TOTAL, Offer::UNDER, Offer::FULL_TIME),
            /*
            new Offer(null, Offer::MONEYLINE, Offer::HOME, Offer::FIRST_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::AWAY, Offer::FIRST_HALF),
            new Offer(null, Offer::SPREAD, Offer::HOME, Offer::FIRST_HALF),
            new Offer(null, Offer::SPREAD, Offer::AWAY, Offer::FIRST_HALF),
            new Offer(null, Offer::TOTAL, Offer::OVER, Offer::FIRST_HALF),
            new Offer(null, Offer::TOTAL, Offer::UNDER, Offer::FIRST_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::HOME, Offer::SECOND_HALF),
            new Offer(null, Offer::MONEYLINE, Offer::AWAY, Offer::SECOND_HALF),
            new Offer(null, Offer::SPREAD, Offer::HOME, Offer::SECOND_HALF),
            new Offer(null, Offer::SPREAD, Offer::AWAY, Offer::SECOND_HALF),
            new Offer(null, Offer::TOTAL, Offer::OVER, Offer::SECOND_HALF),
            new Offer(null, Offer::TOTAL, Offer::UNDER, Offer::SECOND_HALF),
            */
        ];
        $results = [
            'upcoming' => new Result(
                5253972,
                LegendsOdds::PROVIDER_NAME,
                TimeStatus::NOT_STARTED(),
                '2020-11-06 17:00:00',
                null,
                null,
                null,
                null,
            ),
            'upcomingBaseball' => new Result(
                6071474,
                LegendsOdds::PROVIDER_NAME,
                TimeStatus::NOT_STARTED(),
                '2020-10-22 00:08:00',
                null,
                null,
                'B Snell',
                'T Gonsolin',
            ),
            'inplayBaseball' => new Result(
                6071474,
                LegendsOdds::PROVIDER_NAME,
                TimeStatus::IN_PLAY(),
                '2020-10-22 00:08:00',
                4,
                6,
                'B Snell',
                'T Gonsolin',
            ),
            'endedBaseball' => new Result(
                6071474,
                LegendsOdds::PROVIDER_NAME,
                TimeStatus::ENDED(),
                '2020-10-22 00:08:00',
                4,
                6,
                'B Snell',
                'T Gonsolin',
            ),
        ];

        return [
            [
                self::TEST_DATA['upcoming_event_no_odds'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(5253972, $results['upcoming'], new LineCollection(), new OfferCollection())
                )
            ],
            [
                self::TEST_DATA['upcoming_event_with_lines_no_offers'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(6071474, $results['upcomingBaseball'], new LineCollection(...$unsettledLines), new OfferCollection(...$nullOffers))
                )
            ],
            [
                self::TEST_DATA['upcoming_event_with_lines'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(6071474, $results['upcomingBaseball'], new LineCollection(...$unsettledLines), new OfferCollection(...$fullOffers))
                )
            ],
            [
                self::TEST_DATA['inplay_event_with_lines'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(6071474, $results['inplayBaseball'], new LineCollection(...$unsettledLines), new OfferCollection(...$fullOffers))
                )
            ],
            [
                self::TEST_DATA['ended_event_with_lines'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(6071474, $results['endedBaseball'], new LineCollection(...$unsettledLines), new OfferCollection(...$fullOffers))
                )
            ],
            [
                self::TEST_DATA['ended_event_with_lines_and_settlements'],
                new UpdateCollection(
                    LegendsOdds::PROVIDER_NAME,
                    new Update(6071474, $results['endedBaseball'], new LineCollection(...$settledLines), new OfferCollection(...$fullOffers))
                )
            ],
        ];
    }
}
