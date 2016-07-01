<?php

/**
 * TEST: Checks that backend does not return incorrect class instance.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Milo\Hydrator\Type;
use Tester\Assert;


class InvalidInstanceBackend implements Hydrator\IHydratorBackend
{
	function createInstance($class, array $args = []) { return (object) []; }
	function getProperties($class) {}
	function getPropertyTypes($class, $property) {}
	function composeValue(Type $type, & $value) {}
	function decomposeValue(Type $type, & $value) {}
	function setPropertyValue($object, $property, $value) {}
	function getPropertyValue($object, $property) {}
}

$hydrator = new Hydrator\Hydrator(new InvalidInstanceBackend);
Assert::exception(function () use ($hydrator) {
	$hydrator->hydrate(DateTime::class, []);
}, Hydrator\InvalidClassInstanceException::class, "InvalidInstanceBackend::createInstance() returned 'stdClass' but 'DateTime' expected.");
