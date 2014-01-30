<?php
	require(dirname(__FILE__) . "/common.php");
	
	// Fetch the query string params
	$id = get("id");
	$report = get("report");
	$url = get("url");
	$direction = get("direction");

	function go_to($id) {
		//redirect("?id=" . $id);
		redirect("/id/" . $id);
	}
	
	// If an ID has bene provided
	if ($id != "") {
		if ($id == "random") { // If it's "random" then choose a random gif and go to it
			$available_items = sql_query("SELECT id FROM item");
			$random_id = $available_items[array_rand($available_items)]["id"];

			go_to($random_id);
		} else { // Otherwise
			if (sql_first("SELECT COUNT(*) FROM item WHERE id = '" . $id . "'") > 0) { // Check that id actually exists
				$item = sql_query("SELECT url, direction FROM item WHERE id = '" . $id . "'"); // And load it

				$url = $item["url"];
				$direction = $item["direction"];
				
				// Increment the number of views for this gif
				$views = sql_first("SELECT number_of_views FROM item WHERE id = '" . $id . "'");
				sql_query("UPDATE item SET number_of_views = " . ($views + 1) . " WHERE id = '" . $id . "'");
			} else die("Bad id provided - " . $id);
		}
	} else if ($report != "") {
		if (sql_first("SELECT COUNT(*) FROM item WHERE id = '" . $report . "'") > 0) { // Check that id actually exists
			// Increment the number of reports for this gif
			$reports = sql_first("SELECT number_of_reports FROM item WHERE id = '" . $report . "'");
			if ($reports == "") $reports = 0;
			sql_query("UPDATE item SET number_of_reports = " . ($reports + 1) . " WHERE id = '" . $report . "'");
			print "<script>alert('Gif has been reported!');var random = true;</script>";
		} else die("Bad report id provided - " . $report);	
	} else if ($url != "") { // If a URL has been provided rather than an ID
		if (sql_first("SELECT COUNT(*) FROM item WHERE url = '" . $url . "' and direction = '" . $direction . "'") > 0) { // Check if that URL (and direction) has already been used
			// If so then just load the existing one instead
			$existing_id = sql_first("SELECT id FROM item WHERE url = '" . $url . "' and direction = '" . $direction . "'");
			go_to($existing_id);
		} else { // Otherwise
			// Check we've actually been given a link to a gif
			if (startsWith($url, "http") && endsWith($url, ".gif")) {
				// Look out for dodgy domains
				if (strpos($url, "4chan") !== FALSE || strpos($url, "4cdn.org") !== FALSE) die("Woh - not adding something from that domain!");
				
				// Generate a new ID and save this gif to the database
				$new_id = substr(hash("md5", $url . microtime()), 0, 6);
				sql_query("INSERT INTO item VALUES ('" . $new_id . "', '" . $url . "', '" . $direction . "', '" . $_SERVER["REMOTE_ADDR"] . "', NOW(), 0, 0)");
				
				go_to($new_id); // Then load the new gif
			} else die("Invalid URL passed");
		}
	} else { // Neither an ID or a URL have been provided
		//go_to("random"); // So just load a random gif
		
		print "<script>var random = true;</script>";
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>gifulous!</title>
		
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1,target-densitydpi=device-dpi"/>
		<meta name="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<meta name="HandheldFriendly" content="true"/>
		
		<meta name="description" content="Because you need stupid GIFs, and you need them to cover your screen."/>		
		
		<meta property="og:image" content="./facebook.jpg">
		<link rel="image_src" href="./facebook.jpg">
		
		<link type="text/css" rel="stylesheet" media="all" href="/css/reset.css"/>
		<link type="text/css" rel="stylesheet" media="all" href="/css/main.css"/>
		
		<style>
			#content {
				background-image: url(<?php print $url; ?>);
			}
		</style>
		
		<script>var id = "<?php print $id; ?>";</script>
	</head>

	<body>
		<div id="content" class="<?php print $direction; ?>">&#160;</div>
		<div id="title" title="Click, tap or press any key for another GIF!">&#160;</div>
		
		<a id="add" class="social" href="#" title="Add a new GIF">Add</a>
		<a id="report" class="social" href="#" title="Report this GIF as broken or inappropriate">Report</a>
		<a id="facebook" class="social" href="#" target="_blank" title="Share on Facebook">Share on Facebook</a>
		<a id="twitter" class="social"  href="#" target="_blank" title="Share on Twitter">Share on Twitter</a>
		
		<script src="/js/jquery.js"></script>
		<script src="/js/main.js"></script>
		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-45462422-3', 'gifulo.us');
			ga('send', 'pageview');
		</script>
	</body>
</html>