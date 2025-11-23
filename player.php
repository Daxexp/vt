<?php
// player.php
// This file contains no direct mpd links or keys. The client-side script reads data.js to find them.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Player</title>
  <style>
    html,body{height:100%;margin:0;background:#000;color:#fff}
    .playerWrap{width:100%;height:100vh;display:flex;flex-direction:column}
    #video{flex:1;background:#000;display:block;width:100%;height:100%}
    .topbar{position:absolute;left:10px;top:10px;z-index:999}
    .back{background:rgba(0,0,0,0.6);color:#fff;padding:8px 10px;border-radius:6px;text-decoration:none}
    .info{position:absolute;left:10px;bottom:12px;z-index:999;color:#ddd;padding:6px 8px;background:rgba(0,0,0,0.35);border-radius:6px}
  </style>
</head>
<body>
  <div class="playerWrap">
    <div class="topbar"><a href="index.php" class="back">◀ Back</a></div>
    <video id="video" controls autoplay playsinline></video>
    <div id="chanInfo" class="info" style="display:none"></div>
  </div>

  <!-- data.js contains all channel definitions & the single ClearKey -->
  <script src="data.js"></script>

  <!-- dash.js to play MPD (DASH) streams -->
  <script src="https://cdn.dashjs.org/latest/dash.all.min.js"></script>

  <script>
    // Helper: convert hex string to base64
    function hexToBase64(hex) {
      if (!hex) return '';
      // pad if needed
      if (hex.length % 2 !== 0) hex = '0' + hex;
      const bytes = hex.match(/.{1,2}/g).map(b => parseInt(b, 16));
      let binary = '';
      for (let i = 0; i < bytes.length; i++) {
        binary += String.fromCharCode(bytes[i]);
      }
      return btoa(binary);
    }

    // Read channel id from URL
    const params = new URLSearchParams(window.location.search);
    const channelId = params.get('channel');

    function showError(msg) {
      const info = document.getElementById('chanInfo');
      info.style.display = 'block';
      info.textContent = msg;
    }

    if (!window.TV_DATA || !TV_DATA.channels) {
      showError('Channel data not found (data.js missing or malformed).');
      throw new Error('TV_DATA not found');
    }

    const channel = TV_DATA.channels.find(c => c.id === channelId);
    if (!channel) {
      showError('Channel not found. Choose a channel from the home page.');
      throw new Error('Channel not found');
    }

    // Update small info area
    const info = document.getElementById('chanInfo');
    info.style.display = 'block';
    info.innerHTML = '<strong>' + channel.name + '</strong>';

    // dash.js player initialization
    const url = channel.mpd; // NOTE: this is loaded from data.js on client side
    const video = document.getElementById('video');

    const player = dashjs.MediaPlayer().create();
    player.updateSettings({
      streaming: {
        // adjust buffer/low-latency if needed
      }
    });

    // Configure ClearKey if provided (TV_DATA.clearKey expected as "kid_hex:key_hex")
    if (TV_DATA.clearKey) {
      try {
        const parts = TV_DATA.clearKey.trim().split(':');
        if (parts.length === 2) {
          const kidHex = parts[0].replace(/^0x/, '');
          const keyHex = parts[1].replace(/^0x/, '');
          const kidB64 = hexToBase64(kidHex);
          const keyB64 = hexToBase64(keyHex);

          // dash.js protection configuration for ClearKey
          const protectionData = {
            'org.w3.clearkey': {
              clearkeys: {}
            }
          };
          protectionData['org.w3.clearkey'].clearkeys[kidB64] = keyB64;

          // set protection data BEFORE initialize
          if (typeof player.setProtectionData === 'function') {
            player.setProtectionData(protectionData);
          } else if (player.getProtectionController && player.getProtectionController()) {
            // some dash.js versions expose a protection controller
            try {
              player.getProtectionController().setProtectionData(protectionData);
            } catch (e) {
              console.warn('Could not set protection data via protection controller', e);
            }
          } else {
            console.warn('dash.js does not expose setProtectionData in this build/version.');
          }
        } else {
          console.warn('clearKey value has unexpected format. Expected kid:key (hex).');
        }
      } catch (err) {
        console.warn('Error configuring ClearKey:', err);
      }
    }

    // Initialize player with MPD
    player.initialize(video, url, true);

    // Accessibility: set alt/title if channel has logo/name
    if (channel.logo) {
      video.setAttribute('title', channel.name);
    }
  </script>

  <!-- optional devtools-disabler (your original included this) -->
  <script disable-devtool-auto src="https://cdn.jsdelivr.net/npm/disable-devtool@latest"></script>
</body>
</html>
