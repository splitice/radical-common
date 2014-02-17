<?php
namespace Radical\Cache\Object\Internal;

abstract class FileCacheBase extends CacheBase {
	static $cachePath = null;
	protected function cachePath(){
		if(static::$cachePath){
			return static::$cachePath;
		}
		global $BASEPATH;
		return $BASEPATH.DS.'cache'.DS;
	}
	
	static function setCachePath($cp){
		static::$cachePath = $cp;
	}
}