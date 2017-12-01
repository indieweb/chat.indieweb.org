<?php
include('inc.php');

// Relay from the web chat interface to the Slack-IRC-Gateway system

$ch = curl_init(Config::$web_gateway_url.'web/'.$_GET['action']);
curl_setopt($ch, CURLOPT_POST, true);
$_POST['token'] = Config::$web_gateway_token;
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
curl_exec($ch);
