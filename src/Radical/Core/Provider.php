<?php
namespace Radical\Core;

class Provider {
	static function find($id,$array = false){
		$idExpanded = explode('.',$id);
		if($idExpanded[0] == 'php'){//php dependency -- simple check
			$phpClass = str_replace('.',DIRECTORY_SEPARATOR,substr($id,4));
			$phpClass = Libraries::toClass($phpClass);
			if(class_exists($phpClass) || interface_exists($phpClass)){
				return $phpClass;
			}
		}
		if($idExpanded[0] == 'lib'){//custom lib reference
			$found = array();
			foreach(Libraries::getAllClass() as $class){
				if(CoreInterface::oneof($class, '\\Core\\Object')){
					if(in_array($id, $class::__getProvides())){
						$found[] = $class;
					}
				}
			}
			if($found){
				if(count($found) == 1){
					return $found[0];
				}
				return $found;
			}
		}
		if($idExpanded[0] == 'interface'){//everything using an interface
			$interface = implode('\\',array_slice($idExpanded,1));
			$found = array();
			foreach(Libraries::getAllClass() as $class){
				if(CoreInterface::oneof($class, $interface)){
					$found[] = $class;
				}
			}
			if($found){
				if(count($found) == 1 && !$array){
					return $found[0];
				}
				return $found;
			}
		}
		if($array) return array();
	}
}