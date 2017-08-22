<?php

namespace Hiraeth\Journey;

use Hiraeth;

/**
 * Delegates are responsible for constructing dependencies for the dependency injector.
 */
class GCBDispatcherDelegate implements Hiraeth\Delegate
{
	/**
	 *
	 */
	protected $app = NULL;


	/**
	 *
	 */
	protected $config = NULL;


	/**
	 *
	 */
	protected $caching = TRUE;


	/**
	 *
	 */
	protected $cacheFile = NULL;


	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	static public function getClass()
	{
		return 'FastRoute\Dispatcher\GroupCountBased';
	}


	/**
	 * Get the interfaces for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return array A list of interfaces for which the delegate provides a class
	 */
	static public function getInterfaces()
	{
		return ['FastRoute\Dispatcher'];
	}


	/**
	 *
	 */
	public function __construct(Hiraeth\Application $app, Hiraeth\Configuration $config)
	{
		$this->app       = $app;
		$this->config    = $config;
		$this->caching   = $app->getEnvironment('CACHING', TRUE);
		$this->cacheFile = $config->get('journey', 'cache_file', NULL);

		if ($this->caching && !$this->cacheFile) {
			throw new RuntimeException(
				'Please specify "cache_file" in the journey configuration.'
			);
		}
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Broker $broker The dependency injector instance
	 * @return Object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Broker $broker)
	{
		$class = static::getClass();

		if ($this->caching && $this->app->hasFile($this->cacheFile)) {
			$data = require $this->app->getFile($this->cacheFile);

			if (!is_array($data)) {
				throw new RuntimeException(sprintf(
					'Invalid cache file at "%s", delete the file.',
					$this->cacheFile
				));
			}

		} else {
			$collector = $broker->make('Journey\Collector');
			$data      = $collector->getData();

			if ($this->caching) {
				file_put_contents(
					$this->app->getFile($this->cacheFile, TRUE),
					sprintf('<?php return %s;', var_export($data, TRUE))
				);
			}
		}

		return new $class($data);
	}
}
