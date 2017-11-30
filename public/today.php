<?php
include('inc.php');

$channel = $_GET['channel'];
$destination = ($channel == 'indieweb' ? '' : $channel.'/');

list($tzname, $tz) = getViewerTimezone();

$date = new DateTime();

// WebSub headers
header('Link: <' . Config::$hub . '>; rel="hub"');
header('Link: <' . Config::base_url_for_channel($channel) . '>; rel="self"', false);

header('Location: ' . Config::$base . $destination . $date->format('Y-m-d') . '#bottom');
