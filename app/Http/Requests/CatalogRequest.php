<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class CatalogRequest extends FormRequest
{
    /**
     * С какой сущностью сейчас работаем: категория, бренд, товар
     * @var array
     */
    protected $entity = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return $this->createItem();
            case 'PUT':
            case 'PATCH':
                return $this->updateItem();
        }
    }

    /**
     * @return string[][]
     */
    protected function createItem()
    {
        return [
            'name' => [
                'required',
                'max:100',
            ],
            'slug' => [
                'required',
                'max:100',
                'unique:' . $this->entity['table'] . ',slug',
                'regex:~^[-_a-z0-9]+$~i',
            ],
            'image' => [
                'mimes:jpeg|jpg|png',
                'max:5000'
            ]
        ];
    }

    /**
     * @return \string[][]
     */
    protected function updateItem()
    {
        $model = $this->route($this->entity['name']);

        return [
            'name' => [
                'required',
                'max:100',
            ],
            'slug' => [
                'required',
                'max:100',
                // проверка на уникальность slug, исключая эту сущность по идентифкатору
                'unique:' . $this->entity['table'] . ',slug,' . $model->id . ',id',
                'regex:~^[-_a-z0-9]+$~i',
            ],
            'image' => [
                'mimes:jpeg,jpg,png',
                'max:5000'
            ],
        ];
    }
}
