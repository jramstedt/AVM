<?php
class Logger {
	private $finest = 0;
	private $finer = 1;
	private $fine = 2;
	private $config = 3;
	private $info = 4;
	private $warning = 5;
	private $severe = 6;

	private static function getVariables($backtrace) {
		$setvar['className'] = $backtrace[1]['class'];
		$setvar['method'] = $backtrace[1]['function'];
		$setvar['lineNumber'] = $backtrace[0]['line'];
		$setvar['filename'] = $backtrace[0]['file'];
		$setvar['username'] = Sessionmanager::getUsername();

		return $setvar;
	}

	private static function query($level, $message, $get) {
		if(LOG_SAVELEVEL > $level)
			return;
			
		$serverQuery = "INSERT INTO log (message, date, class, method, linenumber, file, username, level)
						VALUES('$message', NOW(),'{$get['className']}', '{$get['method']}',
						'{$get['lineNumber']}','{$get['filename']}','{$get['username']}','$level')";

		Kernel::mysqli()->query($serverQuery);
	}

	public static function finest($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('finest', $message, $get);
	}

	public static function finer($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('finer', $message, $get);
	}

	public static function fine($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('fine', $message, $get);
	}

	public static function config($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('config', $message, $get);
	}

	public static function info($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('info', $message, $get);
	}

	public static function warning($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('warning', $message, $get);
	}

	public static function severe($message) {
		$get = Logger::getVariables(debug_backtrace());
		Logger::query('severe', $message, $get);
	}
}
?>
