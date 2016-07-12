<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassToBeSet
{
	public $a;
}


$backend = new PublicPropertiesBackend;
$object = new ClassToBeSet;

$backend->setPropertyValue($object, 'a', 'SET');
Assert::same('SET', $object->a);
