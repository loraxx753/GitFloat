<?php
/**
 * GitFloat Webdriver is the workhorse for Facebook's Selenium bindings to contol GitFloat sites. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.1
 * @author    Kevin Baugh
 */

namespace GitFloat;

/**
 * Processes the requests from process.php
 */
abstract class Base_Processor {

	public $client;

	function __construct() {
		$this->client = new \Github\Client();
		$this->client->authenticate($_SESSION['access_token'], null, \Github\Client::AUTH_HTTP_TOKEN);
		$this->client->getHttpClient()->setOption('user_agent', 'GitFloat');
		
		$loaders = array();
		$explodedClassName = explode("\\", get_called_class());
		$namespace = array_shift($explodedClassName);

		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/import/'.$namespace.'/html/output');
		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/base');
		$loader = new \Twig_Loader_Chain($loaders);

		$this->twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => true));
	}

	public function run_error_page($exception) {
		return $exception->getMessage();
	}

}