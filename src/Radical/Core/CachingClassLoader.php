<?php
namespace Radical\Core;

/**
 * ApcClassLoader implements a wrapping autoloader cached in APC for PHP 5.3.
 *
 * It expects an object implementing a findFile method to find the file. This
 * allow using it as a wrapper around the other loaders of the component (the
 * ClassLoader and the UniversalClassLoader for instance) but also around any
 * other autoloader following this convention (the Composer one for instance)
 *
 *     $loader = new ClassLoader();
 *
 *     // register classes with namespaces
 *     $loader->add('Symfony\Component', __DIR__.'/component');
 *     $loader->add('Symfony',           __DIR__.'/framework');
 *
 *     $cachedLoader = new ApcClassLoader('my_prefix', $loader);
 *
 *     // activate the cached autoloader
 *     $cachedLoader->register();
 *
 *     // eventually deactivate the non-cached loader if it was registered previously
 *     // to be sure to use the cached one.
 *     $loader->unregister();
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kris Wallsmith <kris@symfony.com>
 *
 * @api
 */
class CachingClassLoader
{
	private $prefix;

	/**
	 * The class loader object being decorated.
	 *
	 * @var \Symfony\Component\ClassLoader\ClassLoader
	 *   A class loader object that implements the findFile() method.
	 */
	protected $decorated;
	
	protected $base;
	protected $base_len;

	/**
	 * Constructor.
	 *
	 * @param string $prefix      The APC namespace prefix to use.
	 * @param object $decorated   A class loader object that implements the findFile() method.
	 *
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 *
	 * @api
	 */
	public function __construct($prefix, $decorated, $base)
	{
		if (!method_exists($decorated, 'findFile')) {
			throw new \InvalidArgumentException('The class finder must implement a "findFile" method.');
		}

		$this->prefix = $prefix;
		$this->decorated = $decorated;
		$this->base = $base;
		$this->base_len = strlen($this->base);
		
		//$this->load_common();
	}
	
	function load_common(){
		$index_key = ':'.$this->base.'index';
		$index = apc_fetch($index_key);
		if($index){
			foreach($index as $i){
				include_once $this->base.$i;
			}
		}else{
			$index = $this->calculate_load_list();
			apc_store($index_key, $index, 600);
		}
	}

	/**
	 * Registers this instance as an autoloader.
	 *
	 * @param Boolean $prepend Whether to prepend the autoloader or not
	 */
	public function register($prepend = false)
	{
		spl_autoload_register(array($this, 'loadClass'), true, $prepend);
	}

	/**
	 * Unregisters this instance as an autoloader.
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));
	}

	/**
	 * Loads the given class or interface.
	 *
	 * @param string $class The name of the class
	 *
	 * @return Boolean|null True, if loaded
	 */
	public function loadClass($class)
	{
		if ($file = $this->findFile($class)) {
			require $file;

			return true;
		}
	}

	/**
	 * Finds a file by class name while caching lookups to APC.
	 *
	 * @param string $class A class name to resolve to file
	 *
	 * @return string|null
	 */
	public function findFile($class)
	{
		$file = apc_fetch($this->prefix.$class);
		if (false === $file) {
			$file = $this->decorated->findFile($class);
			if(substr_compare($file, $this->base, 0, $this->base_len) == 0){
				apc_store($this->prefix.$class, substr($file,$this->base_len));
			}
		}else{
			$file = $this->base.$file;
		}

		return $file;
	}
	
	function calculate_load_list(){
		$keys = array();
		foreach (new \APCIterator('user', '/^'.preg_quote($this->prefix,'/').'/') as $key) {
			$keys[$key['value']] = $key['nhits'];
		}
		arsort($keys);
		$max = max($keys)*0.7;
		$keys = array_filter($keys, function($a) use ($max){
			return $a >= $max;
		});
		$keys = array_slice($keys, 0, 100);
		return array_keys($keys);
	}

	/**
	 * Passes through all unknown calls onto the decorated object.
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->decorated, $method), $args);
	}

}