<div id="chat-footer">
  <?php if((!isset($tomorrow) || !$tomorrow) && in_array($_GET['channel'], Config::supported_chat_channels())): ?>
    <?php include('templates/chat.php'); ?>
  <?php endif; ?>

  <div class="clear"></div>
</div>
