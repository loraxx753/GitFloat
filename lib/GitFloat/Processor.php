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
class Processor {

	public $client;

	public static function forge() {
		return new self();
	}

	function __construct() {
		$this->client = new \Github\Client();
		$this->client->authenticate($_SESSION['access_token'], null, \Github\Client::AUTH_HTTP_TOKEN);
		$this->client->getHttpClient()->setOption('user_agent', 'GitFloat');
	}

	public function run_find_organizations() {
		$orgs = $this->client->api('current_user')->organizations();
		echo "<option>".htmlspecialchars(" -- Select an Organization -- ")."</option>";
		foreach ($orgs as $org) {
			if($_SESSION['organization'] == $org['login']) {
				echo "<option selected='selected'>$_SESSION[organization]</option>";
			}
			else {
				echo "<option value='$org[login]'>$org[login]</option>";
			}
		}
	}

	public function run_set_organization($org) {
		// unset($_SESSION['organization']);
		$_SESSION['organization'] = $org;
	}

	public function run_find_repos($organization) {
		$repos = $this->client->api('organizations')->repositories($organization);
		var_dump($repos);
		echo "<option>".htmlspecialchars(" -- Select an Repository -- ")."</option>";
		foreach ($repos as $repo) {
			if($_SESSION['repo'] == $repo['name']) {
				echo "<option selected='selected'>$_SESSION[repo]</option>";
			}
			else {
				echo "<option value='$repo[name]'>$repo[name]</option>";
			}
		}
	}

	public function run_set_repo($repo) {
		// unset($_SESSION['repo']);
		$_SESSION['repo'] = $repo;

	}
	
	public function run_error_page($exception) {
		return $exception->getMessage();
	}
}