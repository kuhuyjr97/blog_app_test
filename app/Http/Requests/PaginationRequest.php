<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    
  
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page'=> 'int',
            'per_page'=> 'int',
        ];
    }
}
