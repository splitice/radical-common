<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;

class NullErrorHandler extends ErrorHandlerBase {
	function error(ErrorBase $error) {
	}
	function exception(ErrorException $error){
	}
	function isNull(){
		return true;
	}
}