<?php
namespace Radical\Core;

use Composer\Autoload\ClassLoader;
use Radical\Cache\PooledCache;

class Libraries {
	/**
	 * @var \Radical\Cache\Object\ICache an optional cache for iterated queries
	 */
	public static $cache;
	
	/**
	 * @return \Composer\Autoload\ClassLoader
	 */
	static function composer_autoloader(){
		static $once = false;
		if($once === false){
			foreach(spl_autoload_functions () as $a){
				if(is_array($a)){
					$a = $a[0];
					if($a instanceof ClassLoader){
						$once = $a;
						break;
					}
				}
			}
		}
		return $once;
	}
	
	/**
	 * Resolves a relative path to class the
	 * appropriate full class path.
	 * 
	 * @param string $path partial path to class
	 * @return string
	 */
	static function path($path){
		return self::composer_autoloader()->findFile(self::toClass($path));
	}
	
	/**
	 * Convert path slashes into namespace seperators
	 * 
	 * @param string $path
	 * @return string
	 */
	static function toClass($path){
		return str_replace(DIRECTORY_SEPARATOR, '\\', $path);
	}
	
	/**
	 * Convert a class to path format, optionally resolving
	 * 
	 * @param string $class the class to convert
	 * @param boolean $full return full path or not
	 * @return string the path
	 */
	static function toPath($class,$full = false){
		if($full){
			return self::path($class);
		}
		return str_replace('\\', DIRECTORY_SEPARATOR, $class);
	}

	static function isProjectSpace($class){
	    global $_PROJECT;
	    return starts_with($class, $_PROJECT);
    }
	
	/**
	 * Get a class relative to the project namespace
	 * 
	 * @param string $class Class to append to project space
	 * @param string $project Project space to use, null for default
	 * @return string
	 */
	static function getProjectSpace($class = '',$project = null){
		if($project === null){
			global $_PROJECT;
			$project = $_PROJECT;
		}
		return '\\'.$project.'\\'.ltrim($class,'\\');
	}
	
	static function getAllClass(){
		return array_keys(self::composer_autoloader()->getClassMap());
	}
	
	/**
	 * Get classes by expression. Expressions use the glob format.
	 * 
	 * @param string $expr expression to search for
	 * @return array of classes
	 */
	static function get($expr, $cache = true){
		if(self::$cache && $cache){
			$ret = self::$cache->get($expr);
			if(is_array($ret)){
				return $ret;
			}
		}
		
		$ret = array();
		
		$expr = ltrim($expr, '\\');
		$expr_path = str_replace('\\',DIRECTORY_SEPARATOR,$expr).'.php';
		$prefixes = self::composer_autoloader()->getPrefixes();
		$wildcard_pos = strpos($expr, '*');

		foreach ($prefixes as $prefix => $dirs) {
			$compare_len = strlen($prefix);
			if($compare_len > $wildcard_pos)
				$compare_len = $wildcard_pos;
			
			if ($wildcard_pos == 0 || 0 === substr_compare($expr, $prefix, 0, $compare_len)) {
				foreach ($dirs as $dir) {
					$s = strlen($dir);
					$g = glob($dir . DIRECTORY_SEPARATOR. $expr_path);
					foreach($g as $v){
						$c = substr($v,$s);
						$c = substr($c,0,-4);
						$ret[] = str_replace(DIRECTORY_SEPARATOR, '\\', $c);
					}
				}
			}
		}
		
		if(self::$cache && $cache){
			self::$cache->set($expr, $ret, 0);
		}
		
		return $ret;
	}

    static function getProjectOrCommon($expr, $cache = true, $project = null){
        if($project === null){
            global $_PROJECT;
            $project = $_PROJECT;
        }

        $all = self::get($expr, $cache);

        if(count($all) == 0){
            return null;
        }

        foreach($all as $a){
            if(substr_compare($a, '\\'.$project, strlen($project) + 1) == 0){
                return $a;
            }
        }

        return $all[0];
    }
	
	/**
	 * @see Libraries::get
	 */
	static function getNSExpression($expr){
		return self::get($expr);
	}
}