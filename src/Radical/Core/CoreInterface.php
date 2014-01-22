<?php
namespace Radical\Core;

class CoreInterface {
	static function oneof($object, $class){
		if(is_object($object)) return $object instanceof $class;
		if(is_string($object)){
			if(is_object($class)) $class=get_class($class);
	
			if(class_exists($class)) return is_subclass_of($object, $class) || $object==$class;
			if(interface_exists($class)) {
				if(!class_exists($object)) return false;
				
				$reflect = new \ReflectionClass($object);
				return $reflect->implementsInterface($class);
			}
	
		}
		return false;
	}
}