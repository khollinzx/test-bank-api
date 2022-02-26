<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use App\Models\Role;
use App\Models\User;
use App\Services\JsonAPIResponse;
use Closure;
use Illuminate\Http\Request;

class AuthenticateCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedAccess = [Role::getRolesByName(Role::$CUSTOMER)->id];
        $method = $request->getMethod();
        $value = json_decode((new OauthAccessToken())::retrieveOauthProvider(explode(' ',$request->header("authorization"))[1]));
        if($value->guard !== 'customer')
            return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");

        $User = User::find($value->user_id);

        switch ($method)
        {
            case "POST":
            case "GET":
            case "PATCH":
            case "DELETE":

                /**
                 * restrict the user from certain actions
                 */
                if(isset($User->role_id) && !in_array($User->role_id, $allowedAccess))
                    return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");
                break;
        }
        return $next($request);
    }
}
