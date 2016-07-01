<?php

namespace Milo\Hydrator;


interface IHydratorBackend
{

	/**
	 * Creates instance of given $class.
	 * @param  string
	 * @param  mixed[]
	 * @return object
	 */
	function createInstance($class, array $args = []);


	/**
	 * Returns all class properties to be hydrated.
	 * @param  string
	 * @return string[]
	 */
	function getProperties($class);


	/**
	 * Returns type of given property in syntax like ['float[]' => $meta, 'int[]' => $meta, 'NULL' => $meta].
	 * @param  string
	 * @param  string
	 * @return Type[]
	 */
	function getPropertyTypes($class, $property);


	/**
	 * Converts $value to given $type during hydration.
	 * @param  Type
	 * @param  mixed
	 * @return bool  conversion was possible or not
	 */
	function composeValue(Type $type, & $value);


	/**
	 * Converts $value from given $type during export.
	 * @param  Type
	 * @param  mixed
	 * @return bool  conversion was possible or not
	 */
	function decomposeValue(Type $type, & $value);


	/**
	 * Sets $property to given $value for given $object.
	 * @param  object
	 * @param  string
	 * @param  mixed
	 * @return void
	 */
	function setPropertyValue($object, $property, $value);


	/**
	 * @param  object
	 * @param  string
	 * @return mixed
	 */
	function getPropertyValue($object, $property);

}
