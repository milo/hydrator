<?php

/**
 * @phpVersion 7.4
 */

require __DIR__ . '/../../bootstrap.php';

use Milo\Hydrator\Backend\PublicPropertiesBackend;
use Tester\Assert;


class ClassWithTypeHints
{
	public string $a;

	public ClassWithTypeHints $b;

	public ?ClassWithTypeHints $c;

	/** string */
	public int $d;

	/** @var string[]|int[] */
	public ?array $e;
}


$backend = new PublicPropertiesBackend;

Assert::equal([
	typeFactory('string'),
], $backend->getPropertyTypes(ClassWithTypeHints::class, 'a'));

Assert::equal([
	typeFactory(ClassWithTypeHints::class, FALSE),
], $backend->getPropertyTypes(ClassWithTypeHints::class, 'b'));

Assert::equal([
	typeFactory(ClassWithTypeHints::class, FALSE, 0, TRUE),
], $backend->getPropertyTypes(ClassWithTypeHints::class, 'c'));

Assert::equal([
	typeFactory('int'),
], $backend->getPropertyTypes(ClassWithTypeHints::class, 'd'));

Assert::equal([
	typeFactory('string', TRUE, 1, TRUE),
	typeFactory('int', TRUE, 1, TRUE),
], $backend->getPropertyTypes(ClassWithTypeHints::class, 'e'));
