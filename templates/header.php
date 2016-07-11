<?php
header('Content-Type: text/html; charset=utf-8');
?>
<html class="<?= $permalink ? '' : 'h-feed' ?>">
<head>
  <meta charset="utf-8">
  <title class="p-name">#indiewebcamp <?=$dateTitle?></title>
  <link rel="stylesheet" type="text/css" href="/assets/styles.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="pingback" href="https://webmention.io/indiewebcamp/xmlrpc">
  <link href="https://webmention.io/indiewebcamp/webmention" rel="webmention">
  <script src="/assets/pushstream.js"></script>
  <script src="/assets/streaming.js"></script>
</head>
<body>
