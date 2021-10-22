<?php

namespace Unit\Http\Transformers\App;

use App\Betting\TimeStatus;
use App\Betting\SportEvent\Result;
use App\Domain\ApiEvent;
use App\Domain\BetItem;
use App\Domain\Tournament;
use App\Domain\TournamentBet;
use App\Domain\TournamentPlayer;
use App\Domain\User;
use App\Tournament\Enums\BetStatus;
use App\Tournament\Enums\GamePeriod;
use App\Tournament\Enums\PendingOddType;
use App\Http\Transformers\App\DoctrineTournamentBetTransformer;
use PHPUnit\Framework\TestCase;
use Tests\Fixture\Factory\ApiEventFactory;
use Tests\Fixture\Factory\FactoryAbstract;

/**
 * @covers App\Http\Transformers\App\DoctrineTournamentBetTransformer
 * @uses App\Domain\ApiEvent
 * @uses App\Domain\BetItem
 * @uses App\Domain\Tournament
 * @uses App\Domain\TournamentBet
 * @uses App\Domain\TournamentEvent
 * @uses App\Domain\TournamentPlayer
 * @uses App\Domain\User
 */
class TournamentBetTransformerTest extends TestCase
{
    public function testTransform()
    {
       
        $apiEvent = ApiEventFactory::create();
        $tournament = new Tournament();
        FactoryAbstract::setProperty($tournament, 'id', 1);
        FactoryAbstract::setProperty($tournament, 'chips', 10000);
        $tournament->addEvent($apiEvent);
        $tournamentEvent = $tournament->getEvents()->first();

        $user = new User('test', 'test@test.com', 'test', '', '', new \DateTime());
        $tournament->registerPlayer($user);
        $tournamentPlayer = $user->getTournamentPlayer($tournament);
        FactoryAbstract::setProperty($tournamentPlayer, 'id', 1);

        $tournament->placeStraightBet($tournamentPlayer, 1000, new BetItem(2, PendingOddType::MONEY_LINE_AWAY(), GamePeriod::FULL_TIME(), $tournamentEvent));
        $apiEvent->result(new Result('eid', 'test', TimeStatus::ENDED(), new \DateTime(), 10, 20));

        $bet = $tournament->getBets() -> first();
        FactoryAbstract::setProperty($bet, 'id', 1);

        $expected = [ 
            "id" => 1,
            "chips_wager" => 1000,
            "chips_win" => 500,
            "tournament_id"=> 1,
            "status"=> BetStatus::PENDING(),
        ];
        $tbt = new DoctrineTournamentBetTransformer();
        $result = $tbt->transform($bet);
        self::assertEquals($expected, $result);


    }
}
