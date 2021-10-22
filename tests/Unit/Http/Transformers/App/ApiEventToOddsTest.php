<?php

namespace Unit\Http\Transformers\App;

use App\Http\Transformers\App\ApiEventToOdds;
use App\Tournament\Enums\GamePeriod;
use Decimal\Decimal;
use PHPUnit\Framework\TestCase;
use Tests\Fixture\Factory\ApiEventFactory;

/**
 * @covers \App\Http\Transformers\App\ApiEventToOdds
 * @uses \App\Domain\ApiEvent
 * @uses \App\Domain\ApiEventOdds
 */
class ApiEventToOddsTest extends TestCase
{
    public function testTransform()
    {
        $expected = [
            "external_id" => 'eid',
            "count" => 6,

            /*
            'odds' => [
                [
                    "external_id" => 'eid',
                    "period" => GamePeriod::FULL_TIME(),
                    "moneyline_away" => -200,
                    "moneyline_home" => 200,
                    "point_spread_away" => -125,
                    "point_spread_home" => 275,
                    "point_spread_away_line" => new Decimal('-2'),
                    "point_spread_home_line" => new Decimal('2'),
                    "overline" => 150,
                    "underline" => -175,
                    "total_number" => new Decimal('4'),
                ],
                [
                    "external_id" => 'eid',
                    "period" => GamePeriod::FIRST_HALF(),
                    "moneyline_away" => null,
                    "moneyline_home" => null,
                    "point_spread_away" => null,
                    "point_spread_home" => null,
                    "point_spread_away_line" => null,
                    "point_spread_home_line" => null,
                    "overline" => null,
                    "underline" => null,
                    "total_number" => null,
                ],
                [
                    "external_id" => 'eid',
                    "period" => GamePeriod::SECOND_HALF(),
                    "moneyline_away" => null,
                    "moneyline_home" => null,
                    "point_spread_away" => null,
                    "point_spread_home" => null,
                    "point_spread_away_line" => null,
                    "point_spread_home_line" => null,
                    "overline" => null,
                    "underline" => null,
                    "total_number" => null,
                ]
            ]
            */
            
        ];
        $apiEvent = ApiEventFactory::create();
        $sut = new ApiEventToOdds();

        $result = $sut->transform($apiEvent);

        self::assertEquals($expected, $result);
        self::assertEquals(6, $result['count']);
        //self::assertEquals('-2', $result['odds'][0]['point_spread_away_line']->toString());
        //self::assertEquals('2', $result['odds'][0]['point_spread_home_line']->toString());
        //self::assertEquals('4', $result['odds'][0]['total_number']->toString());
    }
}
