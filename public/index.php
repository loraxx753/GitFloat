<?php 
session_start();
ini_set('display_errors',1);
date_default_timezone_set("America/Chicago");
define('ROOT_DIR', dirname(__FILE__)."/..");
define('APP_DIR', dirname(__FILE__)."/../app");
define('LIB_DIR', dirname(__FILE__)."/../lib");
define('PUBLIC_DIR', dirname(__FILE__));
require_once(ROOT_DIR.'/vendor/autoload.php');
function compress( $minify ) 
{
	/* remove comments */
	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

    /* remove tabs, spaces, newlines, etc. */
	$minify = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $minify );
		
    return $minify;
}

if($_POST) {
	if($_POST['author'] == 'GitFloat') {
		$author = $_POST['author'];
		$call = "run_".$_POST['request'];
		$class = "\\".ucwords($author)."\\Processor";

		$processor = new $class();
		unset($_POST['request']);
		unset($_POST['author']);


		try { 
			echo call_user_func_array(array($processor, $call), array_filter($_POST));
		} catch(Exception $e) {
			echo $processor->run_error_page($e);
		}
	} else {
		$namespace = ucwords($_POST['author'])."\\".ucwords($_POST['request'], "_");
		unset($_POST['request']);
		unset($_POST['author']);

		$class = $namespace."\\Processor";

		$processor = new $class();
		try { 
			echo call_user_func_array(array($processor, 'run'), array_filter($_POST));
		} catch(Exception $e) {
			echo $processor->run_error_page($e);
		}

	}

} else if(isset($_GET['args']) && strpos($_GET['args'], "minify.js") !== false) {
	$parts = explode("/", $_GET['args']);
	$page = $parts[1];
	$findWidgets = \GitFloat\Config::get(array('widgets', $page));
	$widgets = "";


	foreach ($findWidgets as $foundWidget) {
		$parts = explode("/", $foundWidget);
		$widgets .= @file_get_contents(APP_DIR."/import/".$parts[0]."/".$parts[1]."/js/"."script.js");
	}
	echo \JShrink\Minifier::minify($widgets);

 } else if(isset($_GET['args']) && strpos($_GET['args'], "minify.css") !== false) {
	$parts = explode("/", $_GET['args']);
	$page = $parts[1];
	$findWidgets = \GitFloat\Config::get(array('widgets', $page));
	$widgets = "";


	header('Content-type: text/css');
	ob_start("compress");
	foreach ($findWidgets as $foundWidget) {
		$parts = explode("/", $foundWidget);
		@include(APP_DIR."/import/".$parts[0]."/css/".$parts[1].".css");
	}
	ob_end_flush();
} else { 
		$loaders = array();

		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR."/base");

		$page = (isset($_GET['page'])) ? $_GET['page'] : \GitFloat\Config::get('homepage');

		$findWidgets = \GitFloat\Config::get('widgets');
		$loader = new Twig_Loader_Filesystem(APP_DIR."/base");
		foreach ($findWidgets->{$page} as $foundWidget) {
			$parts = explode("/", $foundWidget);
			$loader->addPath(APP_DIR."/import/".$parts[0]."/".$parts[1]."/html/", str_replace("/", "_", $foundWidget));
			$widgets[] = array("file" => "@".str_replace("/", "_", $foundWidget)."/widget.twig", "author" => $parts[0]); 

		}
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
										'widgets' => $widgets,
										'pages' => array_keys(get_object_vars($findWidgets)),
										'homepage' => \GitFloat\Config::get('homepage')
										));
} ?>