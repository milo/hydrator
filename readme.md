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


### [`PublicPropertiesBackend`](src/Hydrator/Backend/PublicPropertiesBackend.php)
It manipulates with public properties of class. It makes type check according to `@var` annotation.
 Type string has to be in piped format (e.g. `int|string|NULL`).
 Type checking is strict which means, integer cannot be cast to string or vice versa even it would be possible.

Implement [`Milo\Hydrator\IHydratorBackend`](src/Hydrator/IHydratorBackend.php) for your own backend.



# Caching
Implement [`ICache`](src/Hydrator/Backend/ICache.php) and use it with backend. It saves resources associated with
 classes reflection and FQCN (Fully Qualified Class Name) resolving.

I'm using a simple cache class with [Nette Caching](https://github.com/nette/caching). It looks like:
```php
use Milo\Hydrator;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;


class HydratorCache implements Hydrator\Backend\ICache
{
	/** @var Cache */
	private $cache;


	public function __construct(IStorage $storage)
	{
		$this->cache = new Cache($storage, 'milo.hydrator');
	}


	public function save($key, $value, array $dependentFiles = NULL)
	{
		$dependencies = $dependentFiles
			? [Cache::FILES => $dependentFiles]
			: NULL;

		$this->cache->save($key, $value, $dependencies);
	}


	public function load($key)
	{
		return $this->cache->load($key);
	}
}
```


# Installation
Use [Composer](https://getcomposer.org/): `composer require milo/hydrator`



# TODO
- improve error messages for deep structures because now are terrible
- support more than one array dimension in Hydrator
