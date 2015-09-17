<?php 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *  THIS IS A SLIGHT MODIFICATION OF https://gist.github.com/aaronpk/3612742 by https://github.com/aaronpk  
 *  
 * * * * * * * * * * * * * * * * * * * * * * * * * * * */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ROOT_DIR', dirname(__FILE__)."/..");
require_once(ROOT_DIR.'/vendor/autoload.php');

$config = \GitFloat\Config::get('facebook');

$auth = new \GitFloat\Auth\Facebook($config);

if(isset($_GET['action']) && $_GET['action'] == 'logout') 
{
	$auth->destroy();
	header('Location: http://' . $_SERVER['HTTP_HOST']);
}
else
{
	$auth->get_access_token();
	header('Location: http://' . $_SERVER['HTTP_HOST']);
}