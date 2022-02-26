<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminRequest extends BaseRequest
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
            case "AdminLogin":
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
