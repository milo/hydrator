<?php

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


$backend = new PublicPropertiesBackend;

$successCases = [
	['bool', TRUE],
	['bool', FALSE],
	['boolean', TRUE],
	['int', -1],
	['int', 0],
	['int', 1],
	['integer', 0],
	['float', -1.0],
	['float', 0.0],
	['float', 1.0],
	['double', 1.0],
	['number', 1],
	['number', 1.0],
	['string', ''],
	['string', '0'],
	['array', []],
	['resource', fopen('php://memory', 'w')],
	['callable', function () {}],
];
foreach ($successCases as $k => $case) {
	list($type, $value) = $case;
	Assert::true($backend->composeValue(typeFactory($type), $value), "Key $k");
	Assert::true($backend->composeValue(typeFactory('mixed'), $value), "Key $k");
	if (isset($case[2])) {
		Assert::type($case[2], $value);
	}
}


$objectCases = [
	['stdClass', (object) []],
	['\stdClass', (object) []],
	['DateTime', new DateTime],
	['DateTime', time(), DateTime::class],
	['DateTime', '2016-05-31', DateTime::class],
	['DateTime', '2016-05-31 00:11:22', DateTime::class],
];
foreach ($objectCases as $k => $case) {
	list($type, $value) = $case;
	Assert::true($backend->composeValue(typeFactory($type, FALSE), $value), "Key $k");
	Assert::true($backend->composeValue(typeFactory('mixed'), $value), "Key $k");
	if (isset($case[2])) {
		Assert::type($case[2], $value);
	}
}


$failingCases = [
	['bool', NULL],
	['bool', 'TRUE'],
	['bool', ''],
	['bool', '0'],
	['bool', '1'],
	['int', NULL],
	['int', ''],
	['int', '0'],
	['int', '1'],
	['float', NULL],
	['float', ''],
	['float', '0.0'],
	['float', '1.0'],
	['number', NULL],
	['number', ''],
	['number', '1'],
	['number', '1.0'],
	['string', NULL],
	['string', TRUE],
	['string', []],
	['string', 0],
	['string', 0.1],
	['array', NULL],
	['resource', NULL],
	['callable', NULL],
	['callable', FALSE],
	['stdClass', []],
	['\stdClass', []],
	['DateTime', NULL],
	['DateTime', []],
	['DateTime', '2016-05:31'],
];

foreach ($failingCases as $k => $case) {
	list($type, $value) = $case;
	Assert::false($backend->composeValue(typeFactory($type), $value), "Key $k");
}
