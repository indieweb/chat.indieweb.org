<?php

function format_line($channel, $date, $tz, $input, $mf=true) {
  ob_start();

  $nick = $input->author->nickname;
  $user = userForNick($input->author->nickname);
  $permalink = false;

  $localdate = clone $date;
  $localdate->setTimeZone($tz);

  $blank_avatar = '<div class="avatar" style="opacity: .20;"><img src="'.Config::$base.'/assets/user.svg" width="20" height="20"/></div>';
  $avatar = $blank_avatar;

  if($user) {
    if(property_exists($user->properties, 'photo')) {
      $avatar = '<div class="avatar"><img src="' . htmlspecialchars(ImageProxy::url($user->properties->photo[0])) . '" width="20" height="20" class="' . ($mf ? 'u-photo' : '') . '"/></div>';
    }
    $who = $avatar . '<span class="">'
      . '<a href="' . @$user->properties->url[0] . '" class="author ' . ($mf ? 'p-nickname p-name u-url' : '') . '" target="_blank">' . $nick . '</a>'
      . '</span>';
  } else {
    $who = $avatar . '<span class="">'
      . '<span class="' . ($mf ? 'p-nickname p-name' : '') . '">' . $nick . '</span>'
      . '</span>';
  }
      
  $line['content'] = stripIRCControlChars($input->content);


  $timestamp = DateTime::createFromFormat('U.u', $input->timestamp);
  $line['timestamp'] = $timestamp->format('Uu');
  $line['type'] = $input->type;
  $hidden = false;
  if (substr($line['type'], 0, 7) === 'hidden-') {
    $hidden = true;
    $line['type'] = substr($line['type'], 7);
  }
  if(preg_match('/^\[\[(?<page>.+)\]\](?: (?<type>[!NM]*|delete|restore|upload|moved))? (?<url>[^ ]+) +\* (?<user>[^\*]+) \* (?:\((?<size>[+-]\d+)\))?(?:uploaded|deleted|restored|moved)?(?<comment>.*)/', $line['content'], $match)) {
    $line = format_wiki_line($channel, $line, $match, $mf, $blank_avatar);
    if(isset($line['who']))
      $who = $line['who'];
  }

  // Old twitter citations  
  if($timestamp->format('U') < strtotime('2014-01-01') && preg_match('/^https?:\/\/twitter.com\/([^ ]+) /', $line['content'], $match)) {
    $line['type'] = 'twitter';
    $line['content'] = str_replace(array($match[0].':: ',$match[0]), '', $line['content']);
    $avatar = '<div class="avatar"><img src="' . htmlspecialchars(ImageProxy::url('https://twitter.com/' . $match[1] . '/profile_image')) . '" width="20"/></div>';
    $who = $avatar . '<a href="https://twitter.com/' . $match[1] . '" class="author ' . ($mf ? 'p-url' : '') . '" target="_blank">@<span class="p-name p-nickname">' . $match[1] . '</span></a>';
  }

  // New tweets
  if(preg_match('/\[@([^\]]+)\] (.+) \((https?:\/\/twtr\.io\/[^ ]+|https?:\/\/twitter\.com\/[^ ]+)\)/ms', $line['content'], $match)) {
    $line['type'] = 'twitter';
    $line['content'] = $match[2];
    $permalink = $match[3];
    $avatar = '<div class="avatar"><img src="' . htmlspecialchars(ImageProxy::url('https://twitter.com/' . $match[1] . '/profile_image')) . '" width="20"/></div>';
    $who = $avatar . '<a href="https://twitter.com/' . $match[1] . '" class="author" target="_blank">@<span class="p-name p-nickname">' . $match[1] . '</span></a>';
  }
  
  // Ugly hack for old Loqi ACTIONs
  if($nick == 'Loqi') {
    if(preg_match('/^ACTION (.+)/', $line['content'], $match)) {
      $line['content'] = $match[1];
    }
  }


  # localize the timestamp to the person who spoke
  // if($user && property_exists($user->properties, 'tz')) {
  //   $tz = $user->properties->tz[0];
  // } else {
  //   $tz = 'America/Los_Angeles';
  // }
  // $date = new DateTime();
  // $date->setTimestamp(round($line['timestamp']/1000));
  // try {
  //   $date->setTimezone(new DateTimeZone($tz));
  // } catch(Exception $e) {
  //   $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
  // }


  $url = Config::base_url_for_channel($channel) . $date->format('Y-m-d') . '/' . $date->format('Uu');
  $urlInContext = Config::base_url_for_channel($channel) . $date->format('Y-m-d') . '#t' . $date->format('Uu');

  // Different css for retweets
  $classes = array();
  if($line['type'] == 'twitter' && preg_match('/^RT /', $line['content']))
    $classes[] = 'retweet';

  if(isMeMessage($line['content']))
    $classes[] = 'emote';

  $mf = $mf && !in_array($line['type'], ['join','leave']);


  echo '<div id="t' . $line['timestamp'] . '" class="' . ($mf ? 'h-entry' : '') . ' line msg-' . $line['type'] . ' ' . implode(' ', $classes) . '"' . ($hidden ? ' hidden' : '') . '>';

    echo '<div class="in">';
      echo '<a href="' . $urlInContext . '" class="hash">#</a> ';
    
      echo '<time class="' . ($mf ? 'dt-published' : '') . '" datetime="' . $localdate->format('c') . '">';
        echo '<a href="' . $url . '" class="' . ($mf ? 'u-url' : '') . ' time" title="' . $localdate->format('c') . '">' 
          . $localdate->format('H:i') 
        . '</a>';
      echo '</time> ';

      echo '<span class="text">';
        if(!in_array($line['type'], ['join','leave']))
          echo '<span class="nick' . ($mf ? ' p-author h-card' : '') . '">' . $who . '</span> ';

        echo '<span class="' . ($mf ? 'e-content p-name' : '') . '">';
          if(!in_array($line['type'], ['join','leave'])) {
            echo filterText($line['content'], $channel);
          } else {
            echo $nick . ' ' . ($line['type'] == 'join' ? 'joined' : 'left') . ' the channel';
          }
        echo '</span>';
      echo '</span>';
      
      if($line['type'] == 'twitter' && $permalink) {
        echo ' (<a href="' . $permalink . '" class="u-url" target="_blank">' . preg_replace('/https?:\/\//', '', $permalink) . '</a>)';
      } elseif($line['type'] == 'wiki' && $line['diff']) {
        echo ' (<a href="' . $line['diff'] . '" class="u-url" target="_blank">view diff</a>)';
      }
    echo '</div>';
    
  echo "</div>\n";

  return ob_get_clean();
}

