<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BasketService extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'basketService';
    }
}
