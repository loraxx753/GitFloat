<?php
/**
 * Commit Audit takes commits from a certain time period and matches them to a regex. 
 * 
 * @package   GitFloat
 * @version   1.0
 * @author    Kevin Baugh
 */

namespace Loraxx753\Commit_Audit;

/**
 * Processes the commit audit request
 */
class Processor extends \GitFloat\Base_Processor {

	/**
	 * Use github and twig
	 */
	function __construct() {
		$this->use_github();
		$this->use_twig();

	}

	/**
	 * Runs the commit audit and presents results
	 * @param  string  $auditSince  Time frame to pull commits from
	 * @param  string  $branch      Branch name
	 * @param  mixed $commitRegex   Default false, regex to compare commits against
	 * @return string               Twig response
	 */
	public function run($auditSince, $branch, $commitRegex = false) {
		$repo = $this->github->api('repo')->commits();
		$paginator  = new \Github\ResultPager($this->github);
		$parameters = array($_SESSION['organization'], $_SESSION['repo'], array('sha' => $branch, 'since' => $auditSince));
		$results    = $paginator->fetchAll($repo, 'all', $parameters);

		foreach ($results as $result) {
			// If it matches the regex, then bold the match
			if($commitRegex) {
				preg_match($commitRegex, $result['commit']['message'], $matches);
				$result['commit']['message'] = preg_replace($commitRegex, '<b>$1</b>', $result['commit']['message']);				
			}
			// If there's no regex OR there's a match, then it's good
			if(isset($matches[1]) || !$commitRegex) {
				// If there's no array for this person yet
				if(!isset($commits['names'][$result['commit']['author']['name']])) {
					$commits['names'][$result['commit']['author']['name']]['good'] = array();
					$commits['names'][$result['commit']['author']['name']]['bad'] = array();
				}
				$commits['names'][$result['commit']['author']['name']]['good'][] = $this->parse_commit($result);
				$commits['good'][] = $this->parse_commit($result);
			}
			// Else it's bad
			else {
				// If there's no array for this person yet
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

	/**
	 * Makes the commits pretty
	 * @param  array   $commit The contents of the commit
	 * @return string          Twig response
	 */
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