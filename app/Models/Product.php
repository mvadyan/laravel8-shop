<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'content',
        'slug',
        'image',
        'price',
        'new',
        'hit',
        'sale',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // CreatedAt

    /**
     * @param $builder
     * @param $id
     * @return mixed
     */
    public function scopeCategoryProducts($builder, $id)
    {
        $descendants = Category::getAllChildren($id);
        $descendants[] = $id;
        return $builder->whereIn('category_id', $descendants);
    }

    /**
     * @param $builder
     * @param $filters
     * @return mixed
     */
    public function scopeFilterProducts($builder, $filters)
    {
        return $filters->apply($builder);
    }

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
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsToMany
     */
    public function baskets(): BelongsToMany
    {
        return $this->belongsToMany(Basket::class)->withPivot('quantity');
    }
}
