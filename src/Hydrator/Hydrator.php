<?php

namespace Milo\Hydrator;


class Hydrator
{
	use Strict;

	/** @var IHydratorBackend */
	private $backend;

	/** @var string[] */
	private $path;


	public function __construct(IHydratorBackend $backend)
	{
		$this->backend = $backend;
	}


	/**
	 * @param  string|object
	 * @param  array
	 * @param  array
	 * @return mixed
	 * @throws HydrationException
	 */
	public function hydrate($class, array $data, array $path = ['$data'])
	{
		$this->path = $path;

		if (is_object($class)) {
			$object = $class;
			$class = get_class($object);

		} else {
			$object = $this->backend->createInstance($class, $data);
			if (!$object instanceof $class) {
				$type = is_object($object) ? get_class($object) : gettype($object);
				throw new InvalidClassInstanceException(get_class($this->backend) . "::createInstance() returned '$type' but '$class' expected.");
			}
		}


		foreach ($this->backend->getProperties($class) as $property) {
			$this->path[] = "[$property]";

			$types = $this->backend->getPropertyTypes($class, $property);
			if (!isset($data[$property])) {
				foreach ($types as $type) {
					if ($type->isNullable && $type->dimensionCount === 0) {
						$this->backend->setPropertyValue($object, $property, NULL);
						array_pop($this->path);
						continue 2;
					}
				}

				if (array_key_exists($property, $data)) {
					throw new InvalidValueException("The $class::\$$property requires data of " . implode('|', $types) . " type, but " . implode($this->path) . " contains {$this->toString($data[$property])}.");
				}

				throw new MissingValueException("Missing " . implode($this->path) . " for $class::\$$property property.");
			}

			$value = NULL;
			$success = FALSE;
			foreach ($types as $type) {
				if ($type->isNullable) {
					continue;
				}

				if ($type->dimensionCount > 0) {
					if ($type->dimensionCount > 1) {
						throw new LogicException("More than 1 array dimension is not supported yet.");
					}

					if (is_array($data[$property])) {
						$tmp = [];
						foreach ($data[$property] as $k => $v) {
							if ($this->tryCompose($type, $v)) {
								$tmp[$k] = $v;
							} else {
								continue 2;  # try next type
							}
						}
						$value = $tmp;
						$success = TRUE;
						break;
					}

				} else {
					$tmp = $data[$property];
					if ($this->tryCompose($type, $tmp)) {
						$value = $tmp;
						$success = TRUE;
						break;
					}
				}
			}

			if (!$success) {
				throw new InvalidValueException("The $class::\$$property requires data of " . implode('|', $types) . " type, but " . implode($this->path) . " contains {$this->toString($data[$property])}.");
			}
			$this->backend->setPropertyValue($object, $property, $value);

			array_pop($this->path);
		}

		# TODO: What about remaining keys in data? Warning?
		return $object;
	}


	/**
	 * @param  object
	 * @param  string
	 * @param  array
	 * @return array
	 * @throws ExportException
	 */
	public function export($object, $class = NULL, array $path = ['$object'])
	{
		$this->path = $path;

		if (!is_object($object)) {
			throw new ExportException('Object expected for export but ' . gettype($object) . ' given.');
		} elseif ($class === NULL) {
			$class = get_class($object);
		} elseif (!$object instanceof $class) {
			throw new ExportException("Expected object of $class but " . get_class($object) . " given.");
		}

		$data = [];
		foreach ($this->backend->getProperties($class) as $property) {
			$this->path[] = "\$$property";

			$types = $this->backend->getPropertyTypes($class, $property);
			$value = $this->backend->getPropertyValue($object, $property);
			if ($value === NULL) {
				foreach ($types as $type) {
					if ($type->isNullable && $type->dimensionCount === 0) {
						array_pop($this->path);
						continue 2;
					}
				}

				throw new ExportException("Value of " . implode('::', $this->path) . " is NULL but $class::\$$property has no nullable type.");
			}

			$success = FALSE;
			foreach ($types as $type) {
				if ($type->dimensionCount > 0) {
					if ($type->dimensionCount > 1) {
						throw new LogicException("More than 1 array dimension is not supported yet.");
					}

					if (is_array($value)) {
						$tmp = [];
						foreach ($value as $k => $v) {
							if ($this->tryDecompose($type, $v)) {
								$tmp[$k] = $v;
							} else {
								continue 2;  # try next type
							}
						}

						$value = $tmp;
						$success = TRUE;
						break;
					}
				} else {
					if ($this->tryDecompose($type, $value)) {
						$success = TRUE;
						break;
					}
				}
			}

			if (!$success) {
				throw new ExportException("Cannot export " . implode('::', $path) . " value {$this->toString($value)}. It cannot be decomposed to " . implode('|', $types) . ' type.');
			}
			$data[$property] = $value;

			array_pop($this->path);
		}

		return $data;
	}


	/**
	 * @return Hydrator
	 */
	protected function createSelf()
	{
		return new self($this->backend);
	}


	/**
	 * @param  Type
	 * @param  mixed
	 * @param  mixed
	 * @return bool
	 */
	private function tryCompose(Type $type, & $data)
	{
		if ($this->backend->composeValue($type, $data)) {
			return TRUE;
		} elseif (is_array($data) && !$type->isBuiltin) {
			try {
				$data = $this->createSelf()->hydrate($type->name, $data, $this->path);
				return TRUE;
			} catch (HydrationException $e) {
			}
		}

		return FALSE;
	}


	/**
	 * @param  Type
	 * @param  mixed
	 * @param  mixed
	 * @return bool
	 */
	private function tryDecompose(Type $type, & $value)
	{
		if ($this->backend->decomposeValue($type, $value)) {
			return TRUE;
		} elseif (is_object($value) && !$type->isBuiltin) {
			try {
				$value = $this->createSelf()->export($value, $type->name, $this->path);
				return TRUE;
			} catch (ExportException $e) {
			}
		}

		return FALSE;
	}


	/**
	 * @param  mixed
	 * @return string
	 */
	private function toString($value)
	{
		if ($value === NULL) {
			return 'NULL';
		}

		if (is_object($value)) {
			return 'instance of ' . get_class($value);
		}

		if (is_array($value)) {
			return 'array';
		}

		if (is_bool($value)) {
			return $value ? 'TRUE' : 'FALSE';
		}

		return "(" . gettype($value) . ") '$value'";
	}

}
