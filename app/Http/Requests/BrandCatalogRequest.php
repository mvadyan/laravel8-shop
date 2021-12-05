<?php

namespace App\Http\Requests;

class BrandCatalogRequest extends CatalogRequest
{
    /**
     * @var string[]
     */
    protected $entity = [
        'name' => 'brand',
        'table' => 'brands'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * @return array|\string[][]
     */
    protected function createItem()
    {
        $rules = [];
        return array_merge(parent::createItem(), $rules);
    }

    /**
     * @return array|\string[][]
     */
    protected function updateItem()
    {
        $rules = [];
        return array_merge(parent::updateItem(), $rules);
    }
}
