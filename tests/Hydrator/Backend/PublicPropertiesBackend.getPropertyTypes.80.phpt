<?php

/**
 * TEST: PHP 8 union types
 * @phpVersion 8.0
 */

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassWith80Types
{
	/** @var string[]|int[]|array */
	public string|int|null|ClassWith80Types|array $a;
}


$backend = new PublicPropertiesBackend;

Assert::equal([
	typeFactory(ClassWith80Types::class, false),
	typeFactory('string', true, 1),
	typeFactory('int', true, 1),
	typeFactory('string'),
	typeFactory('int'),
	typeFactory('null', true, 0, true),
], $backend->getPropertyTypes(ClassWith80Types::class, 'a'));
