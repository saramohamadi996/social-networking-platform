<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'likes_count' => ['nullable', 'integer', 'min:0'],
            'shares_count' => ['nullable', 'integer', 'min:0'],
            'view_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

