<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\CustomerRequest;
use App\Models\Admin;
use App\Models\OauthAccessToken;
use App\Models\User;
use App\Services\Helpers;
use App\Services\JsonAPIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    /**
     * @param User $user
     * @param Admin $admin
     */
    public function __construct(User $user, Admin $admin)
    {
        $this->userModel = $user;
        $this->adminModel = $admin;
    }

    /**
     * @param AdminRequest $request
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminLogin(AdminRequest $request, string $guard = 'admin'){

        $validated = $request->validated();

        try {

            if(!Auth::guard($guard)->attempt($validated))
                return JsonAPIResponse::sendErrorResponse('Invalid login credentials.');

            /**
             * Get the User Account and create access token
             */
            $admin = $this->adminModel::getRecordByColumnAndValue( 'email', $validated['email']);

            if(!$admin->is_active)
                return JsonAPIResponse::sendErrorResponse('Your Account is yet to be activated.');

            $LoginRecord = OauthAccessToken::createAccessToken($admin, $guard);

            return JsonAPIResponse::sendSuccessResponse('Login succeeded', $LoginRecord);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }

    }

    /**
     * @param CustomerRequest $request
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerLogin(CustomerRequest $request, string $guard = 'customer'){

        $validated = $request->validated();

        try {

            if(!Auth::guard($guard)->attempt($validated))
                return JsonAPIResponse::sendErrorResponse('Invalid login credentials.');

            /**
             * Get the User Account and create access token
             */
            $customer = $this->userModel::getRecordByColumnAndValue( 'email', $validated['email']);

            if(!$customer->is_active)
                return JsonAPIResponse::sendErrorResponse('Your Account is yet to be activated.');

            $LoginRecord = OauthAccessToken::createAccessToken($customer, $guard);

            return JsonAPIResponse::sendSuccessResponse('Login succeeded', $LoginRecord);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }

    }
}
