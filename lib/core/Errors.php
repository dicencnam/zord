<?php
/**
* Errors
* @package Micro
* @subpackage Core
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
class Errors {

	/**
	* Initialisation
	*/
	public static function init() {
		if(DEBUG){
			error_reporting(-1);
			require_once('phar://'.LIB_FOLDER.'tools/PhpConsole.phar');
			$connector = PhpConsole\Helper::register();
			$handler = PC::getHandler();
			// start handling PHP errors & exceptions
			$handler->start();
			$connector->setPassword(DEBUG_PASSWORD);
		} else {
			error_reporting(0);
			// Sets a user-defined error handler function
			set_error_handler(array(__CLASS__,'errorHandler'));
			// Register a function for execution on shutdown
			register_shutdown_function(array(__CLASS__,'lastErrorHandler'));
			// Sets a user-defined exception handler function
			set_exception_handler(array(__CLASS__,'exceptionHandler'));
		}
	}

	/**
	* Error Handling
	*
	* @param Integer $code Error level
	* @param String $msg Error message
	* @param String $file Filename that the error was raised in
	* @param Integer $line line number the error was raised at
	* @param String $context Variables the error was raised at
	*/
	public static function errorHandler($code, $msg, $file, $line, $context){

		// exception xml
		if ($code==E_WARNING && ((substr_count($msg,'DOMDocument::load()')>0) || (substr_count($msg,'DOMDocument::loadXML()')>0))){
			throw new DOMException($msg);
		} else {
			$error = self::_convertErrorCode($code);
			$msg = sprintf("%s: %s %s #%d", $error, $msg, $file, $line);
			Log::set($msg,'error');
		}
	}

	/**
	* Exception Handling
	*
	* @param Object $e Exception
	*/
	public static function exceptionHandler($e){
		$msg = 'Uncaught exception: '.$e->getMessage().' '.$e->getFile().' #'.$e->getLine();
		Log::set($msg,'error');
	}

	/**
	* Last error Handling
	*/
	public static function lastErrorHandler(){
		$error = error_get_last();
		if (!empty($error)) {
			$errors = self::_convertErrorCode($error['type']);
			$msg = sprintf("%s: %s %s #%d", $errors, $error['message'], $error['file'], $error['line']);
			Log::set($msg,'error');
		}
	}

	/**
	* Convert error constant to string
	*
	* @param Integer $errCode Error constant
	* @return String
	*/
	static function _convertErrorCode($errCode){
		switch ($errCode){
			case E_ERROR:               return 'Error';
			case E_WARNING:             return 'Warning';
			case E_PARSE:               return 'Parse Error';
			case E_NOTICE:              return 'Notice';
			case E_CORE_ERROR:          return 'Core Error';
			case E_CORE_WARNING:        return 'Core Warning';
			case E_COMPILE_ERROR:       return 'Compile Error';
			case E_COMPILE_WARNING:     return 'Compile Warning';
			case E_USER_ERROR:          return 'User Error';
			case E_USER_WARNING:        return 'User Warning';
			case E_USER_NOTICE:         return 'User Notice';
			case E_STRICT:              return 'Strict Notice';
			case E_RECOVERABLE_ERROR:   return 'Recoverable Error';
			case E_DEPRECATED:          return 'Deprecated';
			case E_USER_DEPRECATED:     return 'User Deprecated';
			default:                    return "Unknown error ($errno)";
		}
	}
}
?>
