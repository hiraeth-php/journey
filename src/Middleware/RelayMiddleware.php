<?php

namespace Hiraeth\Journey;

use RuntimeException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Hiraeth;

/**
 *
 */
class RelayMiddleware
{
	const DEFAULT_PARSER = 'FastRoute\RouteParser\Std';
	const DEFAULT_GENERATOR = 'FastRoute\DataGenerator\GroupCountBased';
	const DEFAULT_DISPATCHER = 'FastRoute\Dispatcher\GroupCountBased';
	const DEFAULT_RESOLVER = 'Hiraeth\Journey\BrokerResolver';

	/**
	 *
	 */
	protected $app = NULL;


	/**
	 *
	 */
	protected $broker = NULL;


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
	 *
	 */
	public function __construct(Hiraeth\Application $app, Hiraeth\Configuration $config, Hiraeth\Broker $broker)
	{
		$this->app       = $app;
		$this->config    = $config;
		$this->broker    = $broker;
		$this->caching   = $this->app->getEnvironment('CACHING', TRUE);
		$this->cacheFile = $this->config->get('journey', 'cache_file', NULL);

		$parser     = $this->config->get('journey', 'parser', static::DEFAULT_PARSER);
		$generator  = $this->config->get('journey', 'generator', static::DEFAULT_GENERATOR);
		$dispatcher = $this->config->get('journey', 'dispatcher', static::DEFAULT_DISPATCHER);
		$resolver   = $this->config->get('journey', 'resolver', static::DEFAULT_RESOLVER);

		$this->broker->alias('FastRoute\RouteParser', $parser);
		$this->broker->alias('FastRoute\DataGenerator', $generator);
		$this->broker->alias('FastRoute\Dispatcher', $dispatcher);
		$this->broker->alias('Journey\Resolver', $resolver);

		if ($this->caching && !$this->cacheFile) {
			throw new RuntimeException(
				'Please specify "cache_file" in the journey configuration.'
			);
		}
	}


	public function __invoke(Request $request, Response $response, callable $next)
	{
		$configs = $this->config->get('*', 'journey', array());

		if ($this->caching && $this->app->hasFile($this->cacheFile)) {
			$data = require $this->app->getFile($this->cacheFile);

			if (!is_array($data)) {
				throw new RuntimeException(sprintf(
					'Invalid cache file at "%s", delete the file.',
					$cache
				));
			}

		} else {
			$collector = $this->broker->make('Journey\Collector');

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

			$data = $collector->getData();

			if ($this->caching) {
				file_put_contents(
					$this->app->getFile($this->cacheFile, TRUE),
					sprintf('<?php return %s;', var_export($data, TRUE))
				);
			}
		}

		$router     = $this->broker->make('Journey\Router');
		$dispatcher = $this->broker->make('FastRoute\Dispatcher', [':data' => $data]);

		foreach (array_keys($configs) as $config) {
			$transformers = $this->config->get($config, 'journey.transformers', array());

			foreach ($transformers as $type => $transformer) {
				$router->addTransformer($type, $this->broker->make($transformer));
			}
		}

		return $next($request, $router->run($request, $response, $dispatcher));
	}
}
