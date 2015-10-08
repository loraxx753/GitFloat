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
class Google extends \GitFloat\Auth {
	function __construct($config) {
		parent::__construct($config);

		$this->google = new \Google_Client();
		$this->google->setClientId(Config::get('google.client_id'));
	    $this->google->setClientSecret(Config::get('google.client_secret'));
		$this->google->addScope(\Google_Service_Drive::DRIVE_METADATA_READONLY);
		$this->google->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/login.php');

	}
	public function get_access_token() {
		if($this->check_code()) {
			$this->google->authenticate($_GET['code']);
			$access_token = $this->google->getAccessToken();
			$access_token_objects = json_decode($access_token);
			foreach ($access_token_objects as $key => $access_token_object) {
				$_SESSION['oauth']['google'][$key] = $access_token_object;
			}
		}
		else {
			$this->get_code();
		}


	}

	public function get_code() {
		// $this->google->setAuthConfig(json_encode(Config::get('google')));

		$auth_url = $this->google->createAuthUrl();

		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	}

	public static function check_code() {
		if(isset($_GET['code'])) {
			return true;
		}

		return false;
	}
}