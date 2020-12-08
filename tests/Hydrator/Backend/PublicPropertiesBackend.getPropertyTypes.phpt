<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


trait TraitWithTypes
{
	/** @var NULL */
	public $t;
}

class ClassWithTypes
{
	use TraitWithTypes;

	/** @var int|float[]|MiXeD|NULL|ClassWithTypes text */
	public $a;

	/** @var int*/
	public $b;

	/** @foo @var float @bar */
	public $c;

	/**
	 * @var int
	 * @var float
	 */
	public $d;

	public $e;
}


$backend = new PublicPropertiesBackend;

Assert::equal([
	typeFactory('int'),
	typeFactory('float', true, 1),
	typeFactory('mixed'),
	typeFactory('null', true, 0, true),
	typeFactory('ClassWithTypes', false),
], $backend->getPropertyTypes(ClassWithTypes::class, 'a'));

Assert::equal([
	typeFactory('int'),
], $backend->getPropertyTypes(ClassWithTypes::class, 'b'));

Assert::equal([
	typeFactory('float'),
], $backend->getPropertyTypes(ClassWithTypes::class, 'c'));

Assert::exception(function () use ($backend) {
	$backend->getPropertyTypes(ClassWithTypes::class, 'd');
}, Milo\Hydrator\Backend\InvalidAnnotationException::class, 'Multiple @var annotations for ClassWithTypes::$d property. Exactly one required.');

Assert::exception(function () use ($backend) {
	$backend->getPropertyTypes(ClassWithTypes::class, 'e');
}, Milo\Hydrator\Backend\InvalidAnnotationException::class, 'Missing @var annotation for ClassWithTypes::$e property.');

Assert::equal([
	typeFactory('null', true, 0, true),
], $backend->getPropertyTypes(ClassWithTypes::class, 't'));
