<?php

namespace Milo\Hydrator\Backend;


interface ICache
{

	/**
	 * Writes into cache.
	 * @param  string
	 * @param  mixed
	 * @param  array
	 * @return void
	 */
	function save($key, $value, array $dependentFiles = null);


	/**
	 * Loads from cache
	 * @param  string
	 * @return mixed|NULL
	 */
	function load($key);

}
