<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'comment',
        'amount',
        'status',
    ];

    public const STATUSES = [
        0 => 'Новый',
        1 => 'Обработан',
        2 => 'Оплачен',
        3 => 'Доставлен',
        4 => 'Завершен',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param $value
     * @return Carbon|false
     */
    public function getCreatedAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timezone('Europe/Kiev');
    }

    /**
     * @param $value
     * @return Carbon|false
     */
    public function getUpdatedAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timezone('Europe/Kiev');
    }
}
