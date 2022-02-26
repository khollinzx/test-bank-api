<?php

namespace App\Http\Requests;

use App\Services\JsonAPIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This overrides the default throwable failed message in json format
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(JsonAPIResponse::sendErrorResponse($validator->errors()->first()));
    }
}
