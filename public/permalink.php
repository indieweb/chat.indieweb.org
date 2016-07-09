<?php
include('inc.php');

# Pre-load variables required for header
$permalink = true;

loadUsers();
$timezones = loadTimezones();

# Get timezone of viewer from cookie
list($tzname, $tz) = getViewerTimezone();
$utc = new DateTimeZone('UTC');


$channel = Config::irc_channel_for_slug($_GET['channel'], $_GET['timestamp']);
$channel_link = Config::base_url_for_channel('#'.$_GET['channel']);
$timestamp = $_GET['timestamp'];


$query = db()->prepare('SELECT * FROM irclog 
  WHERE channel=:channel AND timestamp = :timestamp AND hide=0');
$query->bindParam(':channel', $channel);
$query->bindValue(':timestamp', floor($timestamp/1000));
$query->execute();
$current = false;
while($q = $query->fetch(PDO::FETCH_OBJ))
  $current = $q;

if(!$current)
  die('not found');

$date = DateTime::createFromFormat('U.u', sprintf('%.03f',$current->timestamp/1000));

$dateTitle = $date->format('Y-m-d');

$channelName = $channel;
if($timestamp < 1467615600 && $channelName == '#indieweb') $channelName = '#indiewebcamp';


include('templates/header.php');
include('templates/header-bar.php');
?>
<div class="logs">
  <div id="log-lines" class="featured">
    <?= format_line($channel, $date, $tz, db_row_to_new_log($current)) ?>
  </div>
</div>
<?php

include('templates/footer.php');
