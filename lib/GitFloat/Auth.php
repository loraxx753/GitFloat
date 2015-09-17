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
abstract class Auth {

	protected $_params = array();

	function __construct($config) {

		foreach($config as $key => $value) {
			$this->_params[$key] = $value;
		}
	}

	public function destroy() {
		unset($_SESSION['oauth']);
	}

	public function get_access_token() {}

	public static function find_access_tokens($provider = false) {
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
