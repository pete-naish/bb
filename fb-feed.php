<?php
require_once('fbsdk/facebook.php');
 
// connect to app
$config = array();
$config['appId'] = '160229230730533';
$config['secret'] = '0979d9bef7d21930d50efef444bdc084';
$config['fileUpload'] = false; // optional
 

 //187845997900472
//https://graph.facebook.com/oauth/access_token?client_id=160229230730533&client_secret=0979d9bef7d21930d50efef444bdc084&grant_type=client_credentials
// instantiate

// https://graph.facebook.com/187845997900472/feed?access_token=160229230730533|YeOa3AZIsl7PzsVMWSU-vQHivnk
$facebook = new Facebook($config);

$pageid = "187845997900472";

$pagefeed = $facebook->api("/" . $pageid . "/feed");

$picturefeed = $facebook->api("/" . $pageid . "?fields=picture");

$picture = $picturefeed['picture']['data']['url'];

$feedpic = "<div class=\"feed-pic\"><a href=\"https://www.facebook.com/thebeautyboxknebworth\" target=\"_blank\"><img src=" . $picture . "></a></div>";


// echo "<div class=\"fb-feed\">";
 
// set counter to 0, because we only want to display 10 posts
$i = 0;

foreach($pagefeed['data'] as $post) {
 
    if ($post['type'] == 'status' || $post['type'] == 'link' || $post['type'] == 'photo') {
 
        // open up an fb-update div
        echo "<div class=\"status-article\">";
 		echo $feedpic;
 		echo "<div class=\"feed-text\"><p>";
            // post the time
 
            // check if post type is a status
            if ($post['type'] == 'status' && $post['story'] == '') {
                // echo "<h2>Status updated on: " . date("jS M, Y", (strtotime($post['created_time']))) . "</h2>";
                
                echo "<span class=\"feed-content\">" . $post['message'] . "</span>";
                 $i++; // add 1 to the counter if our condition for $post['type'] is met
            }
 
            // check if post type is a link
            if ($post['type'] == 'link') {
                // echo "<h2>Link posted on: " . date("jS M, Y", (strtotime($post['created_time']))) . "</h2>";
                echo "<p>" . $post['name'] . "</p>";
                echo "<p><a href=\"" . $post['link'] . "\" target=\"_blank\">" . $post['link'] . "</a></p>";
                 $i++; // add 1 to the counter if our condition for $post['type'] is met
            }
 
            // check if post type is a photo
            if ($post['type'] == 'photo') {
                echo "<span class=\"feed-time\">Photo posted on: " . date("jS M, Y", (strtotime($post['created_time']))) . "</span>";
                if (empty($post['story']) === false) {
                    echo "<span class=\"feed-content\">" . $post['story'] . "</span>";
                } elseif (empty($post['message']) === false) {
                    echo "<span class=\"feed-content\">" . $post['message'] . "</span>";
                }
                echo "<a class=\"feed-image\" href=\"" . $post['link'] . "\" target=\"_blank\">
                <img src=\"" . $post['picture'] . "\"></a>";
                 $i++; // add 1 to the counter if our condition for $post['type'] is met
            }
 
        echo "</p></div></div>"; // close fb-update div
 
       
    }
 
    //  break out of the loop if counter has reached 10
    if ($i == 5) {
        break;
    }
} // end the foreach statement
 
// echo "</div>";

?>