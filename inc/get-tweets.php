<?php
	session_start();
	require_once("../twitteroauth/twitteroauth.php"); //Path to twitteroauth library
	 
	$twitteruser = "BeautyBox120";
	$notweets = 5;
	$consumerkey = "Q0RSbItem9RcNMNnIobhMg";
	$consumersecret = "1ImNx1LIvWsdsSn15iuhmkL24O1sP7Tx6i8Qy2c0Y";
	$accesstoken = "14282257-I27zg9e0CrWCP0YvEbCV3uRYUgyvLrqJ0IJFEMHVV";
	$accesstokensecret = "e6UAD8AZuMOMcvysVa3D3povRIcCezguEUN19xwmvqk";
	 
	function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
	  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
	  return $connection;
	}
	 
	$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
	 
	$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
	 
	//Check twitter response for errors.
	if ( isset( $tweets->errors[0]->code )) {
	    // If errors exist, print the first error for a simple notification.
	    echo "Error encountered: ".$tweets->errors[0]->message." Response code:" .$tweets->errors[0]->code;
	} else {
	    // No errors exist. Write tweets to json/txt file.
	    $file = $twitteruser."-tweets.txt";
	    $fh = fopen($file, 'w') or die("can't open file");
	    fwrite($fh, json_encode($tweets));
	    fclose($fh);
	      
	    if (file_exists($file)) {
	       echo $file . " successfully written (" .round(filesize($file)/1024)."KB)";
	    } else {
	        echo "Error encountered. File could not be written.";
	    }
	}
?>