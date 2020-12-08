<?php

/**
 * TEST: PHP 7.4 property types
 * @phpVersion 7.4
 */

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassWith74Types
{
	public string $a;

	public ?string $b;

	public array $c;

	/** @var string[]|int[]|stdClass|bool|DateTime[] */
	public array $d;
}


$backend = new PublicPropertiesBackend;

Assert::equal([
	typeFactory('string'),
], $backend->getPropertyTypes(ClassWith74Types::class, 'a'));

Assert::equal([
	typeFactory('string'),
	typeFactory('null', true, 0, true),
], $backend->getPropertyTypes(ClassWith74Types::class, 'b'));

Assert::equal([
	typeFactory('array', false),
], $backend->getPropertyTypes(ClassWith74Types::class, 'c'));

Assert::equal([
	typeFactory('string', true, 1),
	typeFactory('int', true, 1),
	typeFactory(DateTime::class, false, 1),
], $backend->getPropertyTypes(ClassWith74Types::class, 'd'));
