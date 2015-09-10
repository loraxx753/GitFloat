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
 * Default processor to handle organizations and repos.
 */
class Processor extends Base_Processor {

	/**
	 * By default use github.
	 */
	function __construct() {
		$this->use_github();
	}

	/**
	 * Find all the organizations for the current user
	 * @return string Html options for orgs
	 */
	public function run_find_organizations() {
		$orgs = $this->github->api('current_user')->organizations();
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

	/**
	 * Sets the organization to the session
	 * @param  string $org Organization name
	 * @return null
	 */
	public function run_set_organization($org) {
		$_SESSION['organization'] = $org;
	}

	/**
	 * Finds repos for the selected organization
	 * @param  string $organization Organization name
	 * @return string Html options for repos
	 */
	public function run_find_repos($organization) {
		$repos = $this->github->api('organizations')->repositories($organization);
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

	/**
	 * Sets the repository to the session
	 * @param  string $org Repository name
	 * @return null
	 */
	public function run_set_repo($repo) {
		$_SESSION['repo'] = $repo;

	}
}