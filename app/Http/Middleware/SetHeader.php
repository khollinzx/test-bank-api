<?php

namespace App\Http\Middleware;

use App\Services\JsonAPIResponse;
use Closure;
use Illuminate\Http\Request;

class SetHeader
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->hasHeader('accept') || $request->header('accept') !== "application/json")
            return JsonAPIResponse::sendErrorResponse('The Header is required to have {Accept: application/json}');

        return $next($request);
    }
}
