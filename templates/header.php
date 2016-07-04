<?php
header('Content-Type: text/html; charset=utf-8');
?>
<html class="<?= $permalink ? '' : 'h-feed' ?>">
<head>
  <title class="p-name">#indiewebcamp <?=$dateTitle?></title>
  <link rel="stylesheet" type="text/css" href="/assets/styles.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="pingback" href="https://webmention.io/indiewebcamp/xmlrpc">
  <link href="https://webmention.io/indiewebcamp/webmention" rel="webmention">

  <?php if(array_key_exists('timestamp', $_GET)) { 
    $currentUser = userForNick($current['nick']);
    if($currentUser && property_exists($currentUser->properties, 'photo')) {
      $image = $currentUser->properties->photo[0];
    } else {
      $image = 'https://indiewebcamp.com/wiki/skins/indieweb/indiewebcamp-logo-500px.png';
    }
  ?>
    <meta name="og:title" content="<?= $current['nick'] ?> on #indiewebcamp">
    <meta name="og:description" content="<?= htmlspecialchars(stripIRCControlChars($current['line'])) ?>">
    <meta name="og:site_name" content="IndieWebCamp IRC">
    <meta name="og:image" content="<?= $image ?>">
  <?php } ?>  
</head>
<body>
