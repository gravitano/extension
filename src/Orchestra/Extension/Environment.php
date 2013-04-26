<?php namespace Orchestra\Extension;

use Exception;
use Orchestra\Memory\Drivers\Driver as MemoryDriver;

class Environment {

	/**
	 * Application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app = null;

	/**
	 * Dispatcher instance.
	 *
	 * @var Orchestra\Extension\Dispatcher
	 */
	protected $dispatcher = null;

	/**
	 * Memory instance.
	 *
	 * @var Orchestra\Memory\Drivers\Driver
	 */
	protected $memory = null;

	/**
	 * List of extensions.
	 *
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * Construct a new Application instance.
	 *
	 * @access public
	 * @param  Illuminate\Foundation\Application    $app
	 * @param  Orchestra\Extension\Dispatcher       $dispatcher
	 * @return void
	 */
	public function __construct($app, Dispatcher $dispatcher)
	{
		$this->app        = $app;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Set memory provider.
	 *
	 * @access public
	 * @return self
	 */
	public function attach(MemoryDriver $memory)
	{
		$this->memory = $memory;

		return $this;
	}
	
	/**
	 * Boot active extensions.
	 *
	 * @access public
	 * @return void
	 */
	public function boot()
	{
		$memory     = $this->memory;
		$availables = $memory->get('extensions.available', array());
		$actives    = $memory->get('extensions.active', array());

		foreach ($actives as $name => $options)
		{
			if (isset($availables[$name]))
			{
				$config = array_merge(
					(array) array_get($availables, "{$name}.config"), 
					(array) array_get($options, "config")
				);

				array_set($options, "config", $config);
				$this->extensions[$name] = $options;
				$this->dispatcher->start($name, $options);
			}
		}

		return $this;
	}

	/**
	 * Shutdown all Extensions.
	 *
	 * @access public
	 * @return void
	 */
	public function finish()
	{
		foreach ($this->extensions as $name => $options)
		{
			$this->dispatcher->finish($name, $options);
		}

		$this->extensions = array();
	}

	/**
	 * Activate an extension.
	 *
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public function activate($name)
	{
		$memory     = $this->memory;
		$availables = $memory->get('extensions.available', array());
		$actives    = $memory->get('extensions.active', array());

		if (isset($availables[$name]))
		{
			// Append the activated extension to active extensions, and also
			// publish the extension (migrate the database and publish the
			// asset).
			$this->extensions[$name] = $actives[$name] = $availables[$name];
			$this->publish($name);
		}

		$memory->put('extensions.active', $actives);
	}

	/**
	 * Deactivate an extension
	 *
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public function deactivate($name)
	{
		$memory  = $this->memory;
		$current = $memory->get('extensions.active', array());
		$actives = array();

		foreach ($current as $extension => $config)
		{
			if ($extension === $name) continue;
		
			$actives[$extension] = $config;
		}

		$memory->put('extensions.active', $actives);
	}

	/**
	 * Publish an extension.
	 *
	 * @access public
	 * @param  string
	 * @return void
	 */
	public function publish($name)
	{
		$this->app['orchestra.publisher.migrate']->extension($name);
		$this->app['orchestra.publisher.asset']->extension($name);

		$this->app['events']->fire("orchestra.publishing", array($name));
		$this->app['events']->fire("orchestra.publishing: {$name}");	
	}

	/**
	 * Check if extension is started
	 *
	 * @access public
	 * @param  string   $name
	 * @return bool
	 */
	public function started($name)
	{
		return (array_key_exists($name, $this->extensions));
	}

	/**
	 * Get an option for a given extension.
	 *
	 * @access public
	 * @param  string   $name
	 * @param  string   $option
	 * @param  mixed    $default
	 * @return mixed
	 */
	public function option($name, $option, $default = null)
	{
		if ( ! isset($this->extensions[$name]))
		{
			return value($default);
		}

		return array_get($this->extensions[$name], $option, $default);
	}

	/**
	 * Check whether an extension is available.
	 *
	 * @access public
	 * @param  string   $name
	 * @return boolean
	 */
	public function isAvailable($name)
	{	
		$memory = $this->memory;
		return (is_array($memory->get("extensions.available.{$name}")));
	}

	/**
	 * Check whether an extension is active.
	 *
	 * @access public
	 * @param  string   $name
	 * @return boolean
	 */
	public function isActive($name)
	{
		$memory = $this->memory;
		return (is_array($memory->get("extensions.active.{$name}")));
	}

	/**
	 * Detect all extensions.
	 *
	 * @access public
	 * @return array
	 */
	public function detect()
	{
		$extensions = $this->app['orchestra.extension.finder']->detect();
		$this->memory->put('extensions.available', $extensions);

		return $extensions;
	}
}
