<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Stem\LinguaStemRu;

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

    /**
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeSearch($query, $search) {
        // обрезаем поисковый запрос
        $search = iconv_substr($search, 0, 64);
        // удаляем все, кроме букв и цифр
        $search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
        // сжимаем двойные пробелы
        $search = preg_replace('#\s+#u', ' ', $search);
        $search = trim($search);
        if (empty($search)) {
            return $query->whereNull('id'); // возвращаем пустой результат
        }
        // разбиваем поисковый запрос на отдельные слова
        $temp = explode(' ', $search);
        $words = [];
        $stemmer = new LinguaStemRu();
        foreach ($temp as $item) {
            if (iconv_strlen($item) > 3) {
                // получаем корень слова
                $words[] = $stemmer->stem_word($item);
            } else {
                $words[] = $item;
            }
        }
        $relevance = "IF (`products`.`name` LIKE '%" . $words[0] . "%', 2, 0)";
        $relevance .= " + IF (`products`.`content` LIKE '%" . $words[0] . "%', 1, 0)";
        $relevance .= " + IF (`categories`.`name` LIKE '%" . $words[0] . "%', 1, 0)";
        $relevance .= " + IF (`brands`.`name` LIKE '%" . $words[0] . "%', 2, 0)";
        for ($i = 1; $i < count($words); $i++) {
            $relevance .= " + IF (`products`.`name` LIKE '%" . $words[$i] . "%', 2, 0)";
            $relevance .= " + IF (`products`.`content` LIKE '%" . $words[$i] . "%', 1, 0)";
            $relevance .= " + IF (`categories`.`name` LIKE '%" . $words[$i] . "%', 1, 0)";
            $relevance .= " + IF (`brands`.`name` LIKE '%" . $words[$i] . "%', 2, 0)";
        }

        $query->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.*', DB::raw($relevance . ' as relevance'))
            ->where('products.name', 'like', '%' . $words[0] . '%')
            ->orWhere('products.content', 'like', '%' . $words[0] . '%')
            ->orWhere('categories.name', 'like', '%' . $words[0] . '%')
            ->orWhere('brands.name', 'like', '%' . $words[0] . '%');
        for ($i = 1; $i < count($words); $i++) {
            $query = $query->orWhere('products.name', 'like', '%' . $words[$i] . '%');
            $query = $query->orWhere('products.content', 'like', '%' . $words[$i] . '%');
            $query = $query->orWhere('categories.name', 'like', '%' . $words[$i] . '%');
            $query = $query->orWhere('brands.name', 'like', '%' . $words[$i] . '%');
        }
        $query->orderBy('relevance', 'desc');
        return $query;
    }
}
