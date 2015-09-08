<?php 
session_start();
ini_set('display_errors',1);
date_default_timezone_set("America/Chicago");
define('ROOT_DIR', dirname(__FILE__)."/..");
define('APP_DIR', dirname(__FILE__)."/../app");
define('LIB_DIR', dirname(__FILE__)."/../lib");
define('PUBLIC_DIR', dirname(__FILE__));
require_once(ROOT_DIR.'/vendor/autoload.php');

if($_POST) {
	$author = $_POST['author'];
	$call = "run_".$_POST['request'];
	unset($_POST['request']);
	unset($_POST['author']);

	$class = "\\".ucwords($author)."\\Processor";

	$processor = $class::forge();

	try { 
		echo call_user_func_array(array($processor, $call), array_filter($_POST));
	} catch(Exception $e) {
		echo $processor->run_error_page($e);
	}
}
else if(isset($_GET['args']) && strpos($_GET['args'], "minify.js") !== false) {
	$parts = explode("/", $_GET['args']);
	$page = $parts[1];
	$findWidgets = \GitFloat\Config::get(array('widgets', $page));
	$widgets = "";


	foreach ($findWidgets as $foundWidget) {
		$parts = explode("/", $foundWidget);
		$widgets .= file_get_contents(PUBLIC_DIR."/assets/js/import/".$parts[0]."/".$parts[1].".js");
	}
	echo \JShrink\Minifier::minify($widgets);

} else { 
		$loaders = array();

		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR."/base");

		$page = (isset($_GET['page'])) ? $_GET['page'] : 'dashboard';

		$findWidgets = \GitFloat\Config::get(array('widgets', $page));
		$widgets = array();


		foreach ($findWidgets as $foundWidget) {
			$parts = explode("/", $foundWidget);
			$loaders[] = new \Twig_Loader_Filesystem(APP_DIR."/import/".$parts[0]."/html/widgets");
			$widgets[] = array("file" => $parts[1].".twig", "author" => $parts[0]); 

		}
		$loader = new \Twig_Loader_Chain($loaders);

		$twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => true));

		$access_token = (isset($_SESSION['access_token'])) ? $_SESSION['access_token'] : false;
		$organization = (isset($_SESSION['organization'])) ? $_SESSION['organization'] : false;
		$repo = (isset($_SESSION['repo'])) ? $_SESSION['repo'] : false;
		$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : false;

		echo $twig->render('wrapper.twig', array(
										'access_token' => $access_token, 
										'user' => $user,
										'page' => $page,
										'repo' => $repo,
										'organization' => $organization,
										'widgets' => $widgets
										));
} ?>