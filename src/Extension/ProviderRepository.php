<?php namespace Orchestra\Extension;

use Illuminate\Foundation\Application;

class ProviderRepository
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = array();

    /**
     * Construct a new finder.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Load available service providers.
     *
     * @param  array    $services
     * @return void
     */
    public function provides(array $services)
    {
        foreach ($services as $service) {
            // Register service provider as a service for
            // Illuminate\Foundation\Application.
            $this->app->register($service);

            $this->services[] = $service;
        }
    }
}
