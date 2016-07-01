<?php

/**
 * TEST: Error messages.
 */

require __DIR__ . '/../bootstrap.php';

use Milo\Hydrator;
use Tester\Assert;


$hydrator = new Hydrator\Hydrator(new Hydrator\Backend\PublicPropertiesBackend);


Assert::exception(function () use ($hydrator) {
	$hydrator->export([]);
}, Hydrator\ExportException::class, 'Object expected for export but array given.');

Assert::exception(function () use ($hydrator) {
	$hydrator->export(new DateTime, 'FooClass');
}, Hydrator\ExportException::class, 'Expected object of FooClass but DateTime given.');


//class D1
//{
//	/** @var int */
//	public $int;
//}
//
//class D2
//{
//	/** @var D1 */
//	public $d1;
//}
//
//class D3
//{
//	/** @var D2 */
//	public $d2;
//}
//
//
//Assert::exception(function () use ($hydrator) {
//	$hydrator->hydrate(D1::class, [
//		'int' => NULL,
//	]);
//}, Hydrator\InvalidValueException::class, 'The D1::$int requires data of int type, but $data[int] contains NULL.');
//
//Assert::exception(function () use ($hydrator) {
//	$hydrator->hydrate(D1::class, [
//		'int' => TRUE,
//	]);
//}, Hydrator\InvalidValueException::class, 'The D1::$int requires data of int type, but $data[int] contains TRUE.');
//
//Assert::exception(function () use ($hydrator) {
//	$hydrator->hydrate(D1::class, [
//		'int' => 'foo',
//	]);
//}, Hydrator\InvalidValueException::class, 'The D1::$int requires data of int type, but $data[int] contains (string) \'foo\'.');
//
//Assert::exception(function () use ($hydrator) {
//	$hydrator->hydrate(D3::class, [
//		'd2' => [],
//	]);
//}, Hydrator\InvalidValueException::class, 'The D3::$d2 requires data of D2 type, but $data[d2] contains array.');
//
//Assert::exception(function () use ($hydrator) {
//	$hydrator->hydrate(D3::class, [
//		'd2' => (object) [],
//	]);
//}, Hydrator\InvalidValueException::class, 'The D3::$d2 requires data of D2 type, but $data[d2] contains instance of stdClass.');
