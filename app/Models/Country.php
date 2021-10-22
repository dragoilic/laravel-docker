<?php
namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $code
 * @property string $name
 * @property string $phone_code
 * @mixin Eloquent
 */
class Country extends Model
{
    use StaticTable;

    protected $table = 'countries';

    public function users()
    {
        return $this->hasMany(User::class, 'country_code', 'code');
    }
}
