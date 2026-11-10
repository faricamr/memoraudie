<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create a Moment | Memoraudie</title>
  <meta name="description" content="Create a private audio memory capsule for your event. Set the title, category, and time window." />
  <link rel="icon" type="image/svg+xml" href="./icon.svg" />
  <link rel="stylesheet" href="./assets/css/style.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <div class="noise-overlay" aria-hidden="true"></div>
  <div class="deco-star deco-1" aria-hidden="true">✦</div>
  <div class="deco-star deco-3" aria-hidden="true">✦</div>
  <div class="deco-star deco-5" aria-hidden="true">✧</div>

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

      <a href="./index.php" class="btn-back" id="btn-back-home">
        ← Back to Home
      </a>

      <!-- Create card -->
      <div class="create-card">
        <div class="tape tape--tl" aria-hidden="true"></div>
        <div class="tape tape--tr" aria-hidden="true"></div>

        <div class="create-card-header">
          <span class="eyebrow">Start your capsule</span>
          <h1 class="create-card-title">Create a Moment</h1>
          <p class="create-card-sub">Set up your private audio guestbook. Share the link with your guests after.</p>
        </div>

        <form
          id="create-moment-form"
          action="./api/create_moment.php"
          method="POST"
          autocomplete="off"
          novalidate
        >

          <!-- Title -->
          <div class="form-group">
            <label class="form-label" for="moment-title">
              Event Title <span class="required">*</span>
            </label>
            <input
              type="text"
              id="moment-title"
              name="title"
              class="form-input"
              placeholder="e.g. Wisuda 2026, Pernikahan Rina & Budi…"
              maxlength="120"
              required
              autocomplete="off"
            />
          </div>

          <!-- Category -->
          <div class="form-group">
            <label class="form-label" for="moment-category">
              Category <span class="required">*</span>
            </label>
            <select id="moment-category" name="category" class="form-select" required>
              <option value="" disabled selected>Select a category…</option>
              <option value="graduation">Graduation</option>
              <option value="wedding">Wedding</option>
              <option value="birthday">Birthday</option>
              <option value="farewell">Farewell</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- Start & End time -->
          <div class="form-group">
            <label class="form-label" for="moment-start">
              Start Time <span class="required">*</span>
            </label>
            <p style="font-size: 0.82rem; color: var(--taupe); margin-top: -4px; margin-bottom: 8px;">When does the moment begin?</p>
            <input
              type="datetime-local"
              id="moment-start"
              name="start_time"
              class="form-input"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label" for="moment-end">
              End Time <span class="required">*</span>
            </label>
            <p style="font-size: 0.82rem; color: var(--taupe); margin-top: -4px; margin-bottom: 8px;">When does the link expire?</p>
            <input
              type="datetime-local"
              id="moment-end"
              name="end_time"
              class="form-input"
              required
            />
          </div>

          <!-- Form Error -->
          <div id="form-error" hidden style="background: #fdeaea; color: #d32f2f; padding: var(--sp-3); border-radius: var(--r-md); margin-bottom: var(--sp-4); font-size: 0.9rem; text-align: center;"></div>

          <!-- Submit -->
          <button
            type="submit"
            id="btn-create-submit"
            class="btn-primary btn-lg"
            style="width:100%; justify-content:center; margin-top:var(--sp-3);"
          >
            ✦ Create Moment Capsule
          </button>

        </form>
      </div><!-- /create-card -->

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

  <!-- Integration Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('create-moment-form');
      const submitBtn = document.getElementById('btn-create-submit');
      const errorDiv = document.getElementById('form-error');

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Hide previous error
        errorDiv.hidden = true;
        errorDiv.textContent = '';

        // Gather data
        const title = document.getElementById('moment-title').value.trim();
        const category = document.getElementById('moment-category').value;
        const startTime = document.getElementById('moment-start').value;
        const endTime = document.getElementById('moment-end').value;

        // Validation
        if (!title || !category || !startTime || !endTime) {
          showError('All fields are required.');
          return;
        }

        if (new Date(endTime) <= new Date(startTime)) {
          showError('End Time must be later than Start Time.');
          return;
        }

        // Format datetime from YYYY-MM-DDTHH:mm to YYYY-MM-DD HH:mm:ss
        const formatDateTime = (dt) => {
          let formatted = dt.replace('T', ' ');
          if (formatted.split(':').length === 2) {
            formatted += ':00';
          }
          return formatted;
        };

        // Loading state
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Creating...';

        try {
          const response = await fetch('./api/create_moment.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              title: title,
              category: category,
              start_time: formatDateTime(startTime),
              end_time: formatDateTime(endTime)
            })
          });

          const result = await response.json();

          if (result.success) {
            // Redirect to success page, passing the slug and host_key
            window.location.href = './success.php?slug=' + encodeURIComponent(result.data.slug) + '&host_key=' + encodeURIComponent(result.data.host_key);
          } else {
            showError(result.error || 'Failed to create moment.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        } catch (err) {
          showError('A network error occurred. Please try again.');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      });

      function showError(msg) {
        errorDiv.textContent = msg;
        errorDiv.hidden = false;
      }
    });
  </script>
</body>
</html>
