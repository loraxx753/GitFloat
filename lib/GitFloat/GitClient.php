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
class GitClient {

	public $client;

	public static function forge() {
		return new self();
	}

	public static function setUp() {
		self::$client = new \Github\Client();
		self::client->authenticate($_SESSION['access_token'], null, \Github\Client::AUTH_HTTP_TOKEN);
		self::client->getHttpClient()->setOption('user_agent', 'GitFloat');
	}
}