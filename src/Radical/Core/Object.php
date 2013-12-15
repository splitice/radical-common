<?php
namespace Radical\Core;

//TODO: Make use comments for dependencies and provides
class Object {
	
	/**
	 * calls a toMethod dynamically
	 *
	 * ```
	 * $url = $object->to('url');
	 * //is the same as
	 * $url = $object->toUrl();
	 * ```
	 *
	 * @param string $method method to be called
	 * @param ... arguments to be passed
	 * @return mixed
	 */
	function to($method){
		$method = 'to'.$method;
		if(is_callable(array($this,$method))){
			$args = func_get_args();
			if(count($args) == 1){
				return $this->method();
			}else{
				return call_user_func_array(array($this, $method), array_slice($args, 1));
			}
		}
	}
	
	/**
	 * calls a fromMethod dynamically
	 * 
	 * ```
	 * $object = Class::from('url',$url);
	 * //is the same as
	 * $object = $object->fromUrl($url);
	 * ```
	 * 
	 * @param string $method method to be called
	 * @param ... arguments to be passed
	 * @return mixed
	 */
	static function from($method){
		$method = 'from'.$method;
		$class = get_called_class();
		if(is_callable(array($class,$method))){
			$args = func_get_args();
			if(count($args) == 1){
				return static::$method();
			}else{
				return call_user_func_array(array($class, $method), array_slice($args, 1));
			}
		}
	}
}