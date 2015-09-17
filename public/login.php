<?php 

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ROOT_DIR', dirname(__FILE__)."/..");
require_once(ROOT_DIR.'/vendor/autoload.php');



if(isset($_GET['action']) && $_GET['action'] == 'logout') 
{
	\GitFloat\Auth::destroy();
	session_destroy();
	header('Location: http://' . $_SERVER['HTTP_HOST']);
}
else
{
	if(isset($_GET['provider'])) {
		$_SESSION['provider'] = $_GET['provider'];
	}
	$config = \GitFloat\Config::get(strtolower($_SESSION['provider']));
	$classname = "\GitFloat\Auth\\".ucwords($_SESSION['provider']);
	$auth = new $classname($config);

	$auth->get_access_token();
	if(isset($_SESSION['oauth'][$_SESSION['provider']])) {
		header('Location: http://' . $_SERVER['HTTP_HOST']);
	}
}