// Streaming logs
var pushstream = new PushStream({
  host: window.location.hostname,
  port: 443,
  useSSL: true,
  modes: "eventsource",
  urlPrefixEventsource: "/__/sub",
  channelsByArgument: true,
  channelsArgument: "id"
});
pushstream.onmessage = function(data,id,channel) {
  // console.log(data);
  
  // Check that this channel matches the active channel
  if(document.getElementById('active-channel') && document.getElementById('active-channel').value == data.channel) {
    var html = data.html;
    var autoScroll = (window.innerHeight + window.scrollY) >= document.body.offsetHeight;
    var line = document.createElement('div');
    line.innerHTML = html;

    // Format the timestamp in the display timezone
    var timestamp = line.querySelector("time").attributes['datetime'].value;
    var displayTime = moment(timestamp).utcOffset(document.getElementById('tz-offset').value).format("hh:mm");
    line.querySelector(".dt-published a").innerText = displayTime;

    document.getElementById('log-lines').appendChild(line.childNodes[0]);
    // Auto-scroll if the window is already scrolled to the bottom
    if(autoScroll) {
      window.scrollTo(0,document.body.scrollHeight);
    }
    if(window.check_alert) {
      check_alert(data);
    }
  } else {
    channel_activity(data.channel, data.type);
  }
}
pushstream.addChannel('chat');
pushstream.connect();

// Channel unread indicators
function channel_activity(channel, type) {
  var tab = document.querySelector('li.channel[data-channel="'+channel+'"]');
  if(tab && type == 'message') {
    tab.classList.add('activity');
  }

  var channels = Cookies.getJSON('unread');
  if(channels == "undefined") {
    channels = {};
  }
  if(typeof channels[channel] == 'undefined' || channels[channel] == 'read') {
    channels[channel] = 'unread';
    Cookies.set('unread', channels);
  }
}

function channel_unread(channel) {
  var channels = Cookies.getJSON('unread');
  return channels && channels[channel] == 'unread';
}

function channel_read(channel) {
  var channels = Cookies.getJSON('unread');
  if(channels) {
    channels[channel] = 'read';
  }
  Cookies.set('unread', channels);
  var tab = document.querySelector('li.channel[data-channel="'+channel+'"]');
  if(tab) {
    tab.classList.remove('activity');
  }
}