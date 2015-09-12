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

/**
 * The base processor for all widgets
 */
class  Github extends \GitFloat\Auth{

	private $_authorizeURL = 'https://github.com/login/oauth/authorize';
	private $_tokenURL = 'https://github.com/login/oauth/access_token';

	function __construct($config) {

		foreach($config as $key => $param) {
			$key = "_$key";
			$this->$key = $param;
		}
	}

	public function get_access_token() {
		if($this->check_code()) {
			$response = Curl::post($this->_tokenURL, array(
				'client_id' => $this->_client_id,
				'client_secret' => $this->_client_secret,
				'redirect_uri' => $this->_redirect_uri,
				'state' => $_SESSION['state'],
				'code' => $_GET['code']
			), array(CURLOPT_HTTPHEADER => array('Accept: application/json')), true);
			$_SESSION['oauth']['github']['access_token'] = $response->access_token;
			return true;
		}
		else {
			return false;
		}


	}

	public function get_code() {
		$params = array(
			'client_id' => $this->_client_id,
			'redirect_uri' => $this->_redirect_uri,
			'scope' => $this->_scope,
			'state' => $this->create_state()
		);
		$_SESSION['state'] = $params['state'];

		header('Location: ' . $this->_authorizeURL . '?' . http_build_query($params));
	}

	public static function check_code() {
		if(isset($_GET['code']) && isset($_SESSION['state'])) {
			if($_GET['state'] == $_SESSION['state']) {
				return true;
			}
		}

		return false;
	}

	private function create_state() {
		return hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
	}



}
