<?php
/**
* Bootstrap
* @package Micro
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
session_start();
define('DS',DIRECTORY_SEPARATOR);
define('ROOT',dirname(dirname(__file__)).DS);
define('CONFIG_FOLDER',ROOT.'config'.DS);
define('LIB_FOLDER',ROOT.'lib'.DS);
require_once(CONFIG_FOLDER.'config_user.php');
require_once(CONFIG_FOLDER.'config.php');
require_once(LIB_FOLDER.'core'.DS.'Log.php');
require_once(LIB_FOLDER.'core'.DS.'Errors.php');
Errors::init();
require_once(LIB_FOLDER.'core'.DS.'Autoloader.php');
Autoloader::start();
// request
$controlerClName = CONTROLER;
$controler = new $controlerClName();
$controler->route();
?>
