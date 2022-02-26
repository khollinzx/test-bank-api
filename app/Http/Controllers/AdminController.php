<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountProcessRequest;
use App\Models\Account;
use App\Models\User;
use App\Services\Helpers;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $userModel;
    protected $accountModel;

    /**
     * CategoryController constructor.
     * @param User $user
     * @param Account $account
     */
    public function __construct(User $user, Account $account)
    {
        $this->userModel = $user;
        $this->accountModel = $account;
    }

    public function createCustomer(AccountProcessRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $password = Helpers::generateRandomStrings(8);
            $fields = [
                "name" => $validated["name"],
                "email" => $validated["email"],
                "bvn" => $validated["bvn"],
                "phone" => $validated["phone"],
            ];

            $User = $this->userModel::CreateNewAdminAccount($fields, $password);

            $AccountDetails = $this->accountModel::CreateAccountForCustomer($User->id);

            $this->accountModel::sendCustomerCredentials($AccountDetails,true, $password);

            return JsonAPIResponse::sendSuccessResponse("Account created successfully", $AccountDetails);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    public function createSubAccountForExistingCustomer(AccountProcessRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {

            $User = $this->userModel::getUserByBVN($validated["bvn"]);

            $AccountDetails = $this->accountModel::CreateAccountForCustomer($User->id);

            $this->accountModel::sendCustomerCredentials($AccountDetails, false);
            return JsonAPIResponse::sendSuccessResponse("Account created successfully", $AccountDetails);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getCustomers(): JsonResponse
    {
        try {
            if(!$this->userModel->fetchAllCustomer())
                return JsonAPIResponse::sendErrorResponse("No Records Found");

            $Customers = $this->userModel->fetchAllCustomer();
            if(count($Customers))
                return JsonAPIResponse::sendSuccessResponse("All Customers", $Customers);

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }
}
