<?php
include('inc.php');

$channel = $_GET['channel'];
$destination = ($channel == 'indieweb' ? '' : $channel.'/');

list($tzname, $tz) = getViewerTimezone();

$date = new DateTime();

header('Location: ' . Config::$base . $destination . $date->format('Y-m-d') . '#bottom');
