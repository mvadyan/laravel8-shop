<?php

namespace App\Http\Requests;


class ProductCatalogRequest extends CatalogRequest
{
    /**
     * @var string[]
     */
    protected $entity = [
        'name' => 'product',
        'table' => 'products',
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
     * @return \string[][]
     */
    protected function createItem()
    {
        $rules = [
            'category_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'brand_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'price' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];

        return array_merge(parent::createItem(), $rules);
    }

    /**
     * @return \string[][]
     */
    protected function updateItem()
    {
        $rules = [
            'category_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'brand_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'price' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];

        return array_merge(parent::updateItem(), $rules);
    }
}