function format_wiki_line($channel, $line, $match, $mf, $blank_avatar) {
  if(!Config::wiki_base($channel))
    return $line;
  
  // Wiki edits
  $line['type'] = 'wiki';
  $user = userForHost($match['user']);

  if($user) {
    if(property_exists($user->properties, 'photo')) {
      $avatar = '<div class="avatar"><img src="' . htmlspecialchars($user->properties->photo[0]) . '" width="20" height="20" class="' . ($mf ? 'u-photo' : '') . '"/></div>';
    } else {
      $avatar = $blank_avatar;
    }
    $who = $avatar . '<span class="">'
      . '<a class="author ' . ($mf ? 'p-nickname p-name u-url' : '') . '" href="http://' . strtolower($match['user']) . '" target="_blank">' . strtolower($match['user']) . '</a>'
      . '</span>';
  } else {
    $who = $blank_avatar . '<span class="">'
      . '<a class="author ' . ($mf ? 'p-nickname p-name u-url' : '') . '" href="http://' . strtolower($match['user']) . '" target="_blank">' . strtolower($match['user']) . '</a>'
      . '</span>';
  }

  $line['who'] = $who;

  if(trim($match['url']) == 'delete')
    $action = 'deleted';
  elseif(trim($match['url']) == 'restore')
    $action = 'restored';
  elseif(trim($match['url']) == 'upload')
    $action = 'uploaded';
  elseif(trim($match['url']) == 'move')
    $action = 'moved';
  elseif(strpos($match['type'], 'N') !== false)
    $action = 'created';
  else
    $action = 'edited';

  if(in_array($action, array('deleted','restored','uploaded','moved'))) {
    $line['diff'] = false;
    if(preg_match('/"\[\[(.+)\]\]": (.+)/', $match['comment'], $dmatch)) {
      $match['page'] = str_replace(' ','_',$dmatch[1]);
      $match['comment'] = $dmatch[2];
    }
    if(preg_match('/moved \[\[([^\]]+)\]\] to \[\[([^\]]+)\]\](?:: (.*))?/', $match['comment'], $dmatch)) {
      $match['page'] = str_replace(' ','_',$dmatch[2]);
      $match['oldpage'] = str_replace(' ','_',$dmatch[1]);
      $match['comment'] = array_key_exists(3, $dmatch) ? $dmatch[3] : '';
    }
  } else {
    $line['diff'] = $match['url'];
  }
  
  $match['page'] = str_replace(' ', '_', $match['page']);

  $line['content'] = $action 
    . ($action == 'moved' ? ' /' . $match['oldpage'] . ' to' : '') . ' /' . $match['page']
    . (in_array($action, array('deleted','restored','uploaded','moved')) ? '' : ' (' . ($match['size']) . ')') 
    . (trim($match['comment']) ? ' "' . trim($match['comment']) . '"' : '');

  return $line;
}
