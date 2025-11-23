<?php
// index.php
// Simple index page — does not contain any links/keys itself. All channel data comes from data.js (client-side).
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>TV Channels — Home</title>
  <style>
    :root{--bg:#000;--card:#111;--text:#fff;--muted:#aaa}
    html,body{height:100%;margin:0;background:var(--bg);color:var(--text);font-family:Arial,Helvetica,sans-serif}
    .wrap{max-width:1100px;margin:18px auto;padding:10px}
    h1{margin:8px 0 18px;font-size:20px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
    .card{background:var(--card);border-radius:8px;padding:10px;display:flex;flex-direction:column;align-items:center;text-align:center;cursor:pointer;transition:transform .14s}
    .card:hover{transform:translateY(-6px)}
    .logo{width:140px;height:90px;object-fit:contain;margin-bottom:8px;background:#000}
    .name{font-size:14px;color:var(--text);margin-bottom:6px}
    .play-btn{background:#0b84ff;color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:13px}
    .footer{margin-top:14px;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>TV Channels</h1>

    <!-- data.js is the only file that holds links / clear keys / channel info -->
    <script src="data.js"></script>

    <div id="channels" class="grid" aria-live="polite"></div>

    <div class="footer">
      Note: all channel URLs and keys are loaded from data.js. index.php and player.php contain no secret links.
    </div>
  </div>

  <script>
    // Expecting data.js to define window.TV_DATA with shape:
    // { clearKey: "kid_hex:key_hex", channels: [{ id, name, logo, mpd }, ...] }

    function createCard(channel) {
      const a = document.createElement('a');
      a.href = 'player.php?channel=' + encodeURIComponent(channel.id);
      a.className = 'card';
      a.setAttribute('aria-label', 'Play ' + channel.name);

      const img = document.createElement('img');
      img.className = 'logo';
      img.src = channel.logo || '';
      img.alt = channel.name + ' logo';
      a.appendChild(img);

      const name = document.createElement('div');
      name.className = 'name';
      name.textContent = channel.name;
      a.appendChild(name);

      const btn = document.createElement('div');
      btn.className = 'play-btn';
      btn.textContent = 'Play';
      a.appendChild(btn);

      return a;
    }

    function render() {
      const container = document.getElementById('channels');
      container.innerHTML = '';

      if (!window.TV_DATA || !TV_DATA.channels || !TV_DATA.channels.length) {
        const p = document.createElement('div');
        p.style.color = '#f66';
        p.textContent = 'No channels available. Add channels to data.js';
        container.appendChild(p);
        return;
      }

      TV_DATA.channels.forEach(ch => {
        container.appendChild(createCard(ch));
      });
    }

    // Render once data.js is loaded (it is loaded before this script)
    render();
  </script>
</body>
</html>
