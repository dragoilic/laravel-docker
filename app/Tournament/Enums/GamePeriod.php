<?php
namespace App\Tournament\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static GamePeriod FULL_TIME()
 * @method static GamePeriod FIRST_HALF()
 * @method static GamePeriod SECOND_HALF()
 */
final class GamePeriod extends Enum
{
    private const FULL_TIME = "100";
    private const FIRST_HALF = "1";
    private const SECOND_HALF = "2";
}
