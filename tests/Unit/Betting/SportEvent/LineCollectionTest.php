<?php

namespace Unit\Betting\SportEvent;

use App\Betting\SportEvent\Line;
use App\Betting\SportEvent\LineCollection;
use App\Betting\SportEvent\Offer;
use App\Tournament\Enums\GamePeriod;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Betting\SportEvent\LineCollection
 * @uses \App\Betting\SportEvent\Line
 */
class LineCollectionTest extends TestCase
{
    public function testConstruct()
    {
        $period = GamePeriod::FULL_TIME();
        $name = Offer::HOME;;
        $type = Offer::MONEYLINE;
        $line = new Line('ml::h::ft', $period, $name, $type, 175, null, null);
        $sut = new LineCollection($line);

        self::assertCount(1, $sut->getLines());
        self::assertContains($line, $sut->getLines());
    }

    public function testConstructEmpty()
    {
        $sut = new LineCollection();

        self::assertEmpty($sut->getLines());
    }
}
