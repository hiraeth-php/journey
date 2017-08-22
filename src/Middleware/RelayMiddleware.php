<?php

namespace Hiraeth\Journey;

use RuntimeException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Journey\Router;
use FastRoute\Dispatcher;

/**
 *
 */
class RelayMiddleware
{
	/**
	 *
	 */
	protected $dispathcer = NULL;


	/**
	 *
	 */
	protected $router = NULL;


	/**
	 *
	 */
	public function __construct(Router $router, Dispatcher $dispatcher)
	{
		$this->router     = $router;
		$this->dispatcher = $dispatcher;
	}


	public function __invoke(Request $request, Response $response, callable $next)
	{
		return $next($request, $this->router->run($request, $response, $this->dispatcher));
	}
}
