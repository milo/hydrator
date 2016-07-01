<?php

/**
 * TEST: Basic functionality.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$backend = new Hydrator\Backend\PublicPropertiesBackend;
$hydrator = new Hydrator\Hydrator($backend);


# NULLs
test(function () use ($hydrator) {
	class C1
	{
		/** @var int */
		public $int1;

		/** @var NULL|int */
		public $int2;

		/** @var int|NULL */
		public $null = [];

		/** @var int|NULL */
		public $nullSet = [];
	}

	$expected = new C1;
	$expected->int1 = 1;
	$expected->int2 = 2;
	$expected->null = NULL;
	$expected->nullSet = NULL;

	Assert::equal($expected, $hydrator->hydrate(C1::class, ['int1' => 1, 'int2' => 2, 'nullSet' => NULL]));
});


# Arrays
test(function () use ($hydrator) {
	class C5
	{
		/** @var int */
		public $required;
	}

	class C6
	{
		/** @var C5[]|float[]|int[] */
		public $a;
	}

	$data = [10 => 1, 2, 3, 'foo' => 4];

	Assert::exception(function () use ($hydrator, $data) {
		$hydrator->hydrate(C5::class, $data);
	}, Hydrator\MissingValueException::class, "Missing \$data[required] for C5::\$required property.");

	$expected = new C6;
	$expected->a = $data;
	Assert::equal($expected, $hydrator->hydrate(C6::class, ['a' => $data]));
});


# C7 wins because there are no data for C8
test(function () use ($hydrator) {
	class C7
	{
		/** @var int */
		public $c7;
	}

	class C8 extends C7
	{
		/** @var int */
		public $c8;
	}

	class C9
	{
		/** @var C8|C7 */
		public $a;

		/** @var C8[]|C7[] */
		public $b;
	}

	$c7 = new C7;
	$c7->c7 = 1;

	$expected = new C9;
	$expected->a = $c7;
	$expected->b = [$c7, $c7];

	$actual = $hydrator->hydrate(C9::class, [
		'a' => [
			'c7' => 1,
		],
		'b' => [
			['c7' => 1, 'c8' => 2],
			['c7' => 1],
		],
	]);

	Assert::equal($expected, $actual);
});


# Empty class name
test(function () use ($hydrator) {
	class C10
	{
		/** @var |int */
		public $a;
	}

	Assert::exception(function () use ($hydrator) {
		$hydrator->hydrate(C10::class, ['a' => 1]);
	}, Hydrator\Backend\InvalidClassException::class, 'Class name must not be empty.');
});
