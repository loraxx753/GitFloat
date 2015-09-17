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
class Facebook extends \GitFloat\Auth {

	function __construct($config) {
		parent::__construct($config);

		$this->facebook = new \Facebook\Facebook([
									'app_id' => $this->_params['app_id'],
									'app_secret' => $this->_params['app_secret'],
									'default_graph_version' => $this->_params['api_version'],
								]);
	}

	public function get_access_token() {
		$helper = $this->facebook->getRedirectLoginHelper();

		if(isset($_GET['code'])) {
			$accessToken = (string) $helper->getAccessToken();

			// OAuth 2.0 client handler
			$oAuth2Client = $this->facebook->getOAuth2Client();

			// Exchanges a short-lived access token for a long-lived one
			$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

			$_SESSION['oauth']['facebook']['access_token'] = (string) $longLivedAccessToken;
		}
		else {
			$loginUrl = $helper->getLoginUrl('http://gitfloat.dev/login.php');
			header("Location: ".$loginUrl);
		}
	}
}