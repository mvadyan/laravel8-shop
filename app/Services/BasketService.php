<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use \App\Models\Basket;

class BasketService
{
    /**
     * @return Model
     */
    public function getBasket(): Model
    {
        $basket_id = Request::cookie('basket_id');

        if (!empty($basket_id)) {
            try {
                $basket = Basket::findOrFail($basket_id);
            } catch (ModelNotFoundException $e) {
                $basket = Basket::create();
            }
        } else {
            $basket = Basket::create();
        }

        Cookie::queue('basket_id', $basket->id, 525600);

        return $basket;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        $basket_id = request()->cookie('basket_id');
        $count = 0;

        if (!empty($basket_id)) {
            try {
                $count = Basket::findOrFail($basket_id)->products->count();
            } catch (ModelNotFoundException $e) {
                return $count;
            }
        } else {
            return $count;
        }

        return $count;
    }

}
