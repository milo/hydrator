<?php

namespace Milo\Hydrator\Backend;

use ReflectionClass;


/**
 * @internal
 */
class Helpers
{

	/**
	 * Expands class name into fully qualified name.
	 * @author  https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/src/DI/PhpReflection.php
	 * @license https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/license.md
	 *
	 * @param  string
	 * @param  ReflectionClass $rc
	 * @return string  fully qualified name
	 * @throws InvalidClassException
	 */
	public static function expandClassName($name, ReflectionClass $rc)
	{
		if (empty($name)) {
			throw new InvalidClassException('Class name must not be empty.');

		} elseif (in_array(strtolower($name), ['self', 'static', '$this'], TRUE)) {
			return $rc->getName();

		} elseif ($name[0] === '\\') {  # already fully qualified
			return ltrim($name, '\\');
		}

		$uses = self::getUseStatements($rc);
		$parts = explode('\\', $name, 2);
		if (isset($uses[$parts[0]])) {
			$parts[0] = $uses[$parts[0]];
			return implode('\\', $parts);

		} elseif ($rc->inNamespace()) {
			return $rc->getNamespaceName() . '\\' . $name;

		} else {
			return $name;
		}
	}


	/**
	 * @param  ReflectionClass $class
	 * @return array of [alias => class]
	 */
	public static function getUseStatements(ReflectionClass $class)
	{
		static $cache = [];

		if (isset($cache[$name = $class->getName()])) {
			return $cache[$name];
		}

		$code = file_get_contents($class->getFileName());
		$cache += self::parseUseStatements($code);

		return $cache[$name];
	}


	/**
	 * Parses PHP code.
	 * @author  https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/src/DI/PhpReflection.php
	 * @license https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/license.md
	 *
	 * @param  string
	 * @return array of [class => [alias => fqn, ...]]
	 */
	public static function parseUseStatements($code)
	{
		$tokens = token_get_all($code);
		$namespace = $class = $classLevel = $level = NULL;
		$res = $uses = [];

		$nameTokens = PHP_VERSION_ID < 80000
			? [T_STRING, T_NS_SEPARATOR]
			: [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED];

		while ($token = current($tokens)) {
			next($tokens);
			switch (is_array($token) ? $token[0] : $token) {
				case T_NAMESPACE:
					$namespace = ltrim(self::fetch($tokens, $nameTokens) . '\\', '\\');
					$uses = [];
					break;

				case T_CLASS:
				case T_INTERFACE:
				case T_TRAIT:
					if ($name = self::fetch($tokens, T_STRING)) {
						$class = $namespace . $name;
						$classLevel = $level + 1;
						$res[$class] = $uses;
					}
					break;

				case T_USE:
					while (!$class && ($name = self::fetch($tokens, $nameTokens))) {
						$name = ltrim($name, '\\');
						if (self::fetch($tokens, T_AS)) {
							$uses[self::fetch($tokens, T_STRING)] = $name;
						} else {
							$tmp = explode('\\', $name);
							$uses[end($tmp)] = $name;
						}
						if (!self::fetch($tokens, ',')) {
							break;
						}
					}
					break;

				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
				case '{':
					$level++;
					break;

				case '}':
					if ($level === $classLevel) {
						$class = $classLevel = NULL;
					}
					$level--;
			}
		}

		return $res;
	}


	/**
	 * @author  https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/src/DI/PhpReflection.php
	 * @license https://github.com/nette/di/blob/d16c0437b1679c4fe4e74fce6ddeacf9573d41ed/license.md
	 */
	private static function fetch(& $tokens, $take)
	{
		$res = NULL;
		while ($token = current($tokens)) {
			list($token, $s) = is_array($token) ? $token : [$token, $token];
			if (in_array($token, (array) $take, TRUE)) {
				$res .= $s;
			} elseif (!in_array($token, [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT], TRUE)) {
				break;
			}
			next($tokens);
		}
		return $res;
	}


	/**
	 * Returns array of file names which class depends on.
	 * @param  string
	 * @return array
	 */
	public static function getClassDependentFiles($class)
	{
		$files = [];

		$reflections = [new \ReflectionClass($class)];
		while (count($reflections)) {
			/** @var \ReflectionClass $rc */
			$rc = array_shift($reflections);
			$files[$rc->getFileName()] = 1;

			if ($rpc = $rc->getParentClass()) {
				$reflections[] = $rpc;
			}

			foreach ($rc->getTraits() as $rt) {
				$reflections[] = $rt;
			}
		}

		return array_keys($files);
	}

}
