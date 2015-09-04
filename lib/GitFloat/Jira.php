<?php
/**
 * Flosports Webdriver is the workhorse for Facebook's Selenium bindings to contol Flosports sites. 
 * 
 * @package   Flosports WebDriver
 * @version   0.1
 * @author    Kevin Baugh
 */

namespace GitFloat;

/**
 * Gets JIRA information and tickets
 *
 * @todo  Offer more options for JIRA interaction
 */
class Jira {

	/**
	 * JIRA uses this weird custom field thing. Use the get_fields call to figure them out.
	 * @var array
	 */
	public static $customFields = array(
		"issuesInEpic" => "cf[10007]",
		"releaseCandidate" => "cf[10700]"
	);

	/**
	 * The url of JIRA's api
	 * @var string
	 */
	private static $_url = "https://flocasts.atlassian.net/rest/api/latest/";

	/**
	 * Returns a single ticket from JIRA
	 * 
	 * @param  string $id The ID of the ticket (QA-123)
	 * @return object     The response with the ticket under "issues"
	 */
	public static function get_ticket($id)
	{
		return self::send_data("get", "issue/".$id);
	}

	/**
	 * Default call for making calls that aren't in this class
	 * 
	 * @param  string $type Whether it's get, post, put, etc
	 * @param  string $call The call to make to the api
	 * @return object       The response from the call
	 */
	public static function call($type, $call)
	{
		return self::send_data($type, $call);
	}

	/**
	 * Gets a list of tickets base on a release_candidate label
	 * @param  string $release_candidate Name of the label for hte candidate
	 * @return object                    The response with the tickets under "issues"
	 */
	public static function changelog($release_candidate) {
		return self::search(self::$customFields["releaseCandidate"]."=".$release_candidate);
	}

	/**
	 * Preforms a JQL search
	 * 
	 * @param  string $query The query to run (MUST be url escaped)
	 * @return object        The response from the call
	 */
	public static function search($query) {
		return self::send_data("get", "search?jql=".$query);
	}

	/**
	 * Gets all of the current fields. Used to find out what the custom fields id's are.
	 * 
	 * @return object The response from the call
	 */
	public static function get_fields() {
		return self::send_data("get", "field");
	}

	/**
	 * Preforms the Curl request against the JIRA api
	 * 
	 * @param  string $type    Whether it's get, post, put, etc
	 * @param  string $request The request to send to JIRA
	 * @return object          The JSON decoded response.
	 */
	private static function send_data($type, $request) 
	{
		$options = array(
			CURLOPT_USERPWD => Config::get(array('jira', 'username')).":".Config::get(array('jira', 'password')),
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		);
		return json_decode(Curl::$type(self::$_url.$request, $options));
	}
}
