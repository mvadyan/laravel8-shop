<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'content',
        'image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

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
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return mixed
     */
    public static function roots()
    {
        return self::where('parent_id', 0)->with('children')->get();
    }

    /**
     * @param $id
     * @return bool
     */
    public function validParent($id): bool
    {
        $id = (integer)$id;
        $ids = $this->getAllChildren($this->id);
        $ids[] = $this->id;

        return !in_array($id, $ids);
    }

    /**
     * @param $id
     * @return array
     */
    public static function getAllChildren($id): array
    {
        $children = self::where('parent_id', $id)->with('children')->get();
        $ids = [];

        foreach ($children as $child) {
            $ids[] = $child->id;

            if ($child->children->count()) {
                $ids = array_merge($ids, self::getAllChildren($child->id));
            }
        }

        return $ids;
    }
}
