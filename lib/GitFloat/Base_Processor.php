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

	public $github;

	protected function use_github() {
		$this->github = new \Github\Client();
		$this->github->authenticate($_SESSION['access_token'], null, \Github\Client::AUTH_HTTP_TOKEN);
		$this->github->getHttpClient()->setOption('user_agent', 'GitFloat');
	}

	protected function use_twig() {
		$loaders = array();
		$explodedClassName = explode("\\", get_called_class());
		array_pop($explodedClassName);
		$explodedClassName[1] = strtolower($explodedClassName[1]);
		$namespace = implode("/", $explodedClassName);

		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/import/'.$namespace.'/html/');
		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/base');
		$loader = new \Twig_Loader_Chain($loaders);

		$this->twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => true));
	}

	private function use_jira() {

	}

	public function run_error_page($exception) {
		return $exception->getMessage();
	}

}