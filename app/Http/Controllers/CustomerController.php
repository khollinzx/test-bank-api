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

class CustomerController extends Controller
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

    public function transferMoney(AccountProcessRequest $request): JsonResponse
    {
        $customerId = $this->getUserId();
        $validated = $request->validated();
        try {
            $sendAccount = $this->accountModel::getRecordByColumnAndValue($customerId, 'account_no', $validated["transferred_account_no"]);

            if($sendAccount->amount < $validated["amount"])
                return JsonAPIResponse::sendErrorResponse('Insufficient fund');

            $ReceiverAccount = $this->accountModel::getRecordByAccountNumber('account_no', $validated["receiver_account_no"]);

            $this->accountModel::performTransfer($sendAccount, $validated["amount"], false);

            $this->accountModel::performTransfer($ReceiverAccount, $validated["amount"]);

            return JsonAPIResponse::sendSuccessResponse("Transfer was successful", $sendAccount);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getCustomerAccounts(): JsonResponse
    {
        $customerId = $this->getUserId();
        try {
            if(!$this->accountModel->fetchAllAccounts($customerId))
                return JsonAPIResponse::sendErrorResponse("No Records Found");

            $Accounts = $this->accountModel->fetchAllAccounts($customerId);
            if(count($Accounts))
                return JsonAPIResponse::sendSuccessResponse("All Accounts", $Accounts);

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }
}
