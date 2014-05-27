<?php
namespace Radical\Cache;

class PooledCache {
	protected static $cache = array();
	
	static function get($pool, $object){
        $oc = $object.$pool;
		if(isset(static::$cache[$oc])){
			return static::$cache[$oc];
		}
		
		$c = 'Radical\\Cache\\Object\\'.$object;
		if(!class_exists($c)){
			throw new \Exception('Cant find cache of type: '.$object);
		}

		$cache = new $c($pool);
		static::$cache[$oc] = $cache;
		
		return $cache;
	}
}