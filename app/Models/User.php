<?php
namespace App\Models;
use App\Services\UserTokenService;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property int $balance
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|TournamentBet[] $bets
 * @property-read Collection|TournamentPlayer[] $players
 * @property-read Collection|Tournament[] $tournaments
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    
    use StaticTable;

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $fillable = [
        'name', 'firstname', 'lastname', 'email', 'phone', 'country_code', 'facebook_id'
    ];

    protected $appends = ['referral_link'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public function getReferralLinkAttribute()
    {
        return $this->referral_link = route('register', ['ref' => $this->name]);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
    
    public function players()
    {
        return $this->hasMany(TournamentPlayer::class);
    }

    public function tournaments()
    {
        return $this->hasManyThrough(Tournament::class, TournamentPlayer::class);
    }

    public function bets()
    {
        return $this->hasManyThrough(TournamentBet::class, TournamentPlayer::class);
    }

    public function getToken(): string
    {
        /** @var UserTokenService $userTokenService */
        $userTokenService = app(UserTokenService::class);
        return $userTokenService->create($this);
    }
}
