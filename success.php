<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Moment Created — Memoraudie</title>
  <meta name="description" content="Your Memoraudie moment capsule has been created. Share the link with your guests." />
  <link rel="stylesheet" href="./assets/css/style.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <div class="noise-overlay" aria-hidden="true"></div>
  <div class="deco-star deco-1" aria-hidden="true">✦</div>
  <div class="deco-star deco-2" aria-hidden="true">✧</div>
  <div class="deco-star deco-4" aria-hidden="true">✦</div>

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
    <div class="page-wrapper page-wrapper--narrow">

      <!-- Success card -->
      <div class="success-card">
        <div class="tape tape--tl" aria-hidden="true"></div>
        <div class="tape tape--tr" aria-hidden="true"></div>

        <!-- Animated success icon -->
        <div class="success-icon" role="img" aria-label="Celebration"><i class="fa-solid fa-circle-check" style="color:var(--blue-600);"></i></div>

        <span class="eyebrow">Moment Capsule Created</span>
        <h1 class="success-title">It's ready!</h1>
        <p class="success-sub">
          Your private audio guestbook is live. Share the link below with your guests so they can record their memories.
        </p>

        <!-- Share link row -->
        <div class="share-row" role="group" aria-label="Share link">
          <input
            type="text"
            id="share-url"
            class="share-url"
            value=""
            readonly
            aria-label="Shareable moment link"
          />
          <button
            type="button"
            id="btn-copy-link"
            class="btn-primary btn-sm"
            aria-label="Copy link to clipboard"
          >
            Copy Link
          </button>
        </div>

        <!-- Host Key row -->
        <div class="share-row" role="group" aria-label="Host key" style="background: var(--blue-50); border-color: var(--blue-200);">
          <div style="flex:1; text-align: left;">
            <div style="font-family: var(--font-mono); font-size: 0.65rem; color: var(--blue-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Host Key</div>
            <input
              type="text"
              id="host-key-input"
              class="share-url"
              style="color: var(--blue-800); font-weight: bold; width: 100%;"
              value=""
              readonly
              aria-label="Host key"
            />
          </div>
          <button
            type="button"
            id="btn-copy-key"
            class="btn-primary btn-sm"
            aria-label="Copy host key to clipboard"
          >
            Copy Key
          </button>
        </div>
        
        <div class="warning-alert">
          <div class="warning-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="warning-text">
            <strong>Keep this Host Key safe.</strong><br>It grants administrative access to this Moment. Do not share it.
          </div>
        </div>

        <!-- Action buttons -->
        <div class="success-actions">
          <a
            href="#"
            class="btn-primary btn-lg"
            id="btn-go-to-moment"
          >
            Go To Moment
          </a>
          <a
            href="./create.php"
            class="btn-ghost btn-lg"
            id="btn-create-another"
          >
            + Create Another
          </a>
        </div>

      </div><!-- /success-card -->

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

  <!-- Dynamic URL Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const slug = urlParams.get('slug');
      const hostKey = urlParams.get('host_key');

      if (!slug) {
        // Fallback if no slug is provided
        window.location.href = './index.php';
        return;
      }

      // Generate share URL dynamically using standard URL API
      const shareUrl = new URL('moment.php?slug=' + encodeURIComponent(slug), window.location.href).href;
      
      const shareInput = document.getElementById('share-url');
      const hostKeyInput = document.getElementById('host-key-input');
      const btnGoToMoment = document.getElementById('btn-go-to-moment');
      const btnCopyLink = document.getElementById('btn-copy-link');
      const btnCopyKey = document.getElementById('btn-copy-key');

      shareInput.value = shareUrl;
      hostKeyInput.value = hostKey || 'NOT FOUND';
      
      // Pass host key so host can jump straight into host mode
      if (hostKey) {
        btnGoToMoment.href = shareUrl + '&host_key=' + encodeURIComponent(hostKey);
      } else {
        btnGoToMoment.href = shareUrl;
      }

      // Copy Link
      btnCopyLink.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(shareUrl);
          const originalText = btnCopyLink.innerHTML;
          btnCopyLink.innerHTML = 'Copied!';
          setTimeout(() => { btnCopyLink.innerHTML = originalText; }, 2000);
        } catch (err) {
          alert('Failed to copy. Please copy the link manually.');
        }
      });
      
      // Copy Key
      btnCopyKey.addEventListener('click', async () => {
        if (!hostKey) return;
        try {
          await navigator.clipboard.writeText(hostKey);
          const originalText = btnCopyKey.innerHTML;
          btnCopyKey.innerHTML = 'Copied!';
          setTimeout(() => { btnCopyKey.innerHTML = originalText; }, 2000);
        } catch (err) {
          alert('Failed to copy. Please copy the key manually.');
        }
      });
    });
  </script>

</body>
</html>
