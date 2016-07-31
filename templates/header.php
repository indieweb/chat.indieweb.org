<?php
header('Content-Type: text/html; charset=utf-8');
?>
<html class="<?= $permalink ? '' : 'h-feed' ?>">
<head>
  <meta charset="utf-8">
  <title class="p-name"><?= $channelName ?> <?= $dateTitle ?></title>

  <script src="/assets/jquery-3.1.0.min.js"></script>
  <script src="/assets/cookie.js"></script>

  <link rel="stylesheet" type="text/css" href="/materialize/css/materialize.min.css">
  <script src="/materialize/js/materialize.min.js"></script>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="/assets/styles.css">

  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="pingback" href="https://webmention.io/indiewebcamp/xmlrpc">
  <link href="https://webmention.io/indiewebcamp/webmention" rel="webmention">
  <script src="/assets/pushstream.js"></script>
  <script src="/assets/streaming.js"></script>
</head>
<body>
