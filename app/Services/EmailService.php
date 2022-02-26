<?php

namespace App\Services;

use App\Models\EmailProvider;
use Illuminate\Support\Str;

class EmailService
{
    /**
     * @var
     */
    protected $emailProvider;

    /**
     * @var string|null
     */
    var $MAIL_SERVICE = null;

    public function __construct()
    {
        $this->MAIL_SERVICE = Str::of(trim(config('mail.provider')))->studly();//get the default service
        $this->setProvider();
    }

    private function setProvider(): void
    {
        #Get the active service provider from Database
        $active_provider = (new EmailProvider())->getActiveProvider();
        if($active_provider)
            $this->MAIL_SERVICE = Str::of(trim($active_provider->class))->studly();

        $service = "\App\Abstractions\Implementations\EmailProviders\\" . $this->MAIL_SERVICE;
        if(class_exists($service)) {
            $this->MAIL_SERVICE = new $service();
//            Log::info("Sending email through: $service");
        }
    }

    /**
     * This gets the current active Mail service provider
     * @return mixed
     */
    public function getProvider()
    {
        return new $this->MAIL_SERVICE();
    }
}
