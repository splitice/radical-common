<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;

class OutputErrorHandler extends ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	const CLI_HANDLER = '\Radical\Core\ErrorHandling\Handlers\CLIOutputErrorHandler';
	const WEB_HANDLER = '\Radical\Core\ErrorHandling\Handlers\WebOutputErrorHandler';
	private $handler;
	
	function __construct($is_cli = null){
		if($is_cli === null){
			$is_cli = \Radical\Core\Server::isCLI();
		}
		
		if($is_cli)
			$c = self::CLI_HANDLER;
		else
			$c = self::WEB_HANDLER;
		
		$this->handler = new $c;
	}
	
	function error(ErrorBase $error) {
		return $this->handler->error($error);
	}
	function exception(ErrorException $error){
		return $this->handler->exception($error);
	}
}