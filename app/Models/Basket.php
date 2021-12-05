<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Basket extends Model
{
    use HasFactory;

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
     * @return BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    /**
     * @param $id
     * @param int $count
     */
    public function increase($id, int $count = 1)
    {
        $this->change($id, $count);
    }

    /**
     * @param $id
     * @param int $count
     */
    public function decrease($id, $count = 1)
    {
        $this->change($id, -1 * $count);
    }

    /**
     * @param $id
     * @param int $count
     */
    private function change($id, int $count = 0)
    {
        if ($count == 0) {
            return;
        }

        if ($this->products->contains($id)) {
            // получаем объект строки таблицы `basket_product`
            $pivotRow = $this->products()->where('product_id', $id)->first()->pivot;
            $quantity = $pivotRow->quantity + $count;

            if ($quantity > 0) {
                // обновляем количество товара $id в корзине
                $pivotRow->update(['quantity' => $quantity]);
            } else {
                // кол-во равно нулю — удаляем товар из корзины
                $pivotRow->delete();
            }
        } elseif ($count > 0) {
            // иначе — добавляем этот товар
            $this->products()->attach($id, ['quantity' => $count]);
        }

// обновляем поле `updated_at` таблицы `baskets`
        $this->touch();

    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        // удаляем товар из корзины (разрушаем связь)
        $this->products()->detach($id);
        // обновляем поле `updated_at` таблицы `baskets`
        $this->touch();
    }

    /**
     *
     */
    public function clear()
    {
        $this->products()->detach();
        $this->touch();
    }

    /**
     * @return float|int|mixed
     */
    public function getAmount()
    {
        $amount = 0.0;

        foreach ($this->products as $product) {
            $amount = $amount + $product->price * $product->pivot->quantity;
        }

        return $amount;
    }
}
