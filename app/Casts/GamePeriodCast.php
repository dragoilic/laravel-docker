<?php
namespace App\Casts;

use App\Tournament\Enums\GamePeriod;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class GamePeriodCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new GamePeriod($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return (new GamePeriod($value))->getValue();
    }
}
