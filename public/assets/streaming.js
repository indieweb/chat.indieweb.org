// Streaming logs
var pushstream = new PushStream({
  host: "chat.indieweb.org",
  port: 443,
  useSSL: true,
  modes: "eventsource",
  urlPrefixEventsource: "/__/sub",
  channelsByArgument: true,
  channelsArgument: "id"
});
pushstream.onmessage = function(data,id,channel) {
  console.log(data);
  
  // Check that this channel matches the active channel
  if(document.getElementById('active-channel') && document.getElementById('active-channel').value == data.channel) {
    var html = data.html;
    var autoScroll = (window.innerHeight + window.scrollY) >= document.body.offsetHeight;
    var line = document.createElement('div');
    line.innerHTML = html;
    document.getElementById('log-lines').appendChild(line.childNodes[0]);
    // Auto-scroll if the window is already scrolled to the bottom
    if(autoScroll) {
      window.scrollTo(0,document.body.scrollHeight);
    }
    if(window.check_alert) {
      check_alert(data);
    }
  } else {
    // Else check if the channel is in the header bar, and mark that channel as having unread messages
    var tab = document.getElementById('channel-bar').querySelector('.channel[data-channel="#pdxbots"]');
    if(tab) {
      tab.classList.add('activity');
    }
  }
}
pushstream.addChannel('chat');
pushstream.connect();
