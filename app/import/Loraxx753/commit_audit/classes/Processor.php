<?php
/**
 * GitFloat Webdriver is the workhorse for Facebook's Selenium bindings to contol GitFloat sites. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.1
 * @author    Kevin Baugh
 */

namespace Loraxx753\Commit_Audit;

/**
 * Processes the requests from process.php
 */
class Processor extends \GitFloat\Base_Processor {

	function __construct() {
		$this->use_github();
		$this->use_twig();

	}

	public function run($auditSince, $branch, $commitRegex = false) {
		$repo = $this->github->api('repo')->commits();
		$paginator  = new \Github\ResultPager($this->github);
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

		return $this->twig->render('output.twig', 
							array('commits' => $commits));
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