<?php

namespace Milo\Hydrator;


/**
 * PHP type representation.
 */
class Type
{
	/** @var string */
	public $name;

	/** @var bool */
	public $isBuiltin;

	/** @var int */
	public $dimensionCount = 0;

	/** @var bool */
	public $isNullable;

	/** @var mixed */
	public $meta;


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->name . str_repeat('[]', $this->dimensionCount);
	}


	/**
	 * @param  string
	 * @return bool
	 */
	public static function isBuiltin($type)
	{
		static $builtinTypes = [
			'null' => 1,
			'bool' => 1, 'boolean' => 1,
			'int' => 1, 'integer' => 1, 'float' => 1, 'double' => 1, 'number' => 1,
			'string' => 1,
			'resource' => 1,
			'mixed' => 1,
			'callable' => 1,
		];

		return isset($builtinTypes[$type]);
	}

}
