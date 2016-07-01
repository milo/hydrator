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

$o = $backend->createInstance(ClassWithConstructor::class, ['arg']);
Assert::type(ClassWithConstructor::class, $o);
Assert::same('arg', $o->arg);
