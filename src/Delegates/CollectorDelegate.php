<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Journey\Collector;

/**
 * Creates a new Journey\Collector
 */
class CollectorDelegate implements Hiraeth\Delegate
{
	const DEFAULT_PARSER = 'FastRoute\RouteParser\Std';
	const DEFAULT_GENERATOR = 'FastRoute\DataGenerator\GroupCountBased';

	/**
	 *
	 */
	protected $app = NULL;


	/**
	 *
	 */
	protected $config = NULL;


	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	static public function getClass()
	{
		return 'Journey\Collector';
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
		return [];
	}


	/**
	 *
	 */
	public function __construct(Hiraeth\Application $app, Hiraeth\Configuration $config)
	{
		$this->app    = $app;
		$this->config = $config;
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
		$configs   = $this->config->get('*', 'journey', array());
		$parser    = $this->config->get('journey', 'parser', static::DEFAULT_PARSER);
		$generator = $this->config->get('journey', 'generator', static::DEFAULT_GENERATOR);
		$collector = new Collector($broker->make($parser), $broker->make($generator));

		foreach (array_keys($configs) as $config) {
			$patterns = $this->config->get($config, 'journey.patterns', array());
			$group    = $this->config->get($config, 'journey.group', '');

			foreach ($patterns as $type => $pattern) {
				$collector->addPattern($type, $pattern);
			}

			$collector->addGroup($group, function($collector) use ($config) {
				$routes = $this->config->get($config, 'journey.routes', array());

				foreach ($routes as $route => $target) {
					$collector->any($route, $target);
				}
			});
		}

		return $collector;
	}
}
