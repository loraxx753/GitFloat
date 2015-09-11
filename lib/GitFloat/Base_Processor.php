<?php
/**
 * GitFloat is an easy reporter for Git projects that can be extended to handle other applications as well. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.2
 * @author    Kevin Baugh
 */

namespace GitFloat;

/**
 * The base processor for all widgets
 */
abstract class Base_Processor {

	/**
	 * Holds the GitHub object
	 * @var \Github\Client
	 */
	public $github;

	/**
	 * Holds the twig object for view rendering
	 * @var \Twig_Environment
	 */
	public $twig;


	/**
	 * Sets up the GitHub object with the current access token
	 * @return null
	 */
	protected function use_github() {
		$this->github = new \Github\Client();
		$this->github->authenticate($_SESSION['access_token'], null, \Github\Client::AUTH_HTTP_TOKEN);
		$this->github->getHttpClient()->setOption('user_agent', 'GitFloat');
	}

	/**
	 * Sets up the twig object with the widgets html folder
	 * 
	 * @return null
	 */
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

	/**
	 * Returns and error message when hitting an exception
	 * 
	 * @param  object $exception The exception object from the bad processor call
	 * @return string            The message from the bad call
	 */
	public function run_error_page($exception) {
		return $exception->getMessage();
	}

}