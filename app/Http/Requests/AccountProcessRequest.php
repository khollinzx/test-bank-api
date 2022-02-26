<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AccountProcessRequest extends BaseRequest
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
            case "create":
                $validation = $this->handleCreateValidation();
                break;

            case "subCreate":
                $validation = $this->handleCreateSubAccountValidation();
                break;

            case "transfer":
                $validation = $this->handleTransferValidation();
                break;
        }

        return $validation;
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleCreateValidation(): array
    {
        return [
            "name" => 'required|string',
            "email" => 'required|email|unique:users,email',
            "bvn" => 'required|min:10|max:10|unique:users,bvn',
            "phone" => [
                "required",
                function ($k, $v, $fn) {
                    if (User::checkIfNumberExist($k, $v))
                        return $fn('Phone number already exist.');
                }
            ]
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleCreateSubAccountValidation(): array
    {
        return [
            "bvn" => 'required|min:10|max:10|exists:users,bvn',
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleTransferValidation(): array
    {
        return [
            "transferred_account_no" => 'required|integer|exists:accounts,account_no',
            "receiver_account_no" => 'required|integer|exists:accounts,account_no',
            "amount" => [
                "required",
                function ($k, $v, $fn) {
                    if ( $v < 100)
                        return $fn('transfer range is 100 and above');
                }
            ]
        ];
    }
}
