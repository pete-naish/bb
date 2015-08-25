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

$statuslink = "https://www.facebook.com/" . $pageid . "/posts/";

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

                $statuslink .= str_replace('187845997900472_', '', $post['id']);

                echo "<span class=\"feed-time\"><a href=\"" . $statuslink . "\">" . pretty_relative_time(strtotime($post['created_time'])) . "</a></span>";
                // echo "<span class=\"feed-time\">" . pretty_relative_time(strtotime($post['created_time'])) . "</span>";
                echo "<span class=\"feed-content\">" . $post['message'] . "</span>";
                 $i++; // add 1 to the counter if our condition for $post['type'] is met
            }
 
            // check if post type is a link
            if ($post['type'] == 'link') {
                // echo "<h2>Link posted on: " . date("jS M, Y", (strtotime($post['created_time']))) . "</h2>";
                echo "<span class=\"feed-time\"><a href=\"" . $post['link'] . "\">" . pretty_relative_time(strtotime($post['created_time'])) . "</a></span>";
                echo "<p>" . $post['name'] . "</p>";
                echo "<p><a href=\"" . $post['link'] . "\" target=\"_blank\">" . $post['link'] . "</a></p>";
                 $i++; // add 1 to the counter if our condition for $post['type'] is met
            }
 
            // check if post type is a photo
            if ($post['type'] == 'photo') {
                // echo "<span class=\"feed-time\">Photo posted on: " . date("jS M, Y", (strtotime($post['created_time']))) . "</span>";
                echo "<span class=\"feed-time\"><a href=\"" . $post['link'] . "\">" . pretty_relative_time(strtotime($post['created_time'])) . "</a></span>";
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
 
function pretty_relative_time($time) {
    if ($time !== intval($time)) { $time = strtotime($time); }
    $d = time() - $time;
    if ($time < strtotime(date('Y-m-d 00:00:00')) - 60*60*24*3) {
    $format = 'F j';
    if (date('Y') !== date('Y', $time)) {
    $format .= ", Y";
    }
    return date($format, $time);
    }
    if ($d >= 60*60*24) {
    $day = 'Yesterday';
    if (date('l', time() - 60*60*24) !== date('l', $time)) { $day = date('l', $time); }
    return $day . " at " . date('g:ia', $time);
    }
    if ($d >= 60*60*2) { return intval($d / (60*60)) . " hours ago"; }
    if ($d >= 60*60) { return "about an hour ago"; }
    if ($d >= 60*2) { return intval($d / 60) . " minutes ago"; }
    if ($d >= 60) { return "about a minute ago"; }
    if ($d >= 2) { return intval($d) . " seconds ago"; }
    return "a few seconds ago";
}

// echo "</div>";

?>