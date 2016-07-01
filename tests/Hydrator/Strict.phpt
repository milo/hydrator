<?php

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


class StrictClass
{
	use Hydrator\Strict;
}

$o = new StrictClass;

Assert::exception(function () use ($o) {
	$o->foo;
}, Hydrator\LogicException::class);


Assert::exception(function () use ($o) {
	$o->foo = 'bar';
}, Hydrator\LogicException::class);
