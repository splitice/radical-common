<?php
namespace Radical\Cache\Object;

/**
 * Persistant Memory Cache System
 * @author SplitIce
 *
 */
class Memory extends Internal\CacheBase implements ICache {
	/**
	 * Get a value from memory using $key
	 * @param string $key Key the value is stored as
	 * @return mixed
	 */
	function get($key) {
		$key = $this->key($key);
		if (function_exists ( 'apc_fetch' )) {
			if(function_exists ( 'apc_exists' ) && apc_exists($key)){
				return apc_fetch ( $key );
			}
		}
        if (function_exists ( 'apcu_fetch' )) {
            if(function_exists ( 'apcu_exists' ) && apcu_exists($key)){
                return apcu_fetch ( $key );
            }
        }
		if (function_exists ( 'xcache_get' )) {
			if(xcache_isset($key)){
				return xcache_get ( $key );
			}
		}
		return null;
	}
	
	/**
	 * Insert or Update a value in memory using $key
	 * @param string $key Key to store value as
	 * @param mixed $value The Value to store
	 * @param int $ttl Time to cache in memory for
	 */
	function set($key, $value, $ttl = 3600) {
		$key = $this->key($key);
		if (function_exists ( 'apc_store' )) {
			return @apc_store ( $key, $value, $ttl );
		}
        if (function_exists ( 'apcu_store' )) {
            return @apcu_store ( $key, $value, $ttl );
        }
		if (function_exists ( 'xcache_set' )) {
			return xcache_set ( $key, $value, $ttl );
		}
	}
	
	function delete($key){
		$key = $this->key($key);
		if (function_exists ( 'apc_delete' )) {
			return apc_delete ( $key );
		}
        if (function_exists ( 'apcu_delete' )) {
            return apcu_delete ( $key );
        }
		if (function_exists ( 'xcache_unset' )) {
			return xcache_unset ( $key );
		}
	}
}