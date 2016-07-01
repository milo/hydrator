<?php

/**
 * TEST: Fully qualified class names handling.
 */

namespace
{
	require __DIR__ . '/../bootstrap.php';

	class NotNS
	{
		/** @var NotNS */
		public $prop1;
	}
}


namespace TestNamespace\Deep
{
	class C
	{
	}
}


namespace TestNamespace
{
	use TestNamespace\Deep as TND;

	class C
	{
		/** @var \NotNS */
		public $prop1;

		/** @var TND\C */
		public $prop2;

		/** @var \TestNamespace\Deep\C */
		public $prop3;

		/** @var C */
		public $prop4;

		/** @var self */
		public $prop5;

		/** @var static */
		public $prop6;

		/** @var $this */
		public $prop7;
	}
}



namespace {

	use Milo\Hydrator\Backend\PublicPropertiesBackend;
	use Tester\Assert;

	$backend = new PublicPropertiesBackend;

	Assert::equal([
		typeFactory(NotNS::class, FALSE),
	], $backend->getPropertyTypes(NotNS::class, 'prop1'));

	Assert::equal([
		typeFactory(NotNS::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop1'));

	Assert::equal([
		typeFactory(TestNamespace\Deep\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop2'));

	Assert::equal([
		typeFactory(TestNamespace\Deep\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop3'));

	Assert::equal([
		typeFactory(TestNamespace\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop4'));

	Assert::equal([
		typeFactory(TestNamespace\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop5'));

	Assert::equal([
		typeFactory(TestNamespace\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop6'));

	Assert::equal([
		typeFactory(TestNamespace\C::class, FALSE),
	], $backend->getPropertyTypes(TestNamespace\C::class, 'prop7'));
}
