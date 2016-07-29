<?php
include('inc.php');


if(!in_array($_GET['channel'], Config::supported_channels())) {
  header('HTTP/1.1 404 Not Found');
  die('channel not found');
}

if($_GET['channel'] == 'pdxbots' && $_SERVER['REMOTE_ADDR'] != '162.213.78.244') {
  die('forbidden');
}


# Pre-load variables required for header
$permalink = false;

loadUsers();
$timezones = loadTimezones();

# Get timezone of viewer from cookie
list($tzname, $tz) = getViewerTimezone();
$utc = new DateTimeZone('UTC');



# Get the start/end times for this day
$start = new DateTime($_GET['date'].' 00:00:00', $tz);
$date = clone $start;
$end = new DateTime($_GET['date'].' 23:59:59', $tz);

$start_utc = new DateTime($_GET['date'].' 00:00:00', $tz);
$start_utc->setTimeZone($utc);
$end_utc = new DateTime($_GET['date'].' 23:59:59', $tz);
$end_utc->setTimeZone($utc);

$dateTitle = $start->format('Y-m-d');

$tmrw = new DateTime($_GET['date'].' 00:00:00', $tz);
$tmrw->add(new DateInterval('P1D'));
$tomorrow = $tmrw->format('Y-m-d');
$ystr = new DateTime($_GET['date'].' 00:00:00', $tz);
$ystr->sub(new DateInterval('P1D'));
$yesterday = $ystr->format('Y-m-d');
if($tmrw->format('U') > time()) $tomorrow = false;

$channel = '#'.$_GET['channel'];
$channel_link = Config::base_url_for_channel($channel);

// TODO: make this work for reals
if($channel == '#dev' && $start->format('U') < 1467615600) $yesterday = false;

$channelName = $channel;

// #indiewebcamp channel was renamed to #indieweb on 2016-07-04
if($start->format('U') < 1467615600 && $channelName == '#indieweb') $channelName = '#indiewebcamp';


#$db = new Quartz\DB(Config::logpath_for_channel($channel), 'r');
#$results = $db->queryRange(clone $start, clone $end);


if($channel == '#indieweb')
  $query_channels = ['#indieweb','#indiewebcamp'];
else
  $query_channels = [Config::irc_channel_for_slug($_GET['channel'])];

if($channel == '#indieweb')
  $query_channels = '"#indieweb","#indiewebcamp"';
else
  $query_channels = '"'.Config::irc_channel_for_slug($_GET['channel']).'"';

$logs = db()->prepare('SELECT * FROM irclog 
  WHERE channel IN ('.$query_channels.')
  AND timestamp >= :min AND timestamp < :max AND hide=0 
  ORDER BY timestamp');
$logs->bindValue(':min', $start_utc->format('U')*1000);
$logs->bindValue(':max', $end_utc->format('U')*1000);
$logs->execute();



include('templates/header.php');
include('templates/header-bar.php');

# Render chat logs here
?>
<h2 class="date"><?= $channelName ?> <?= $dateTitle ?></h2>

<div class="logs">
  <div id="top" class="skip"><a href="#bottom">jump to bottom</a></div>
  <div id="log-lines">
    <?php
    // foreach($results as $line) {
    //   echo format_line($channel, $line->date, $tz, $line->data);
    // }
    while($row=$logs->fetch(PDO::FETCH_OBJ)) {
      $date = DateTime::createFromFormat('U.u', sprintf('%.03f',$row->timestamp/1000));
      echo format_line($channel, $date, $tz, db_row_to_new_log($row));
    }
    ?>
  </div>
  <div id="bottom" class="skip"><a href="#top">jump to top</a></div>
</div>

<?php if(!isset($tomorrow) || !$tomorrow): /* Set the channel name to activate realtime streaming, only when viewing "today" */ ?>
  <input id="active-channel" value="<?= Config::irc_channel_for_slug($_GET['channel']) ?>" style="display:none;">
<?php endif; ?>

<?php include('templates/footer-bar.php'); ?>

<script type="text/javascript" src="/assets/pushstream.js"></script>
<script type="text/javascript">
  if(window.location.hash && window.location.hash != '#top' && window.location.hash != '#bottom') {
    var n = document.getElementById(window.location.hash.replace('#',''));
    n.classList.add('hilite');
  }
  window.addEventListener("hashchange", function(){
    var n = document.getElementsByClassName('line');
    Array.prototype.filter.call(n, function(el){ el.classList.remove('hilite') });
    var n = document.getElementById(window.location.hash.replace('#',''));
    n.classList.add('hilite');
  }, false);
</script>
<?php if(!array_key_exists('timestamp', $_GET) && isset($date) && date('Y-m-d') == $date): ?>
<script type="text/javascript" src="/assets/log-streaming.js"></script>
<?php endif; ?>

<?php

include('templates/footer.php');
