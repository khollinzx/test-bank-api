<?php

namespace App\Providers;

use App\Abstractions\Interfaces\EmailProviderInterface;
use App\Models\EmailProvider;
use App\Services\EmailService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MailServiceProvider extends ServiceProvider
{
    protected $emailProvider;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindEmailServiceProviderByClassName();
    }

    /**
     * This binds a Mail Service Provider through the Defined Class
     * @return mixed|void
     */
    private function bindEmailServiceProviderByClassName()
    {
        $this->app->when(EmailService::class)
            ->needs(EmailProviderInterface::class)
            ->give(function ()
            {
                #Get the active service provider from Database
                $active_provider = (new EmailProvider())->getActiveProvider();
                if($active_provider)
                {
                    $class = Str::of(trim($active_provider->class?? 'SendgridService'))->studly();
                    $service = "\App\Abstractions\Implementations\EmailProviders\\" . $class;

                    if(class_exists($service))
                    {
                        Config::set('mail.provider', $service);

                        return new $service();
                    }
                }
            });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(EmailProvider $emailProvider)
    {
        $this->emailProvider = $emailProvider;
    }
}
