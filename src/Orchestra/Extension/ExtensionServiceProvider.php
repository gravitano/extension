<?php namespace Orchestra\Extension;

use Exception;
use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() 
	{
		$this->registerExtension();
		$this->registerExtensionConfigManager();
		$this->registerExtensionFinder();
	}

	/**
	 * Register the service provider for Extension.
	 *
	 * @return void
	 */
	protected function registerExtension()
	{
		$this->app['orchestra.extension'] = $this->app->share(function ($app)
		{
			$provider   = new ProviderRepository($app);
			$dispatcher = new Dispatcher($app, $provider);

			return new Environment($app, $dispatcher);
		});
	}

	/**
	 * Register the service provider for Extension Config Manager.
	 *
	 * @return void
	 */
	protected function registerExtensionConfigManager()
	{
		$this->app['orchestra.extension.config'] = $this->app->share(function ($app)
		{
			return new ConfigManager($app);
		});
	}

	/**
	 * Register the service provider for Extension Finder.
	 *
	 * @return void
	 */
	protected function registerExtensionFinder()
	{
		$this->app['orchestra.extension.finder'] = $this->app->share(function ($app)
		{
			return new Finder($app);
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('orchestra/extension', 'orchestra/extension');
		$this->bootExtensionEvents();
	}

	/**
	 * Register on boot extension events.
	 *
	 * @return void
	 */
	protected function bootExtensionEvents()
	{
		$app = $this->app;

		$app->booting(function($app)
		{
			$env = $app['orchestra.extension'];

			$env->attach($app['orchestra.memory']->makeOrFallback());
			$env->boot();
		});

		$app->after(function() use ($app)
		{
			$app['orchestra.extension']->finish();
		});
	}
}
