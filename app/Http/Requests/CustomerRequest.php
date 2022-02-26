<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [];

        switch (basename($this->url()))
        {
            case "customerLogin":
                $validation = $this->handleLoginValidation();
        }

        return $validation;
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleLoginValidation(): array
    {
        return [
            'email' => 'required|email',
            "password" => 'required|string',
        ];
    }
}
