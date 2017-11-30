<?php
include('inc.php');

$input = file_get_contents('php://input');
$params = json_decode($input, true);

unset($params['author']['pronouns']);
unset($params['match']);
unset($params['response_url']);

$date = DateTime::createFromFormat('U.u', $params['timestamp']);


if($params['channel']['name'] !== '#indieweb-chat') {
  $db = new Quartz\DB('data/'.$params['server'].'/'.$params['channel']['name'], 'w');
  $db->add($date, $params);
  $db->close();
}

loadUsers();

$tz = new DateTimeZone('UTC');
$html = format_line(Config::irc_channel_to_local_channel($params['channel']['name']), $date, $tz, json_decode($input));

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

// If a Slack channel is configured for this IRC channel, post it there too
if($params['author']['username'] != '~slackuser' && ($channel=Config::slack_channel_for_irc_channel($params['channel']['name']))) {

  $msg = Config::slack_message_replacements($params['content']);
  $user = userForNick($params['author']['nickname']);
  $icon = '';
  if($user && property_exists($user->properties, 'photo')) {
    $icon = ImageProxy::url($user->properties->photo[0]);
  }
  
  $payload = array(
    'text' => $msg,
    'username' => $params['author']['nickname'],
    'icon_url' => $icon,
    'channel' => $channel,
  );
  
  $ch = curl_init(Config::$slack_hook);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('payload'=>json_encode($payload))));
  curl_exec($ch);
  
}

if($params['channel']['name'] != '#indieweb-chat') {
  // Notify the WebSub hub that there is new content
  $url = Config::base_url_for_channel($params['channel']['name']);
  if(!mc()->get('websub-throttle:'.$url)) {

    $ch = curl_init(Config::$hub);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
      'hub.mode' => 'publish',
      'hub.url' => $url
    ]));
    curl_exec($ch);

    // throttle notifications to 2 minutes
    mc()->set('websub-throttle:'.$url, 1, 120);
  }
}
