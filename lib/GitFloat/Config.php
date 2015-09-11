<?php
/**
 * GitFloat is a . 
 * 
 * @package   GitFloat
 * @version   0.1
 * @author    Flosports
 */

namespace GitFloat;

/**
 * Class to handle configuration information. Hi Kristen!
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
		if(is_array($item)) {
			$currentScope = self::$_config;
			foreach ($item as $key) {
				$currentScope = $currentScope->$key;
			}

			return $currentScope;
		}
		else {
			return self::$_config->$item;
		}
	}

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