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
class Auth {

	protected $_client_id;
	protected $_client_secret;
	protected $_redirect_uri;
	protected $_scope = false;
	protected $_agent;


	public function destroy() {
		unset($_SESSION['oauth']);
	}

	public static function find_access_token($provider = false) {
		if(isset($_SESSION['oauth']))
		{
			if($provider) 
			{
				return $_SESSION['oauth'][$provider]['access_token'];
			}
			return $_SESSION['oauth'];
		}
		return false;
	}

}
