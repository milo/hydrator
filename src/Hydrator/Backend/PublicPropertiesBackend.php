<?php

namespace Milo\Hydrator\Backend;

use DateTime;
use DateTimeZone;
use Exception;
use Milo\Hydrator;
use Milo\Hydrator\Type;
use ReflectionClass;
use ReflectionProperty;


/**
 * This backend is used for hydration into all public properties of class.
 */
class PublicPropertiesBackend implements Hydrator\IHydratorBackend
{
	use Hydrator\Strict;

	private $propertiesCache = [];

	private $typesCache = [];


	/**
	 * @param  string
	 * @param  mixed[]
	 * @return object
	 */
	public function createInstance($class, array $args = [])
	{
		$argsWithoutKeys = array_values($args);

		return new $class(...$argsWithoutKeys);
	}


	/**
	 * @param  string
	 * @return string[]
	 */
	public function getProperties($class)
	{
		$properties = & $this->propertiesCache[$class];
		if ($properties === NULL) {
			$properties = [];

			$rc = new ReflectionClass($class);
			foreach ($rc->getProperties(ReflectionProperty::IS_PUBLIC) as $rp) {
				if ($rp->isStatic()) {
					continue;
				}
				$properties[] = $rp->getName();
			}
		}

		return $properties;
	}


	/**
	 * @param  string
	 * @param  string
	 * @return Type[]
	 * @throws Hydrator\BackendException
	 */
	public function getPropertyTypes($class, $property)
	{
		$types = & $this->typesCache["$class::$property"];
		if ($types === NULL) {
			$rp = new ReflectionProperty($class, $property);

			preg_match_all('#@var\s+([^\s*]+)#mi', $rp->getDocComment(), $matches, PREG_SET_ORDER);
			$matchCount = count($matches);
			if ($matchCount === 0) {
				throw new InvalidAnnotationException("Missing @var annotation for $class::\$$property property.");
			} elseif ($matchCount !== 1) {
				throw new InvalidAnnotationException("Multiple @var annotations for $class::\$$property property. Exactly one required.");
			}

			$types = [];
			foreach (explode('|', $matches[0][1]) as $typeStr) {
				$type = new Type;

				while (substr($typeStr, -2) === '[]') {
					$type->dimensionCount++;
					$typeStr = substr($typeStr, 0, -2);
				}

				$lower = strtolower($typeStr);
				$type->isNullable = $lower === 'null';
				$type->isBuiltin = Type::isBuiltin($lower);
				$type->name = $type->isBuiltin
					? $lower
					: Helpers::expandClassName($typeStr, $rp->getDeclaringClass());

				$types[] = $type;
			}
		}

		return $types;
	}


	/**
	 * @param  Type $type
	 * @param  mixed
	 * @return bool
	 */
	public function composeValue(Type $type, & $value)
	{
		if ($type->isBuiltin) {
			return $this->handleBuiltin($type->name, $value);
		}

		if ($type->name === DateTime::class) {
			if ($value instanceof DateTime) {
				return TRUE;

			} elseif (is_numeric($value)) {
				$value = (new DateTime('@' . $value))->setTimezone(new DateTimeZone(date_default_timezone_get()));
				return TRUE;

			} elseif (is_string($value)) {
				try {
					$value = new DateTime($value);
					return TRUE;
				} catch (Exception $e) {
				}
			}

			return FALSE;
		}

		return $value instanceof $type->name;
	}


	/**
	 * @param  Type $type
	 * @param  mixed
	 * @return bool
	 */
	public function decomposeValue(Type $type, & $value)
	{
		if ($type->isBuiltin) {
			return $this->handleBuiltin($type->name, $value);
		}

		if ($type->name === DateTime::class && $value instanceof DateTime) {
			$format = ($tmp = substr($value->getTimezone()->getName(), 0, 1)) === '+' || $tmp === '-'
				? 'Y-m-d H:i:s P'
				: 'Y-m-d H:i:s T';
			$value = $value->format($format);
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param  object
	 * @param  string
	 * @param  mixed
	 * @return void
	 */
	public function setPropertyValue($object, $property, $value)
	{
		$object->{$property} = $value;
	}


	/**
	 * @param  object
	 * @param  string
	 * @return mixed
	 */
	public function getPropertyValue($object, $property)
	{
		return $object->{$property};
	}


	/**
	 * @param  string
	 * @param  mixed
	 * @return bool
	 */
	private function handleBuiltin($name, $value)
	{
		switch ($name) {
			case 'bool':
			case 'boolean':
				return is_bool($value);

			case 'int':
			case 'integer':
				return is_int($value);

			case 'float':
			case 'double':
				return is_float($value);

			case 'number':
				return is_int($value) || is_float($value);

			case 'string':
				return is_string($value);

			case 'array':
				return is_array($value);

			case 'resource':
				return is_resource($value);

			case 'mixed':
				return TRUE;

			case 'callable':
				return is_callable($value);

			default:
				return FALSE;
		}
	}

}
