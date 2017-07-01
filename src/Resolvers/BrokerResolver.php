<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Journey;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class BrokerResolver implements Journey\Resolver
{
	/**
	 *
	 */
	public function __construct(Hiraeth\Broker $broker)
	{
		$this->broker = $broker;
	}


	/**
	 *
	 */
	public function execute(Journey\Router $router, $target)
	{
		$request  = $router->getRequest();
		$response = $router->getResponse();
		$params   = $request->getAttributes();
		$query    = $request->getQueryParams();

		if (preg_match_all('#{([^}]+)}#', $target, $matches)) {
			foreach ($matches[0] as $i => $token) {
				if (!isset($params[$matches[1][$i]])) {
					throw new RuntimeException(sprintf(
						'Token %s cannot be replaced in target, missing parameter',
						$token
					));
				}

				$target = str_replace($token, $params[$matches[1][$i]], $target);
			}
		}

		if (strpos($target, '::') === FALSE) {
			list($class, $method) = [$target, '__invoke'];
		} else {
			list($class, $method) = explode('::', $target);
		}

		if (isset($query['action'])) {
			$method = $query['action'];
		}

		if (!method_exists($class, $method)) {
			$response = $response->withStatus(404);

		} else {
			$controller = $this->broker->make($class, [':router' => $router]);

			if (!is_callable([$controller, $method])) {
				$response = $response->withStatus(404);

			} else {
				$args = array();

				foreach ($params as $key => $value) {
					$args[':' . $key] = $value;
				}

				$response = $this->handle($this->broker->execute([$controller, $method], $args), $response);
			}
		}

		return $response;
	}


	/**
	 *
	 */
	protected function handle($output, $response)
	{
		if ($output instanceof Response) {
			$response = $output;

		} elseif (is_string($output)) {
			$stream = $this->broker->make('Psr\Http\Message\StreamInterface');

			$stream->write($output);

			$response = $response->withBody($stream);
		}

		return $response;
	}
}
