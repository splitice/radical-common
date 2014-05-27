<?php
namespace Radical\Cache\Object\Internal;

/**
 * Base class for all caches
 * @author SplitIce
 *
 */
abstract class CacheBase {
	protected $pool;
	
	function __construct($pool){
		$this->pool = $pool;
	}
	
	function key($key){
		return md5($key.'|'.$this->pool);
	}
	
	/**
	 * Function that checks if a key exists in the cache, if it doesnt executes a callback and stores it as $key 
	 * @param string $key_sem
	 * @param callable $function
	 * @param int $ttl
	 * @return mixed
	 */
	function cachedValue($key_sem, $function, $ttl = 3600) {
		$data = $this->Get ( $key_sem );
		if ($data) {
			return $data;
		}
		$data = $function ();
		$this->Set ( $key_sem, $data, $ttl );
		return $data;
	}
}