<?php

require __DIR__ . '/../vendor/autoload.php';


Tester\Environment::setup();
date_default_timezone_set('UTC');

function test(\Closure $cb) {
	$cb();
}

function typeFactory($name, $isBuiltin = true, $dimensionCount = 0, $isNullable = false)
{
	$type = new Milo\Hydrator\Type;
	$type->name = $name;
	$type->isBuiltin = $isBuiltin;
	$type->dimensionCount = $dimensionCount;
	$type->isNullable = $isNullable;
	return $type;
}
