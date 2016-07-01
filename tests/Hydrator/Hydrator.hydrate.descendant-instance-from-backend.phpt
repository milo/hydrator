<?php

/**
 * TEST: Backend may return descendant class instance.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Milo\Hydrator\Type;
use Tester\Assert;


class ChildDateTime extends DateTime
{}

class DescendantInstanceBackend implements Hydrator\IHydratorBackend
{
	function createInstance($class, array $args = []) { return new ChildDateTime; }
	function getProperties($class) { return []; }
	function getPropertyTypes($class, $property) { return []; }
	function composeValue(Type $type, & $value) {}
	function decomposeValue(Type $type, & $value) {}
	function setPropertyValue($object, $property, $value) {}
	function getPropertyValue($object, $property) {}
}

$hydrator = new Hydrator\Hydrator(new DescendantInstanceBackend);
Assert::type(
	ChildDateTime::class,
	$hydrator->hydrate(DateTime::class, [])
);
