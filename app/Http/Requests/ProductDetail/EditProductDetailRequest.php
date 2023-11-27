<?php

namespace App\Http\Requests\ProductDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditProductDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            "color" => [
                "required",
                // Rule::unique("product_details")->ignore($this->id)
            ],
            'size' => 'required',
            'qty' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'color.required' => 'Màu sắc không được để trống',
            'size.required' => 'Dung lượng không được để trống',
            'qty.required' => 'Số lượng không được để trống',
        ];
    }
}
