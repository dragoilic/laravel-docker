<?php

namespace App\BotPlayers\BettingStrategy;

use App\Betting\SportEvent\Offer;
use App\Domain\BetItem;
use App\Domain\Tournament;
use App\Domain\TournamentPlayer;
use App\Tournament\Enums\GamePeriod;

class StraightBets implements BettingStrategy
{
    private int $maxBets;
    private int $minBets;
    private WagerCalculator $wagerCalculator;

    public function __construct(WagerCalculator $wagerCalculator, int $minBets = 1, int $maxBets = 8)
    {
        $this->wagerCalculator = $wagerCalculator;
        $this->maxBets = $maxBets;
        $this->minBets = $minBets;
    }

    public function placeBets(Tournament $tournament, TournamentPlayer $tournamentPlayer, int $hundredChipsToWager, int $remainder = 0): bool
    {
        if ($hundredChipsToWager + $remainder === 0) {
            return false;
        }

        $events = $tournament->getBettableEvents()->toArray();

        $maxBetOptions = 0;
        foreach ($events as $event) {
            $maxBetOptions += count($event->getApiEvent()->getOddTypes());
        }

        $betsToPlace = min(rand($this->minBets, $this->maxBets), $maxBetOptions);
        if ($betsToPlace === 0) {
            return false;
        }

        $wagersToPlace = $this->wagerCalculator->calculateWagers($hundredChipsToWager, $betsToPlace);
        if (count($wagersToPlace)) {
            $wagersToPlace[0] += $remainder;
        }
        $betsPlaced = [];

        foreach ($wagersToPlace as $wager) {
            if ($wager === 0) {
                continue;
            }

            $betPlaced = false;
            do {
                $event = $events[array_rand($events, 1)];
                $oddTypes = $event->getApiEvent()->getOddTypes();
                $oddType = $oddTypes[array_rand($oddTypes, 1)];
                $gamePeriod = GamePeriod::FULL_TIME();
                $betType = $oddType;
                $odds = $event->getApiEvent()->getOddsAllLines()->toArray();
                $odd = $odds[array_rand($odds, 1)];
                $tags = explode('_', $oddType);
                if (in_array(Offer::FIRST_HALF, $tags)) {
                    $gamePeriod = GamePeriod::FIRST_HALF();
                    unset($tags[2]);
                    $betType = implode('_', $tags);
                } else if (in_array(Offer::SECOND_HALF, $tags)) {
                    $gamePeriod = GamePeriod::SECOND_HALF();
                    unset($tags[2]);
                    $betType = implode('_', $tags);
                }
                if (!isset($betsPlaced[$event->getId()][$betType])) {
                    $betsPlaced[$event->getId()][$betType] = true;
                    $betItem = new BetItem($odd->getId(), $betType, $gamePeriod, $event);
                    $tournament->placeStraightBet($tournamentPlayer, $wager * 100, $betItem);
                    $betPlaced = true;
                }
            } while (!$betPlaced);
        }

        return true;
    }
}
