<?php 

// Starts the session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Sets up all the File Constants
define('ROOT_DIR', dirname(__FILE__)."/..");
define('APP_DIR', dirname(__FILE__)."/../app");
define('LIB_DIR', dirname(__FILE__)."/../lib");
define('PUBLIC_DIR', dirname(__FILE__));
//Load up composer
require_once(ROOT_DIR.'/vendor/autoload.php');


// Set the timezone from the config
date_default_timezone_set(\GitFloat\Config::get('timezone'));

/**
 * Minify's the css
 * @param  string $minify CSS to minify
 * @return string         Minified CSS
 */
function compress( $minify ) 
{
	//remove comments
	$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

    //remove tabs, spaces, newlines, etc.
	$minify = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $minify );
		
    return $minify;
}
// If there's a post
if($_POST) {
	// And the author is GitFloat
	if($_POST['author'] == 'GitFloat') {
		// Then the setup is a little different
		$author = $_POST['author'];
		$call = "run_".$_POST['request'];
		$class = "\\".ucwords($author)."\\Processor";

		$processor = new $class();
		unset($_POST['request']);
		unset($_POST['author']);

		// It'd be \GitFloat\Processor::run_widget_name(); 
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
		// instead of \Author\Widget_Name\Processor::run()
		try { 
			echo call_user_func_array(array($processor, 'run'), array_filter($_POST));
		} catch(Exception $e) {
			echo $processor->run_error_page($e);
		}

	}
// If the call is to get minify.js
} else if(isset($_GET['args']) && strpos($_GET['args'], "minify.js") !== false) {
	$parts = explode("/", $_GET['args']);
	$page = $parts[1];
	$findWidgets = \GitFloat\Config::get(array('widgets', $page));
	$widgets = "";

	// Get the js for all the registered widgets for the current page.
	foreach ($findWidgets as $foundWidget) {
		$parts = explode("/", $foundWidget);
		$widgets .= @file_get_contents(APP_DIR."/import/".$parts[0]."/".$parts[1]."/js/"."script.js");
	}
	// Minify and show it.
	echo \JShrink\Minifier::minify($widgets);
// If the call is to minify.css
} else if(isset($_GET['args']) && strpos($_GET['args'], "minify.css") !== false) {
	$parts = explode("/", $_GET['args']);
	$page = $parts[1];
	$findWidgets = \GitFloat\Config::get(array('widgets', $page));
	$widgets = "";


	header('Content-type: text/css');
	ob_start("compress");
	// Same thing as the js, just for css.
	foreach ($findWidgets as $foundWidget) {
		$parts = explode("/", $foundWidget);
		@include(APP_DIR."/import/".$parts[0]."/css/".$parts[1].".css");
	}
	ob_end_flush();
// If the call is just for a page
} else { 

		// Check to see if the page is set, if not then it's the homepage
		$page = (isset($_GET['page'])) ? $_GET['page'] : \GitFloat\Config::get('homepage');

		$findWidgets = \GitFloat\Config::get('widgets');

		// Add the base html snippets
		$loader = new Twig_Loader_Filesystem(APP_DIR."/base");

		// Go through every registered html location and add the twigs from there
		foreach ($findWidgets->{$page} as $foundWidget) {
			$parts = explode("/", $foundWidget);
			$loader->addPath(APP_DIR."/import/".$parts[0]."/".$parts[1]."/html/", str_replace("/", "_", $foundWidget));
			// get the widget twig for each registered widget for the page.
			$widgets[] = array("file" => "@".str_replace("/", "_", $foundWidget)."/widget.twig", "author" => $parts[0]); 

		}
		// Set those twigs up.
		$twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => true));

		$access_token = \GitFloat\Auth::find_access_tokens();
		$organization = (isset($_SESSION['organization'])) ? $_SESSION['organization'] : false;
		$repo = (isset($_SESSION['repo'])) ? $_SESSION['repo'] : false;
		$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : false;

		// Echo the twig
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