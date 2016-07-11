[![Build Status](https://travis-ci.org/milo/hydrator.svg?branch=master)](https://travis-ci.org/milo/hydrator)



# Usage
```php
class Person
{
	/** @var string */
	public $name;

	/** @var string */
	public $surname;

	/** @var string|NULL */
	public $email;

	/** @var Address[] */
	public $addresses;
}

class Address
{
	/** @var string */
	public $city;
}

$data = [
	'name' => 'Miloslav',
	'surname' => 'Hůla',
	'addresses' => [
		['city' => 'Prague'],
		['city' => 'Roztoky'],
	],
];


$backend = new Milo\Hydrator\Backend\PublicPropertiesBackend;
$hydrator = new Milo\Hydrator\Hydrator($backend);


/** @var Person $person  created from array */
$person = $hydrator->hydrate(Person::class, $data);
```

Personally, I'm using it for hydrate and export configuration written in [NEON](https://ne-on.org/). Configuration
 stored in an array is fine, but when grows too much, it's hard to refactor. Working with type hinted objects in IDE
 is much more comfortable and less error prone.



# Backend
There is only one backend for now.


### [`PublicPropertiesBackend`](src/Hydrator/Backend/PublicPropertiesBackend.php )
It manipulates with public properties of class. It makes type check according to `@var` annotation.
 Type string has to be in piped format (e.g. `int|string|NULL`).
 Type checking is strict which means, integer cannot be cast to string or vice versa even it would be possible.

Implement [`Milo\Hydrator\IHydratorBackend`](src/Hydrator/IHydratorBackend.php) for your own backend.



# Installation
Use [Composer](https://getcomposer.org/): `composer require milo/hydrator`



# TODO
- caching for `PublicPropertiesBackend` because FQCN resolving is expensive
- improve error messages for deep structures because now are terrible
- support more than one array dimension in Hydrator
