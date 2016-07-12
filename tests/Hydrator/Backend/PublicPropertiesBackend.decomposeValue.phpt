<?php

require __DIR__ . '/../../bootstrap.php';

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
	Assert::true($backend->decomposeValue(typeFactory($type), $value), "Key $k");
	Assert::true($backend->decomposeValue(typeFactory('mixed'), $value), "Key $k");
}


$successCases = [
	['DateTime', new DateTime('2016-05-31 00:11:22'), 'string'],
];
foreach ($successCases as $k => $case) {
	list($type, $value) = $case;
	Assert::true($backend->decomposeValue(typeFactory($type, FALSE), $value), "Key $k");
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
	Assert::true($backend->decomposeValue(typeFactory('DateTime', FALSE), $value), "Key $k");
	Assert::equal($decomposed, $value);
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
	['stdClass', (object) []],
	['DateTime', NULL],
	['DateTime', []],
	['DateTime', '2016-05:31'],
];

foreach ($failingCases as $k => $case) {
	list($type, $value) = $case;
	Assert::false($backend->decomposeValue(typeFactory($type), $value), "Key $k");
}
