<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassForGet
{
	public $a;
}


$backend = new PublicPropertiesBackend;
$object = new ClassForGet;
$object->a = 'GET';

$backend->getPropertyValue($object, 'a');
Assert::same('GET', $object->a);
