<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Memoraudie | Your Digital Audio Diary</title>
  <meta name="description" content="Keep your moments, voices, and memories alive. Memoraudie is a private audio memory capsule for your most meaningful events." />
  <link rel="icon" type="image/svg+xml" href="./icon.svg" />
  <link rel="stylesheet" href="./assets/css/style.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <!-- Paper texture overlay -->
  <div class="noise-overlay" aria-hidden="true"></div>

  <!-- Floating star decorations -->
  <div class="deco-star deco-1" aria-hidden="true">✦</div>
  <div class="deco-star deco-2" aria-hidden="true">✧</div>
  <div class="deco-star deco-3" aria-hidden="true">✦</div>
  <div class="deco-star deco-4" aria-hidden="true">✧</div>
  <div class="deco-star deco-5" aria-hidden="true">✦</div>

  <!-- ========== NAVBAR ========== -->
  <header class="navbar" role="banner">
    <div class="navbar-inner">
      <a href="./index.php" class="logo" aria-label="Memoraudie home">
        <span class="logo-icon" aria-hidden="true">◈</span>
        <span>Memoraudie</span>
      </a>
      <a href="./create.php" class="btn-primary btn-sm" id="btn-nav-create">
        ✦ Create Moment
      </a>
    </div>
  </header>

  <!-- ========== MAIN ========== -->
  <main role="main">
    <div class="page-wrapper">

      <!-- ── HERO ── -->
      <section class="hero" aria-label="Hero">

        <div class="hero-content">
          <div class="hero-badge" aria-hidden="true">✦ · Your Audio Diary · ✦</div>

          <h1 class="hero-title">
            Keep your<br>
            <em>moments</em> alive.
          </h1>

          <p class="hero-subtitle">
            Record voice memories. Capture your face in time. Build a private audio capsule of everything that mattered and relive it forever.
          </p>

          <div class="hero-actions" style="display:flex; gap:1rem; flex-wrap:wrap;">
            <a href="./create.php" class="btn-primary btn-lg" id="btn-hero-create">
              ✦ Create a Moment
            </a>
          </div>
        </div>

        <!-- Hero floating preview card -->
        <div class="hero-preview-card" aria-hidden="true">
          <div class="preview-header">
            <div class="preview-dots">
              <span></span><span></span><span></span>
            </div>
            <div class="preview-title-bar">memoraudie.app</div>
          </div>
          <div class="preview-thumb">
            <div class="preview-thumb-bg"></div>
            <div class="preview-thumb-play"><i class="fa-solid fa-microphone-lines"></i></div>
            <div class="preview-thumb-label">Private Capsule</div>
          </div>
          <div class="preview-waveform" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
          </div>
        </div>


      </section>

      <!-- ── HOW IT WORKS ── -->
      <section class="features-section" aria-label="How it works">
        <span class="eyebrow">How it works</span>
        <h2 class="section-title">Four steps to forever</h2>

        <div class="features-grid" role="list">

          <article class="feature-card" role="listitem">
            <h1 class="feature-step">01</h1>
            <h3>Create a Moment</h3>
            <p>Name your event capsule and set the time window. Only you hold the link.</p>
          </article>

          <article class="feature-card" role="listitem">
            <h1 class="feature-step">02</h1>
            <h3>Share the Link</h3>
            <p>Send the private URL to your guests. No accounts, no friction. Just open and record.</p>
          </article>

          <article class="feature-card" role="listitem">
            <h1 class="feature-step">03</h1>
            <h3>Record &amp; Capture</h3>
            <p>Guests leave a voice message and snap a cover photo with fun stickers. Timestamped forever.</p>
          </article>

          <article class="feature-card" role="listitem">
            <h1 class="feature-step">04</h1>
            <h3>Relive Memories</h3>
            <p>Replay your private audio guestbook anytime. Every voice, preserved.</p>
          </article>

        </div>
      </section>

      <!-- ── EXAMPLE CAPSULE ── -->
      <section class="example-section" id="example-capsule" aria-label="Example Capsule" style="padding: var(--sp-6) 0;" hidden>
        <div style="max-width:800px; margin:0 auto; text-align:center;">
          <span class="eyebrow">See it in action</span>
          <h2 class="section-title">A living memory capsule.</h2>
          <p style="color:var(--text-dim); margin-bottom:var(--sp-4);">This is what a private memory looks like. Every entry is a voice note and a face—preserved perfectly.</p>
          
          <div class="example-visual" style="display:flex; gap:var(--sp-4); justify-content:center; flex-wrap:wrap;">
            <!-- Example Card 1 -->
            <div class="memory-card" style="width:200px; text-align:left; background:var(--white); border-radius:var(--r-md); padding:var(--sp-2); box-shadow:var(--shadow-sm);">
              <div class="memory-card-thumb" style="background-color: var(--blue-200); height:200px; border-radius:var(--r-sm); position:relative;">
                 <div class="memory-duration" style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,0.6); color:#fff; font-size:12px; padding:2px 6px; border-radius:4px;">1:45</div>
              </div>
              <div class="memory-card-info" style="padding-top:var(--sp-2);">
                <div class="memory-guest" style="font-weight:600; color:var(--charcoal);">Aisyah</div>
              </div>
            </div>
            
            <!-- Example Card 2 -->
            <div class="memory-card" style="width:200px; text-align:left; background:var(--white); border-radius:var(--r-md); padding:var(--sp-2); box-shadow:var(--shadow-sm);">
              <div class="memory-card-thumb" style="background-color: var(--blue-300); height:200px; border-radius:var(--r-sm); position:relative;">
                 <div class="memory-duration" style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,0.6); color:#fff; font-size:12px; padding:2px 6px; border-radius:4px;">0:32</div>
              </div>
              <div class="memory-card-info" style="padding-top:var(--sp-2);">
                <div class="memory-guest" style="font-weight:600; color:var(--charcoal);">Budi & Rina</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── FINAL CTA ── -->
      <section class="final-cta-section" aria-label="Get Started" style="padding: var(--sp-12) 0; text-align:center;">
        <div class="final-cta-box" style="background:var(--blue-100); padding:var(--sp-5); border-radius:var(--r-lg); max-width:800px; margin:0 auto;">
          <h2 class="section-title">Start your audio diary today!</h2>
          <p style="color:var(--text-dim); margin-bottom:var(--sp-4);">No downloads, no accounts for your guests. Just pure, preserved memories.</p>
          <a href="./create.php" class="btn-primary btn-lg">✦ Create Your First Moment</a>
        </div>
      </section>

    </div><!-- /page-wrapper -->
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

</body>
</html>
