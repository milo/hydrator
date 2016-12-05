<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\Helpers;
use Tester\Assert;


require __DIR__ . '/assets/trait-at.php';
require __DIR__ . '/assets/trait-bt.php';
require __DIR__ . '/assets/class-ac.php';
require __DIR__ . '/assets/class-bc.php';

Assert::same([
	'class-ac.php',
], array_map('basename', Helpers::getClassDependentFiles(AC::class)));

Assert::same([
	'class-bc.php',
	'class-ac.php',
	'trait-bt.php',
	'trait-at.php',
], array_map('basename', Helpers::getClassDependentFiles(BC::class)));
