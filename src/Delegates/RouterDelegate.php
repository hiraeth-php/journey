<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Journey\Router;

/**
 * Delegates are responsible for constructing dependencies for the dependency injector.
 */
class RouterDelegate implements Hiraeth\Delegate
{
	const DEFAULT_RESOLVER = 'Hiraeth\Journey\BrokerResolver';

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
		return 'Journey\Router';
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
	public function __construct(Hiraeth\Configuration $config)
	{
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
		$configs  = $this->config->get('*', 'journey', array());
		$resolver = $this->config->get('journey', 'resolver', static::DEFAULT_RESOLVER);
		$router   = new Router($broker->make($resolver));

		foreach (array_keys($configs) as $config) {
			$transformers = $this->config->get($config, 'journey.transformers', array());
			$masks        = $this->config->get($config, 'journey.masks', array());

			foreach ($transformers as $type => $transformer) {
				$router->addTransformer($type, $broker->make($transformer));
			}

			foreach ($masks as $from => $to) {
				$router->addMask($from, $to);
			}
		}

		return $router;
	}
}
