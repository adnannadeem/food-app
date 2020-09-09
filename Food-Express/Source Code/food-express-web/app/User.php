<?php

namespace App;

use App\Enums\BalanceType;
use App\Models\Balance;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use Notifiable, HasMediaTrait, HasModelEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'phone', 'address', 'roles', 'device_token', 'status', 'applied',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('user'))) {
            return asset($this->getFirstMediaUrl('user'));
        }
        return asset('assets/img/default/user.png');
    }

    public function OnModelCreating()
    {
        $balance               = new Balance();
        $balance->name         = $this->username;
        $balance->type         = BalanceType::REGULAR;
        $balance->balance      = 0;
        $balance->creator_type = 1;
        $balance->creator_id   = 1;
        $balance->editor_type  = 1;
        $balance->editor_id    = 1;
        $balance->save();

        $this->balance_id = $balance->id;
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }
}
