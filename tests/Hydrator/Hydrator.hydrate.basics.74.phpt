<?php

/**
 * TEST: Basic functionality with PHP 7.4 property types.
 * @phpVersion 7.4
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$backend = new Hydrator\Backend\PublicPropertiesBackend;
$hydrator = new Hydrator\Hydrator($backend);


test(function () use ($hydrator) {
	class F1
	{
		public int $int1;

		public ?int $int2;

		public ?int $null;

		public ?int $nullSet;
	}

	$expected = new F1;
	$expected->int1 = 1;
	$expected->int2 = 2;
	$expected->null = null;
	$expected->nullSet = null;

	$f1 = $hydrator->hydrate(F1::class, ['int1' => 1, 'int2' => 2, 'nullSet' => null]);

	$rp = new ReflectionProperty(F1::class, 'null');
	Assert::true($rp->isInitialized($f1));
	Assert::equal($expected, $f1);
});
