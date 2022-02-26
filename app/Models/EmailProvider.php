<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailProvider extends Model
{
    use HasFactory;

    const Mailgun = "Mailgun";
    const Sendgrid = "Sendgrid";

    protected $fillable = [
        'id',
        'name',
        'class',
        'is_active'
    ];

    /**
     * @param string $name
     * @return mixed
     */
    private function checkByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This fetches all the records
     * @return mixed
     */
    public static function getAllEmailProviders()
    {
        return self::all();
    }

    public static function findById(int $id)
    {
        return self::find($id);
    }

    /**
     * @param string $name
     * @param bool $setAsDefault
     */
    private function initializeProvider(string $name, bool $setAsDefault)
    {
        $service = new self();
        $service->name = ucwords($name);
        $service->class = ucwords(str_replace(' ', '', $name))."Service";
        $service->is_active = $setAsDefault;
        $service->save();
    }

    /**
     * this runs when the migration table is been triggered
     */
    public function createInitializeNewServiceProviders()
    {
        $services = [ self::Sendgrid, self::Mailgun ];

        collect($services)->each(function ($service)
        {
            if(!$this->checkByName($service))
                if($service === 'Sendgrid')
                    $this->initializeProvider($service, 1);

                else
                    $this->initializeProvider($service, 0);
        });
    }

    /**
     * This helps to clear the default Email Provider
     */
    private function clearDefaultProvider()
    {
        $active_provider = $this->getActiveProvider();
        if($active_provider)
            $this->updateProviderActiveStatus($active_provider);
    }

    /**
     * This should be called from Admin ISP
     * @param EmailProvider $provider
     */
    public function setDefaultProvider(self $provider)
    {
        #clear the previous default provider
        $this->clearDefaultProvider();

        #Set to default
        $this->updateProviderActiveStatus($provider);
    }

    /**
     * @return mixed
     */
    public function getActiveProvider()
    {
        return self::where('is_active', 1)->latest()->first();
    }

    /**
     * This updates the active status of a Provider
     * @param EmailProvider $provider
     */
    private function updateProviderActiveStatus(self $provider)
    {
        $provider->is_active = $provider->is_active? 0 : 1;
        $provider->save();
    }
}
