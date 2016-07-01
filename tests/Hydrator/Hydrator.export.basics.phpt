<?php

/**
 * TEST: Basic functionality.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$hydrator = new Hydrator\Hydrator(new Hydrator\Backend\PublicPropertiesBackend);


# NULLs
test(function () use ($hydrator) {
	class E1
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

	$actual = new E1;
	$actual->int1 = 1;
	$actual->int2 = 2;
	$actual->null = NULL;
	$actual->nullSet = NULL;

	Assert::equal([
		'int1' => 1,
		'int2' => 2,
	], $hydrator->export($actual));


	class E2
	{
		/** @var int */
		public $int;
	}

	Assert::exception(function () use ($hydrator) {
		$hydrator->export(new E2);
	}, Hydrator\ExportException::class, 'Value of $object::$int is NULL but E2::$int has no nullable type.');
});


# Arrays
test(function () use ($hydrator) {
	class E3
	{
		/** @var int */
		public $required;
	}

	class E4 extends E3
	{
		/** @var float */
		public $required;
	}


	class E5
	{
		/** @var E4[]|E3[]|float[]|int[] */
		public $a;
	}

	$o1 = new E5;
	$o1->a = [3 => new E3, new E3];
	$o1->a[3]->required = 1;
	$o1->a[4]->required = 2;
	Assert::same([
		'a' => [
			3 => ['required' => 1],
			4 => ['required' => 2],
		],
	], $hydrator->export($o1));


	$o2 = new E5;
	$o2->a = [1.0, 2.1];
	Assert::same([
		'a' => [
			1.0,
			2.1,
		],
	], $hydrator->export($o2));


	$o2 = new E5;
	$o2->a = [1, 2];
	Assert::same([
		'a' => [
			1,
			2,
		],
	], $hydrator->export($o2));


	$o3 = new E5;
	$o3->a = [1, 2.0];
	Assert::exception(function () use ($hydrator, $o3) {
		$hydrator->export($o3, NULL, ['$o3']);
	}, Hydrator\ExportException::class, 'Cannot export $o3 value array. It cannot be decomposed to E4[]|E3[]|float[]|int[] type.');
});
