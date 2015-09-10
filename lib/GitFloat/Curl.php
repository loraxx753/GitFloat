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
 * Runs some curl functions,
 */
class Curl {
	/**
	 * Run the actual curl command
	 * @param  array  $options     Array of options to send to curl
	 * @param  array  $return_json Whether to return the response as json (default false)
	 * @return array               The response from curl
	 */
	private static function run($options, $return_json)
	{
		$ch = curl_init();
		// set URL and other appropriate options
		foreach($options as $constant => $param)
		{
			curl_setopt($ch, $constant, $param);
		}
		// grab URL and pass it to the browser
		$dump = curl_exec($ch);
		// close cURL resource, and free up system resources
		curl_close($ch);
		if($return_json) {
			$dump = json_decode($dump);
		}
		return $dump;
	}

	/**
	 * Issue a GET request 
	 * @param  string $url         Where to issue it
	 * @param  array  $subOptions  Any custom options
	 * @param  array  $return_json Whether to return the response as json (default false) 
	 * @return array 
	 */
	public static function get($url = '', $subOptions = null, $return_json=false)
	{
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0, 
		);
		if($subOptions)
		{
			foreach($subOptions as $key => $value)
			{
				$options[$key] = $value;
			}
		}
		return self::run($options, $return_json);
	}
	
	/**
	 * Issue a DELETE request
	 * @param  string $url         Where to issue it
	 * @param  array  $subOptions  Any custom options
	 * @param  array  $return_json Whether to return the response as json (default false)
	 * @return array 
	 */
	public static function delete($url = '', $subOptions = null, $return_json=false)
	{
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_CUSTOMREQUEST  => "DELETE",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0, 
		);
		if($subOptions)
		{
			foreach($subOptions as $key => $value)
			{
				$options[$key] = $value;
			}
		}
		return self::run($options, $return_json);
	}

	/**
	 * Issue a PATCH request
	 * @param  string $url         Where to issue it
	 * @param  array  $postData    The post data to send
	 * @param  array  $subOptions  Any custom options
	 * @param  array  $return_json Whether to return the response as json (default false)
	 * @return array
	 */
	public static function patch($url = '', $postData = null, $subOptions = null, $return_json=false) 
	{
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0, 
			CURLOPT_CUSTOMREQUEST  => "PATCH",
			CURLOPT_POSTFIELDS     => $postData,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($postData)
			)
		);
		if($subOptions)
		{
			foreach($subOptions as $key => $value)
			{
				$options[$key] = $value;
			}
		}
		return self::run($options, $return_json);
	}

	/**
	 * Issue a PUT request
	 * @param  string $url         Where to issue it
	 * @param  array  $postData    The post data to send
	 * @param  array  $subOptions  Any custom options
	 * @param  array  $return_json Whether to return the response as json (default false)
	 * @return array
	 */
	public static function put($url = '', $postData = null, $subOptions = null, $return_json=false) 
	{
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0, 
			CURLOPT_CUSTOMREQUEST  => "PUT",
			CURLOPT_POSTFIELDS     => $postData,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($postData)
			)
		);
		if($subOptions)
		{
			foreach($subOptions as $key => $value)
			{
				$options[$key] = $value;
			}
		}
		return self::run($options, $return_json);
	}

	/**
	 * Issue a POST request
	 * @param  string $url         Where to issue it
	 * @param  array  $postData    The post data to send
	 * @param  array  $subOptions  Any custom options
	 * @param  array  $return_json Whether to return the response as json (default false)
	 * @return array
	 */
	public static function post($url = '', $postData = null, $subOptions = null, $return_json=false) 
	{
		if(gettype($postData) == 'array') {
			$postData = json_encode($postData);
		}
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0, 
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => $postData,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($postData)
			)
		);
		if($subOptions)
		{
			foreach($subOptions as $key => $value)
			{
				$options[$key] = $value;
			}
		}
		return self::run($options, $return_json);
	}
}