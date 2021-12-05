<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // CreatedAt

    /**
     * @param $value
     * @return Carbon|false
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timezone('Europe/Kiev');
    }

    /**
     * @param $value
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] =
            Carbon::parse($value, config('app.timezone'))->setTimezone('UTC');
    }

    // UpdatedAt

    /**
     * @param $value
     * @return Carbon|false
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timezone('Europe/Moscow');
    }

    /**
     * @param $value
     */
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] =
            Carbon::parse($value, config('app.timezone'))->setTimezone('UTC');
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPassword($token);
        $notification->createUrlUsing(function ($user, $token) {
            return url(route('user.password.reset', [
                'token' => $token,
                'email' => $user->email
            ]));
        });

        $this->notify($notification);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

}
