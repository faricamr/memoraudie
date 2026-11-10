<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Your Memory — Memoraudie</title>
  <meta name="description" content="Record your audio memory for this moment." />
  <link rel="stylesheet" href="./assets/css/style.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <div class="noise-overlay" aria-hidden="true"></div>
  <div class="deco-star deco-2" aria-hidden="true">✧</div>
  <div class="deco-star deco-4" aria-hidden="true">✦</div>

  <!-- ========== NAVBAR ========== -->
  <header class="navbar" role="banner">
    <div class="navbar-inner">
      <a href="./index.php" class="logo" aria-label="Memoraudie home">
        <span class="logo-icon" aria-hidden="true">◈</span>
        <span>Memoraudie</span>
      </a>
    </div>
  </header>

  <!-- ========== MAIN ========== -->
  <main role="main">

    <!-- Loading State -->
    <div id="page-loader" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 4rem 1rem; text-align:center; min-height: 60vh;">
      <h2>Loading capsule...</h2>
    </div>

    <!-- Error State -->
    <div id="error-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding: 4rem 1rem; text-align:center; min-height: 60vh;">
      <h2 id="error-title">Moment Not Found</h2>
      <p id="error-desc" style="margin-bottom: 2rem; color: var(--charcoal-light);">We couldn't find this memory capsule.</p>
      <a href="./index.php" class="btn-primary">Return to Home</a>
    </div>

    <div class="create-memory-wrapper" id="main-content" hidden style="max-width: 1200px; margin: 0 auto; padding: var(--sp-6) var(--sp-4);">
      
      <!-- Page Header -->
      <div class="create-memory-header" style="text-align: center; margin-bottom: var(--sp-10);">
        <h1 style="font-family: var(--font-display); font-size: 2.2rem; color: var(--charcoal); margin-bottom: var(--sp-2);">Add Your Memory</h1>
        <p style="color: var(--charcoal-light); font-size: 1.1rem;">
          For: <span id="moment-title-display" style="font-weight: 600; color: var(--blue-500);"></span><br>
          <span id="moment-date-display" style="font-size: 0.9rem; color: var(--taupe);"></span>
        </p>
      </div>

      <!-- Photobooth Layout -->
      <div id="recording-ui" class="photobooth-layout">
        
        <!-- Section 1 & 2: Row Top -->
        <div class="photobooth-row-top" style="display: flex; flex-direction: column; gap: var(--sp-6); margin-bottom: var(--sp-6);">
          
          <!-- Section 1: Guest Name -->
          <div class="recording-section photobooth-section-name" style="background: var(--white); padding: var(--sp-6); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); border: 1.5px solid var(--taupe-light);">
            <span class="recording-section-label" style="display: block; font-family: var(--font-mono); font-size: 0.8rem; color: var(--taupe); text-transform: uppercase; margin-bottom: var(--sp-3);">Your Name</span>
            <input
              type="text"
              id="guest-name"
              name="guest_name"
              class="form-input"
              placeholder="Whose memory is this?"
              maxlength="80"
              autocomplete="off"
              aria-label="Your name"
            />
          </div>

          <!-- Section 2: Voice Recorder -->
          <div class="recording-section photobooth-section-voice" style="background: var(--white); padding: var(--sp-6); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); border: 1.5px solid var(--taupe-light);">
            <span class="recording-section-label" style="display: block; font-family: var(--font-mono); font-size: 0.8rem; color: var(--taupe); text-transform: uppercase; margin-bottom: var(--sp-3);">Voice Memory</span>
            <div class="recorder-area" id="recorder-area" style="display: flex; align-items: center; gap: var(--sp-4);">
              <div class="recorder-vis">
                <div class="rec-ripple" id="rec-ripple" aria-hidden="true"></div>
                <button type="button" class="rec-btn" id="rec-btn" aria-label="Start recording" title="Click to record">
                  <i class="fa-solid fa-microphone"></i>
                </button>
              </div>
              <div style="flex: 1;">
                <p class="rec-status" id="rec-status" style="margin-bottom: var(--sp-2); font-size: 0.9rem; color: var(--charcoal);">Tap to start recording</p>
                <div class="rec-timer-wrap">
                  <span class="rec-timer" id="rec-timer" aria-live="polite" aria-label="Recording time">00:00 / 03:00</span>
                  <div class="rec-progress-track" aria-hidden="true">
                    <div class="rec-progress-fill" id="rec-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /photobooth-row-top -->

        <!-- Section 3 & 4: Cover + Stickers -->
        <div class="photobooth-row-main" style="display: flex; gap: var(--sp-6);">

          <!-- Section 3: Cover Capture -->
          <div class="recording-section photobooth-section-cover" style="flex: 1; background: var(--white); padding: var(--sp-6); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); border: 1.5px solid var(--taupe-light); display: flex; flex-direction: column;">
            
            <span class="recording-section-label" style="display: block; font-family: var(--font-mono); font-size: 0.8rem; color: var(--taupe); text-transform: uppercase; margin-bottom: var(--sp-4);">Cover Capture</span>
            
            <div class="polaroid-frame" style="max-width: 360px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; background: var(--offwhite); padding: var(--sp-4) var(--sp-4) var(--sp-6); border-radius: var(--r-md); box-shadow: var(--shadow-md); position: relative; margin-bottom: var(--sp-5);">
              <div class="tape tape--tl" aria-hidden="true"></div>
              <div class="tape tape--tr" aria-hidden="true"></div>
              <div class="polaroid-inner" id="camera-area" style="aspect-ratio: 1 / 1; position: relative; border-radius: var(--r-sm); overflow: hidden; background: var(--charcoal);">
                
                <div class="camera-area" id="camera-feed" style="position: absolute; inset: 0;">
                  <div class="camera-placeholder" id="camera-placeholder" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--taupe); text-align: center;">
                    <span class="camera-placeholder-icon" aria-hidden="true" style="font-size: 3rem; margin-bottom: var(--sp-3); opacity: 0.5;"><i class="fa-solid fa-camera"></i></span>
                    <p class="camera-placeholder-text">Camera feed will appear here.<br><small>Tap the button below to activate.</small></p>
                  </div>
                  
                  <video id="camera-video" autoplay playsinline muted style="display:none; width:100%; height:100%; object-fit:cover;"></video>
                  <img id="cover-preview" style="display:none; width:100%; height:100%; object-fit:cover;" />
                  <canvas id="camera-canvas" style="display:none;"></canvas>

                  <!-- 3-2-1 Countdown overlay -->
                  <div id="countdown-overlay" style="display:none; position:absolute; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:50;">
                    <span id="countdown-number" style="font-family:var(--font-display); font-size:6rem; color:var(--white); font-weight:700; line-height:1;">3</span>
                  </div>

                  <!-- Timestamp overlay -->
                  <div id="timestamp-overlay" aria-hidden="true" style="position:absolute; bottom:12px; left:12px; font-family:'Courier New', Courier, monospace; font-weight:bold; font-size:1.1rem; color:#fff; text-shadow:1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000; pointer-events:none; display:none; line-height: 1.2;"></div>

                  <!-- Sticker overlay -->
                  <div id="sticker-overlay" aria-hidden="true" style="position:absolute; inset:0; pointer-events:none; overflow: hidden;"></div>
                </div>

              </div>
            </div>
            
            <!-- Camera controls -->
            <div class="photobooth-camera-actions" style="justify-content:center; display: flex; gap: var(--sp-2);">
              <button type="button" id="btn-start-camera" class="btn-ghost" style="justify-content:center; padding:var(--sp-3);" aria-label="Start camera">
                <i class="fa-solid fa-video"></i> Start
              </button>
              <button type="button" id="btn-switch-camera" class="btn-ghost" style="justify-content:center; padding:var(--sp-3); display:none;" aria-label="Switch camera front back">
                <i class="fa-solid fa-camera-rotate"></i> Flip
              </button>
              <button type="button" id="btn-mirror-camera" class="btn-ghost" style="justify-content:center; padding:var(--sp-3);" aria-label="Toggle mirror">
                <i class="fa-solid fa-arrows-left-right"></i> Mirror
              </button>
              <button type="button" id="btn-capture" class="btn-primary" style="justify-content:center; padding:var(--sp-3);" aria-label="Capture cover photo">
                <i class="fa-solid fa-camera"></i> Capture
              </button>
            </div>
            
          </div>

          <!-- Section 4: Sticker Tray -->
          <div class="recording-section photobooth-section-stickers" style="width: 200px; flex-shrink: 0; background: var(--white); padding: var(--sp-6); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); border: 1.5px solid var(--taupe-light); display: flex; flex-direction: column;">
            <span class="recording-section-label" style="display: block; font-family: var(--font-mono); font-size: 0.8rem; color: var(--taupe); text-transform: uppercase; margin-bottom: var(--sp-4);">Stickers</span>
            
            <div class="sticker-toolbar" id="sticker-toolbar" aria-label="Sticker selection" style="flex: 1; overflow-y: auto; display: grid; grid-template-columns: 1fr 1fr; gap: var(--sp-2); align-content: start;">
              <button type="button" class="sticker-btn" data-sticker="sticker (1).svg" aria-label="Sticker 1"><img src="./assets/stickers/sticker (1).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (2).svg" aria-label="Sticker 2"><img src="./assets/stickers/sticker (2).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (3).svg" aria-label="Sticker 3"><img src="./assets/stickers/sticker (3).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (4).svg" aria-label="Sticker 4"><img src="./assets/stickers/sticker (4).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (5).svg" aria-label="Sticker 5"><img src="./assets/stickers/sticker (5).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (6).svg" aria-label="Sticker 6"><img src="./assets/stickers/sticker (6).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (7).svg" aria-label="Sticker 7"><img src="./assets/stickers/sticker (7).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (8).svg" aria-label="Sticker 8"><img src="./assets/stickers/sticker (8).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (9).svg" aria-label="Sticker 9"><img src="./assets/stickers/sticker (9).svg" alt=""></button>
              <button type="button" class="sticker-btn" data-sticker="sticker (10).svg" aria-label="Sticker 10"><img src="./assets/stickers/sticker (10).svg" alt=""></button>
            </div>
            
            <p class="sticker-hint" style="text-align: center; font-size: 0.75rem; color: var(--taupe); margin-top: var(--sp-4);">Drag onto your photo</p>
          </div>

        </div><!-- /photobooth-row-main -->

        <!-- Section 5: Save -->
        <div class="photobooth-row-save" style="margin-top: var(--sp-6); text-align: center; padding-top: var(--sp-6); border-top: 1px solid var(--taupe-light);">
          <div style="display: flex; align-items: center; justify-content: center; gap: var(--sp-4);">
            <button type="button" id="btn-save-memory" class="btn-primary btn-lg" style="min-width: 200px; justify-content: center;" disabled aria-disabled="true" aria-label="Save memory">
              Save Memory
            </button>
          </div>
          <p class="save-helper" id="save-helper-text" aria-live="polite" style="margin-top: var(--sp-3); font-size: 0.8rem; color: var(--taupe);">Complete audio recording and cover capture first.</p>
        </div>

      </div><!-- /photobooth-layout -->

      <!-- Success State overlay (optional, or just redirect) -->
      <div id="success-overlay" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.9); z-index:9999; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
        <div style="font-size:4rem; color:var(--blue-400); margin-bottom:var(--sp-4);"><i class="fa-regular fa-circle-check"></i></div>
        <h2 style="font-family:var(--font-display); font-size:2rem; color:var(--charcoal); margin-bottom:var(--sp-2);">Memory Saved Successfully</h2>
        <p style="color:var(--charcoal-light); margin-bottom:var(--sp-6);">Your voice has been added to this memory capsule.</p>
        <button id="btn-success-return" class="btn-primary btn-lg">Return to Moment</button>
      </div>

    </div><!-- /main-content -->
  </main>

  <!-- ========== FOOTER ========== -->
  <footer class="footer" role="contentinfo">
    <div class="footer-inner">
      <span class="logo" style="font-size:1rem;">
        <span class="logo-icon" aria-hidden="true">◈</span>
        Memoraudie
      </span>
      <span class="footer-sep" aria-hidden="true">✦</span>
      <span class="footer-tagline">Digital Memory Diary</span>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const slug = urlParams.get('slug');

      if (!slug) {
        document.getElementById('page-loader').style.display = 'none';
        document.getElementById('error-title').textContent = 'Invalid Link';
        document.getElementById('error-desc').textContent = 'No memory capsule ID provided.';
        document.getElementById('error-state').style.display = 'flex';
        return;
      }


      const btnSuccessReturn = document.getElementById('btn-success-return');
      btnSuccessReturn.addEventListener('click', () => {
        window.location.href = `./moment.php?slug=${encodeURIComponent(slug)}`;
      });

      function formatDate(dateString) {
        if (!dateString) return '';
        const d = new Date(dateString.replace(' ', 'T'));
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return d.toLocaleDateString('en-US', options);
      }

      async function fetchMoment() {
        try {
          const res = await fetch(`./api/list_memories.php?slug=${encodeURIComponent(slug)}`);
          const result = await res.json();

          if (!result.success || result.data.state !== 'active') {
             document.getElementById('page-loader').style.display = 'none';
             document.getElementById('error-title').textContent = 'Cannot Add Memory';
             document.getElementById('error-desc').textContent = 'This moment is either not found, upcoming, or closed.';
             document.getElementById('error-state').style.display = 'flex';
             return;
          }

          const { moment } = result.data;
          
          document.title = `Add Memory - ${moment.title} — Memoraudie`;
          document.getElementById('moment-title-display').textContent = moment.title;
          document.getElementById('moment-date-display').textContent = formatDate(moment.start_time);

          document.getElementById('page-loader').style.display = 'none';
          document.getElementById('main-content').hidden = false;

        } catch (err) {
           document.getElementById('page-loader').style.display = 'none';
           document.getElementById('error-title').textContent = 'Network Error';
           document.getElementById('error-desc').textContent = 'Failed to load moment data.';
           document.getElementById('error-state').style.display = 'flex';
        }
      }

      fetchMoment();
    });
  </script>

  <script src="assets/js/interaction.js?v=<?= time() ?>"></script>

</body>
</html>
