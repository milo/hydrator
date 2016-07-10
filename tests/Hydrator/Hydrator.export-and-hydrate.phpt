<?php

/**
 * TEST: Basic functionality in both direction.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$backend = new Hydrator\Backend\PublicPropertiesBackend;
$hydrator = new Hydrator\Hydrator($backend);

# without constructor
test(function () use ($hydrator) {
	class C1
	{
		/** @var string */
		public $a;

		/** @var string[] */
		public $b;
	}

	$object = new C1;
	$object->a = 'str';
	$object->b = ['str1', 'str2'];
	$objectInArray = $hydrator->export($object, C1::class);
	$hydrateObject = $hydrator->hydrate(C1::class, $objectInArray);

	Assert::equal($object, $hydrateObject);
});

# with constructor
test(function () use ($hydrator) {
	class C2
	{
		/** @var string */
		public $a;

		/** @var string[] */
		public $b;

		public function __construct($a, array $b)
		{
			$this->a = $a;
			$this->b = $b;
		}
	}

	$object = new C2('str', ['str1', 'str2']);
	$objectInArray = $hydrator->export($object, C2::class);
	$hydrateObject = $hydrator->hydrate(C2::class, $objectInArray);

	Assert::equal($object, $hydrateObject);
});
