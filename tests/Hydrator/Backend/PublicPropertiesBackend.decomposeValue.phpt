<?php

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


$backend = new PublicPropertiesBackend;

$successCases = [
	['bool', true],
	['bool', false],
	['boolean', true],
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
	Assert::true($backend->decomposeValue(typeFactory($type), $value), "Key $k");
	Assert::true($backend->decomposeValue(typeFactory('mixed'), $value), "Key $k");
}


$successCases = [
	['DateTime', new DateTime('2016-05-31 00:11:22'), 'string'],
];
foreach ($successCases as $k => $case) {
	list($type, $value) = $case;
	Assert::true($backend->decomposeValue(typeFactory($type, false), $value), "Key $k");
	Assert::true($backend->decomposeValue(typeFactory('mixed'), $value), "Key $k");
	Assert::type($case[2], $value);
}


$dateTimeCases = [
	[new DateTime('2016-05-31 00:11:22', new DateTimeZone('UTC')), '2016-05-31 00:11:22 UTC'],
	[new DateTime('2016-05-31 00:11:22', new DateTimeZone('+02:30')), '2016-05-31 00:11:22 +02:30'],
	[new DateTime('2016-05-31 00:11:22', new DateTimeZone('-02:30')), '2016-05-31 00:11:22 -02:30'],
];
foreach ($dateTimeCases as $k => $case) {
	list($value, $decomposed) = $case;
	Assert::true($backend->decomposeValue(typeFactory('DateTime', false), $value), "Key $k");
	Assert::equal($decomposed, $value);
}


$failingCases = [
	['bool', null],
	['bool', 'TRUE'],
	['bool', ''],
	['bool', '0'],
	['bool', '1'],
	['int', null],
	['int', ''],
	['int', '0'],
	['int', '1'],
	['float', null],
	['float', ''],
	['float', '0.0'],
	['float', '1.0'],
	['number', null],
	['number', ''],
	['number', '1'],
	['number', '1.0'],
	['string', null],
	['string', true],
	['string', []],
	['string', 0],
	['string', 0.1],
	['array', null],
	['resource', null],
	['callable', null],
	['callable', false],
	['stdClass', (object) []],
	['DateTime', null],
	['DateTime', []],
	['DateTime', '2016-05:31'],
];

foreach ($failingCases as $k => $case) {
	list($type, $value) = $case;
	Assert::false($backend->decomposeValue(typeFactory($type), $value), "Key $k");
}
