<?php
/**
 * GitFloat Webdriver is the workhorse for Facebook's Selenium bindings to contol GitFloat sites. 
 * 
 * @package   GitFloat WebDriver
 * @version   0.1
 * @author    Kevin Baugh
 */

namespace Loraxx753\Compare_Branch;

/**
 * Processes the requests from process.php
 */
class Processor extends \GitFloat\Base_Processor {

	function __construct() {
		$this->use_github();
		$this->use_twig();

	}

	public function run() {
	 	$result = $this->github->api('repos')->commits()->compare($_SESSION['organization'], $_SESSION['repo'], 'dev', 'master');
		$commits = array();
		foreach ($result['commits'] as $commit) {
			$commits[] = $this->parse_commit($commit);
		}
		$result['commits'] = $commits;

		return $this->twig->render('hotfix_audit.twig', array(
							'result' => $result));
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