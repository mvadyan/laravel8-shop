<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;

class Page extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'slug',
        'content',
        'parent_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function getRouteKeyName()
    {
        $current = \Illuminate\Support\Facades\Route::currentRouteName();

        if ('page.show' == $current) {
            return 'slug';
        }

        return 'id';
    }
}
