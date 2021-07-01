<?php
include('inc.php');

$input = file_get_contents('php://input');
$params = json_decode($input, true);

unset($params['author']['pronouns']);
unset($params['match']);
unset($params['response_url']);

$date = DateTime::createFromFormat('U.u', $params['timestamp']);

/*
// Temporary spam check
// Check here to see if this is spam
$ch = curl_init('http://api.loqi.me/is_spam.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$check = json_decode(curl_exec($ch), true);
if($check['spam'] == true) {
  header('HTTP/1.1 403 Spam');
  die();
}
*/


if($params['channel']['name'] !== '#indieweb-chat') {
  $db = new Quartz\DB('data/'.$params['server'].'/'.$params['channel']['name'], 'w');
  $db->add($date, $params);
  $db->close();
}

redis()->incr('indieweb-chat-total-messages');

$local_channel = Config::irc_channel_to_local_channel($params['channel']['name']);

loadUsers($local_channel);

$tz = new DateTimeZone('UTC');
$html = format_line($local_channel, $date, $tz, json_decode($input))['html'];

if($params['channel']['name'] !== '#indieweb-chat') {

  // Publish to the realtime logs
  $ch = curl_init(Config::$base.'__/pub?id=chat');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  $data = [];
  $data['html'] = $html;
  $data['channel'] = $params['channel']['name'];
  $data['type'] = $params['type'];
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_exec($ch);

}


if($params['channel']['name'] != '#indieweb-chat') {
  // Notify the WebSub hub that there is new content
  $url = Config::base_url_for_channel($params['channel']['name']);
  if(!mc()->get('websub-throttle:'.$url)) {

    $debug = date('c')."\nattempting to deliver PuSH";

    $ch = curl_init(Config::$hub);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
      'hub.mode' => 'publish',
      'hub.url' => $url
    ]));
    $response = curl_exec($ch);

    $debug .= "\n\n".$response."\n";
    file_put_contents('/web/sites/chat.indieweb.org/maintenance/logs/push.txt', $debug);

    // throttle notifications to 2 minutes
    mc()->set('websub-throttle:'.$url, 1, 120);
  }
}
