<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'image',
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
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
        $date->setTimezone(config('app.timezone'));
        return $date;
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
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
        $date->setTimezone(config('app.timezone'));
        return $date;
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
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return mixed
     */
    public static function popular()
    {
        return self::withCount('products')->orderByDesc('products_count')->limit(5)->get();
    }
}
