<?php
/**
 * Compare branch takes two branches and finds commits on one that aren't on another. 
 * 
 * @package   GitFloat
 * @version   1.0
 * @author    Kevin Baugh
 */

namespace Loraxx753\Compare_Branch;

/**
 * Processes the compare branch request
 */
class Processor extends \GitFloat\Base_Processor {

	function __construct() {
		$this->use_github();
		$this->use_twig();

	}

	/**
	 * Rungs the branch compare between two branches
	 * @param  string $compareCommitsFrom Branch with latest information
	 * @param  string $compareCommitsTo   Branch with NOT lastest information
	 * @return string                     Twig response
	 */
	public function run($compareCommitsFrom = 'dev', $compareCommitsTo = 'master') {

		$result = $this->github->api('repos')->commits()->compare($_SESSION['organization'], $_SESSION['repo'], $compareCommitsTo, $compareCommitsFrom);
		$commits = array();
		foreach ($result['commits'] as $commit) {
			$commits[] = $this->parse_commit($commit);
		}
		// Have to reverse so it's latest first.
		array_reverse($commits);
		$result['commits'] = $commits;

		return $this->twig->render('output.twig',
							array('result' => $result,
								'compareCommitsFrom' => $compareCommitsFrom,
								'compareCommitsTo' => $compareCommitsTo));
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