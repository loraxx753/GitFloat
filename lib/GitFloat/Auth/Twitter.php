<?php
/**
 * GitFloat is an easy reporter for Git projects that can be extended to handle other applications as well. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.2
 * @author    Kevin Baugh
 */

namespace GitFloat\Auth;

use \GitFloat\Curl;
use \GitFloat\Config;
use \Abraham\TwitterOAuth\TwitterOAuth;

/**
 * The base processor for all widgets
 */
class  Twitter extends \GitFloat\Auth {

	function __construct($config) {
		parent::__construct($config);

		$this->twitter = new TwitterOAuth($this->_params['consumer_key'], $this->_params['consumer_secret']);
	}

	public function get_access_token() {
		if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] === $_REQUEST['oauth_token']) {
			$this->twitter->setOAuthToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$access_token = $this->twitter->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier']));

			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			foreach ($access_token as $key => $param) {
				$_SESSION['oauth']['twitter'][$key] = $param;	
			}
		}
		else {
			$access_token = $this->twitter->oauth("oauth/request_token", array("oauth_callback" => Config::get('twitter.oauth_callback')));

			$_SESSION['oauth_token'] = $access_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

			$url = $this->twitter->url("oauth/authenticate", array("oauth_token" => $access_token['oauth_token']));

			header("Location: $url");
		}
	}
}