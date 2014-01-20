<?php
namespace Radical\CLI\Output;
class LogFile {
	private $filename;
	const DATE_FORMAT = 'd-m-y H:i:s.';
	
	function __construct($file){
		$this->filename = $file;
	}
	
	function write($line){
		$file = fopen($this->filename,'ct+');
		if(!$file){
			return;
		}
		fseek($file,0,SEEK_END);
		$line = rtrim($line,"\r\n")."\r\n";
		$line = date(self::DATE_FORMAT).' '.$line;
		fwrite($file,$line);
		fclose($file);
	}
	function errorCheck($line){
		$file = fopen($this->filename,'ct+');
		fseek($file,0,SEEK_END);
		$start_pos = ftell($file);
		self::Write($line);
		$end_pos = ftell($file);
		return new Log\ErrorCheck($file, $start_pos, $end_pos);
	}
}