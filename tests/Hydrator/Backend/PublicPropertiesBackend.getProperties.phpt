<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassWithProperties
{
	public $a;

	public $b;

	protected $c;

	private $d;

	protected $e;

	private $f;
}

trait TraitWithProperties
{
	public $t;
}

class DescendantWithProperties extends ClassWithProperties
{
	use TraitWithProperties;

	public $b;

	public $c;

	public $d;

	public static $s;
}


$backend = new PublicPropertiesBackend;

$props = $backend->getProperties(DescendantWithProperties::class);
sort($props);

Assert::same([
	'a',
	'b',
	'c',
	'd',
	't',
], $props);
