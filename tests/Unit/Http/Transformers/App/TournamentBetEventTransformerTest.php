<?php

namespace Unit\Http\Transformers\App;

use App\Domain\ApiEvent;
use App\Domain\BetItem;
use App\Domain\Tournament;
use App\Domain\TournamentBet;
use App\Domain\TournamentEvent;
use App\Domain\TournamentPlayer;
use App\Domain\User;
use App\Http\Transformers\App\DoctrineTournamentBetEventTransformer;
use App\Tournament\Enums\BetStatus;
use App\Tournament\Enums\GamePeriod;
use App\Tournament\Enums\PendingOddType;
use Carbon\Carbon;
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
 */
class TournamentBetEventTransformerTest extends TestCase
{
    public function testTransform()
    {
        $apiEvent = ApiEventFactory::create();
        FactoryAbstract::setProperty($apiEvent, 'teamAway', 'TeamAway');
        FactoryAbstract::setProperty($apiEvent, 'teamHome', 'TeamHome');
        FactoryAbstract::setProperty($apiEvent, 'scoreAway', 10);
        FactoryAbstract::setProperty($apiEvent, 'scoreHome', 20);
        $dateTime = new \DateTime();
        FactoryAbstract::setProperty($apiEvent, 'startsAt', $dateTime);

        $user = new User('test', 'test@test.com', 'test', '', '', new \DateTime());
        $tournament = new Tournament();
        FactoryAbstract::setProperty($tournament, 'id', 1);
        FactoryAbstract::setProperty($tournament, 'chips', 10000);
        $tournament->registerPlayer($user);
        $tournamentPlayer = $user->getTournamentPlayer($tournament);
        FactoryAbstract::setProperty($tournamentPlayer, 'id', 1);
        
        $event = new TournamentEvent($tournament, $apiEvent);
        FactoryAbstract::setProperty($event, 'id', 1);
        FactoryAbstract::setProperty($event, 'apiEvent', $apiEvent);
        $sut = new BetItem(2, PendingOddType::MONEY_LINE_AWAY(), GamePeriod::FULL_TIME(), $event);
        $betEvent = $sut->makeBetEvent($tournamentPlayer);
        FactoryAbstract::setProperty($betEvent, 'id', 1);
        FactoryAbstract::setProperty($betEvent, 'status', BetStatus::PENDING());

        $expected = [ 
            "id" => 1,
            "external_id" => 1,
            "odd" => -200,
            "score_away" => 10,
            "score_home" => 20,
            "selected_team" => "TeamAway",
            "starts_at" => (new Carbon($dateTime))->toAtomString(),
            "status" => BetStatus::PENDING(),
            "team_away" => 'TeamAway',
            "team_home" => 'TeamHome',
            "type" => 'moneyline_away',
            "handicap" => null,
            "period" => '100',
        ];
        $tbet = new DoctrineTournamentBetEventTransformer();
        $result = $tbet->transform($betEvent);
        self::assertEquals($expected, $result);
    }
}
