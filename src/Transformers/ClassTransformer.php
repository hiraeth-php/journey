<?php

namespace Hiraeth\Journey;

use Hiraeth;
use Journey;
use ICanBoogie\Inflector;

/**
 *
 */
class ClassTransformer implements Journey\Transformer
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
		return $this->inflector->camelize($value, $this->inflector::UPCASE_FIRST_LETTER);
	}


	/**
	 *
	 */
	public function toUrl($name, $value, array $context = array())
	{
		return $this->inflector->hyphenate($value);
	}
}
