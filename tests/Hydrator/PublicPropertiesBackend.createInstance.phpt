<?php

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassWithConstructor
{
	public $arg;

	public function __construct($arg)
	{
		$this->arg = $arg;
	}
}


$backend = new PublicPropertiesBackend;

# Numerical keys for constructor
test(function () use ($backend) {
	$o = $backend->createInstance(ClassWithConstructor::class, ['arg']);
	Assert::type(ClassWithConstructor::class, $o);
	Assert::same('arg', $o->arg);
});


# String keys for constructor
test(function () use ($backend) {
	$o = $backend->createInstance(ClassWithConstructor::class, ['arg' => 'arg']);
	Assert::type(ClassWithConstructor::class, $o);
	Assert::same('arg', $o->arg);
});
