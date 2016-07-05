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
</head>
<body>
