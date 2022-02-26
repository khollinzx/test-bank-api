<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function welcome(): string
    {
        return "Welcome to Fake Bank ".env("APP_ENV")." API Version 1";
    }

    public function admin(): string
    {
        return "Welcome Admin";
    }

    public function user(): string
    {
        return "Welcome Customer";
    }

    /**
     * This returns a signed in User Id
     * @return mixed
     */
    public function getUserId()
    {
        return auth()->id();
    }

    public function getUser()
    {
        return auth()->user();
    }
}
