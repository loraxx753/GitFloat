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
 * Class to handle configuration information.
 */
class Config {
	/**
	 * All the config information for the app
	 * @var array
	 */
	private static $_config = array();

	/**
	 * Gets an item out of the config json files
	 * 
	 * @param  mixed $item An array or string of what to look for in $_config
	 * @return string      The matching config.
	 */
	static function get($item) {
		self::load();
		$pieces = explode(".", $item);
		$currentScope = self::$_config;
		foreach ($pieces as $piece) {
			$currentScope = $currentScope->$piece;
		}

		return $currentScope;
	}

	/**
	 * Get the environment set on the server. Default is dev
	 * @return string The environment name
	 */
	static function get_env() {
		if(getenv('APP_ENV')) {
			return getenv('APP_ENV');
		} 
		return 'dev';
	}

	/**
	 * Checks for the config elements and loads them if they're not present.
	 * @return [type] [description]
	 */
	private static function load() {
		if(empty($_config)) {
			// Get the main config file
			$json = json_decode(file_get_contents(ROOT_DIR . "/config/config.json"));
			// Get the environment specific config file
			$customJson = json_decode(file_get_contents(ROOT_DIR . "/config/".Config::get_env()."/config.json"));

			self::$_config = (object)array_merge((array)$json, (array)$customJson);
		}
	}

}