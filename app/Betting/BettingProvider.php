<?php
namespace App\Betting;

use App\Betting\SportEvent\Event;
use App\Betting\SportEvent\League;
use App\Betting\SportEvent\Sport;
use App\Betting\SportEvent\UpdateCollection;

interface BettingProvider
{
    /** @return Pagination<Event> */
    public function getEvents(int $page): Pagination;

    /** @return Sport[] */
    public function getSports(): array;

    /** @return League[] */
    public function getLeagues(): array;

    public function getUpdates(): UpdateCollection;
}
