<?php
/**
 * GitFloat Webdriver is the workhorse for Facebook's Selenium bindings to contol GitFloat sites. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.1
 * @author    Kevin Baugh
 */

namespace Loraxx753;

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
		
		$loaders = array();
		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/import/'.__NAMESPACE__.'/html/output');
		$loaders[] = new \Twig_Loader_Filesystem(APP_DIR.'/base');
		$loader = new \Twig_Loader_Chain($loaders);

		$this->twig = new \Twig_Environment($loader, array('autoescape' => false, 'debug' => true));
	}

	public function run_commit_audit($auditSince, $branch, $commitRegex = false) {
		$repo = $this->client->api('repo')->commits();
		$paginator  = new \Github\ResultPager($this->client);
		$parameters = array($_SESSION['organization'], $_SESSION['repo'], array('sha' => $branch, 'since' => $auditSince));
		$results    = $paginator->fetchAll($repo, 'all', $parameters);

		foreach ($results as $result) {
			if($commitRegex) {
				preg_match($commitRegex, $result['commit']['message'], $matches);
				$result['commit']['message'] = preg_replace($commitRegex, '<b>$1</b>', $result['commit']['message']);				
			}
			if(isset($matches[1]) || !$commitRegex) {
				if(!isset($commits['names'][$result['commit']['author']['name']])) {
					$commits['names'][$result['commit']['author']['name']]['good'] = array();
					$commits['names'][$result['commit']['author']['name']]['bad'] = array();
				}
				$commits['names'][$result['commit']['author']['name']]['good'][] = $this->parse_commit($result);
				$commits['good'][] = $this->parse_commit($result);
			}
			else {
				if(!isset($commits['names'][$result['commit']['author']['name']])) {
					$commits['names'][$result['commit']['author']['name']]['good'] = array();
					$commits['names'][$result['commit']['author']['name']]['bad'] = array();
				}
				$commits['names'][$result['commit']['author']['name']]['bad'][] = $this->parse_commit($result);
				$commits['bad'][] =  $this->parse_commit($result);
			}
		} 

		return $this->twig->render('commit_audit.twig', 
							array('commits' => $commits));
	}

	public function run_hotfix_audit() {
	 	$result = $this->client->api('repos')->commits()->compare($_SESSION['organization'], $_SESSION['repo'], 'dev', 'master');
		$commits = array();
		foreach ($result['commits'] as $commit) {
			$commits[] = $this->parse_commit($commit);
		}
		$result['commits'] = $commits;

		return $this->twig->render('hotfix_audit.twig', array(
							'result' => $result));
	}

	public function run_compare($compareCommitsFrom = 'dev', $compareCommitsTo = 'master') {

		$result = $this->client->api('repos')->commits()->compare($_SESSION['organization'], $_SESSION['repo'], $compareCommitsTo, $compareCommitsFrom);
		$commits = array();
		foreach ($result['commits'] as $commit) {
			$commits[] = $this->parse_commit($commit);
		}
		array_reverse($commits);
		$result['commits'] = $commits;

		return $this->twig->render('compare_branch.twig',
							array('result' => $result,
								'compareCommitsFrom' => $compareCommitsFrom,
								'compareCommitsTo' => $compareCommitsTo));
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

	public function run_set_repo($repo) {
		// unset($_SESSION['repo']);
		$_SESSION['repo'] = $repo;

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

	public function run_error_page($exception) {
		return $exception->getMessage();
	}

	private function parse_commit($commit) {
		$avatar = (isset($commit['author']['avatar_url'])) ? $commit['author']['avatar_url'] : "/assets/img/placeholder.jpg";
		$heading = $commit['commit']['author']['name']." - ".date("m-d-Y @ h:i a", strtotime($commit['commit']['author']['date']));
		$content = "<p>".$commit['commit']['message']."</p>";
		
		return $this->twig->render('bootstrap/media_object.twig', 
							array('image'  => $avatar, 
								  'heading' => $heading,
								  'content' => $content));
	}

}