<?php
	session_start();
	
	ob_start("ob_gzhandler"); // Compress page to increase load time

	// Common imports
	require(dirname(__FILE__) . "/lib/class.MySQL.php");
	require(dirname(__FILE__) . "/settings.php");

	// Initiate our database handler
	$mysql = new mysql($settings["mysql"]["database"], $settings["mysql"]["username"], $settings["mysql"]["password"]);
	
	// Helper function for creating JSON responses
	function json_output($data) {
		header("Content-Type: application/json");
		
		echo(
			json_encode($data)
		);
	}
	
	// Create a JSON response giving an error message
	function json_error($message) {
		json_output(array("error" => $message));
		exit();
	}
	
	// Fetches a get param and strips out any mysql code
	function get($name) {
		return
			mysql_real_escape_string(
				trim(
					$_GET[$name]
				)
			);
	}

	function redirect($url) {
		header("Location: " . $url);
		exit;
	}
	
	function startsWith($haystack, $needle) {
		return $needle === "" || strpos($haystack, $needle) === 0;
	}

	function endsWith($haystack, $needle) {
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}	
	
	// Returns the first element in an array
	function array_first($array) {
		$keys = array_keys($array);
		
		return $array[$keys[0]];
	}
	
	// Runs a SQL query
	function sql_query($query) {
		return $GLOBALS["mysql"]->ExecuteSQL($query);
	}
	
	// Runs a SQL query and returns the 1st result only
	function sql_first($query) {
		$results = $GLOBALS["mysql"]->ExecuteSQL($query);
		
		return array_first($results);
	}
	
	// Helper for checking that user inputs have been provided
	function input_required($input, $message) {
		if ($input == '')
			json_error($message);
	}
	
	// My own SQL function - less fancy but more robust
	function run_query($query) {
		$username = $GLOBALS["settings"]["mysql"]["username"];
		$password = $GLOBALS["settings"]["mysql"]["password"];
		$database = $GLOBALS["settings"]["mysql"]["database"];
		$host     = $GLOBALS["settings"]["mysql"]["host"];

		mysql_connect($host, $username, $password);

		@mysql_select_db($database) or die("Unable to select database");

		$result = mysql_query($query);
		
		if ($result === false) {
			echo "SQL Error: <br/>";
			echo $query . "<br/>";
			echo mysql_error();
		}

		mysql_close();
		
		return $result;
	}
	
	// Helper for making cURL calls
	function curl($url, $data=array(), $format="") {
		if (strrpos($url, "?") == false) $url .= "?";
		
		foreach($data as $key => $val) {
			$url .= $key . "=" . $val . "&";
		}

		$curl = curl_init($url);

		$headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$headers[] = 'Connection: Keep-Alive';
		$headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADER => 0,
			CURLOPT_USERAGENT => "Mozilla/5.0 (compatible; YourCoolBot/1.0; +http://yoursite.com/botinfo)",
			CURLOPT_REFERER => "http://en.wikipedia.org/",
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => 1
		);

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);

		if ($format == "json")
			$response = json_decode($response, true);

		if ($format == "xml")
			$response = json_decode(
				json_encode(
					(array)simplexml_load_string($response)
				),
				1
			);
			
		return $response;		
	}
?>