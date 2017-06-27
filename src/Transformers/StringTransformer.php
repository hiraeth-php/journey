<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Journey;
use ICanBoogie\Inflector;

/**
 *
 */
class StringTransformer implements Journey\Transformer
{
	/**
	 *
	 */
	protected $inflector = NULL;


	/**
	 *
	 */
	public function __construct(Inflector $inflector)
	{
		$this->inflector = $inflector;
	}


	/**
	 *
	 */
	public function fromUrl($name, $value, array $context = array())
	{
		return $value;
	}


	/**
	 *
	 */
	public function toUrl($name, $value, array $context = array())
	{
		return $this->inflector->hyphenate(preg_replace('#[^a-zA-Z0-9]#', '', ucwords($value)));
	}
}
