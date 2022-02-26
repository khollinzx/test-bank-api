<?php

namespace App\Abstractions\Implementations\EmailProviders;

use App\Abstractions\AbstractClasses\EmailClass;
use App\Abstractions\Interfaces\EmailProviderInterface;
use Illuminate\Support\Facades\Log;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

class SendgridService extends EmailClass implements EmailProviderInterface
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return env('SENDGRID_API_KEY');
    }

    /**
     * @param array $data
     * @return \SendGrid
     */
    public function initialize(array $data = [])
    {
        return new \SendGrid(self::getKey());
    }

    /**
     * Add this to Queue in the future
     * @param array $emailConfig
     * @param string $bladeTemplate
     * @param array $bladeData
     * @param string $subject
     * @return mixed|void
     * @throws TypeException
     */
    public function sendMail(array $emailConfig, string $bladeTemplate, array $bladeData = [], string $subject = ''): bool
    {
        $email = new Mail();
        $email->setFrom($emailConfig['sender_email'], $emailConfig['sender_name']);
        $email->setSubject($emailConfig['subject']?? $subject);
        $email->addTo($emailConfig['recipient_email'], $emailConfig['recipient_name']);
        $html = $this->loadTemplateView($bladeTemplate, $bladeData);//fetch the content of the blade template
        $email->addContent( "text/html", $html);

        try {

            $response = self::initialize()->send($email);
//            print $response->statusCode() . "\n";
//            print_r($response->headers());
//            print $response->body() . "\n";

            return true;

        } catch (TypeException $e) {
            Log::error($e);

            return false;
        }
    }

}
