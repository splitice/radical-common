<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\IToCode;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;
use Radical\CLI\Console\Colors;

class CLIOutputErrorHandler extends ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	
	private $in_error = false;

	function error(ErrorBase $error) {
		if($error->isFatal()){
			throw $error;
		}
	}
	function exception(ErrorException $error){
		if($this->in_error){
			return;
		}
		$this->in_error = true;
		
		$c = Colors::getInstance();
		
		//Code
		if($error instanceof IToCode){
			$code = $error->toCode();
		}else{
			if($error->isFatal()){
				$code = $c->getColoredString('FATAL','red');
			}else{
				$code = $c->getColoredString('ERROR','light_red');
			}
		}
		
		//Format Output
		$message = $error->getMessage();
		if($message{0} != '['){
			$message = ' '.$message;
		}
		$output = sprintf(static::CLI_START,$code,$message);
		
		//If Threaded include ThreadID
		/*$T = Thread::current();
		if($T){//If threading
			if($T->parent || count($T->children)){
				$output = '['.$c->getColoredString('#'.$T->getId(),'cyan').']'.$output;
			}
		}*/
		
		//Output it
		\Radical\CLI\Console\Colors::getInstance()->Output($output);
		
		//OB
		if(ob_get_level()) ob_flush();
		

		$this->in_error = false;
	}
}