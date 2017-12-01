<?php
include('inc.php');

$channel = $_GET['channel'];
$destination = ($channel == 'indieweb' ? '' : $channel.'/');


if(array_key_exists('bookmark', $_GET)) {
	?>
	<style>body { font-family: sans-serif; }</style>
		<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
		<a style="padding: 20px 40px; display: inline-block; border: 1px #ccc solid; border-radius: 6px; background: #e9e9e9; text-decoration: none;" href="/<?= $destination ?>today">Click this, then bookmark</a>
	<?
	die();
}

if(array_key_exists('HTTP_REFERER', $_SERVER) && strpos($_SERVER['HTTP_REFERER'], '?bookmark') !== false) {
	?>
	<title>#<?= $channel ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<style>body { font-family: sans-serif; }</style>
  <link rel="apple-touch-icon-precomposed" href="/assets/logo/<?= Config::logo_for_channel($channel); ?>">
	<p>Bookmark this page or add to your home screen! When you visit it again, it will redirect you to today's logs.</p>
	<?php
	die();
}


list($tzname, $tz) = getViewerTimezone();

$date = new DateTime();

// WebSub headers
header('Link: <' . Config::$hub . '>; rel="hub"');
header('Link: <' . Config::base_url_for_channel($channel) . '>; rel="self"', false);

header('Location: ' . Config::$base . $destination . $date->format('Y-m-d') . '#bottom');
