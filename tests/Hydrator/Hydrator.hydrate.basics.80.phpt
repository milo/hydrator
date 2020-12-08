<?php

/**
 * TEST: Basic functionality with PHP 8.0 union property types.
 * @phpVersion 8.0
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$backend = new Hydrator\Backend\PublicPropertiesBackend;
$hydrator = new Hydrator\Hydrator($backend);


test(function () use ($hydrator) {
	class G1
	{
		public int|string $a;

		public int|string $b;

		public int|null $null;

		public int|null $nullSet;
	}

	$expected = new G1;
	$expected->a = 1;
	$expected->b = 'x';
	$expected->null = null;
	$expected->nullSet = null;

	Assert::equal($expected, $hydrator->hydrate(G1::class, ['a' => 1, 'b' => 'x', 'nullSet' => null]));
});
