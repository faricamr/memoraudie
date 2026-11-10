<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Wisuda 2026 — Memoraudie</title>
  <meta name="description" content="Record your audio memory for this moment." />
  <link rel="stylesheet" href="./assets/css/style.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    <div class="moment-page-wrapper" id="main-content" hidden>
      <!-- Edit Moment Modal (host only) -->
      <div id="edit-moment-modal" style="display:none; position:fixed; inset:0; background:rgba(12, 17, 26, 0.72); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:var(--sp-4);">
        <div style="background:var(--white); padding:var(--sp-8); border-radius:var(--r-xl); width:90vw; max-width:640px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">

          <h3 style="margin-bottom:var(--sp-6); font-family:var(--font-display); font-size:1.8rem; font-weight:600; color:var(--charcoal);">Edit Moment</h3>

          <div class="form-group" style="margin-bottom:var(--sp-4);">
            <label for="edit-title" class="form-label" style="font-size:0.75rem; color:var(--taupe);">Title</label>
            <input type="text" id="edit-title" class="form-input" placeholder="e.g. Farica">
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--sp-4); margin-bottom:var(--sp-8);">
            <div class="form-group" style="margin-bottom:0;">
              <label for="edit-start" class="form-label" style="font-size:0.75rem; color:var(--taupe);">Start Time</label>
              <input type="datetime-local" id="edit-start" class="form-input">
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label for="edit-end" class="form-label" style="font-size:0.75rem; color:var(--taupe);">End Time</label>
              <input type="datetime-local" id="edit-end" class="form-input">
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:var(--sp-3); border-top:1px solid var(--taupe-light); padding-top:var(--sp-4);">
            <button type="button" id="btn-cancel-edit" class="btn-ghost">Cancel</button>
            <button type="button" id="btn-save-edit" class="btn-primary">Save Changes</button>
          </div>

        </div>
      </div>

      <!-- Host Login Modal -->
      <div id="host-login-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:var(--sp-4);">
        <div style="background:var(--white); padding:var(--sp-6); border-radius:var(--r-lg); width:100%; max-width:360px; box-shadow:var(--shadow-xl); text-align:center;">
          <h3 style="margin-bottom:var(--sp-2); font-family:var(--font-display); font-size:1.5rem;">Host Login</h3>
          <p style="font-size:0.85rem; color:var(--charcoal-light); margin-bottom:var(--sp-4);">Enter your Host Key to manage this moment.</p>
          <input type="text" id="host-login-input" placeholder="e.g. ABCD-EFGH" style="width:100%; padding:10px; border:1.5px solid var(--taupe-light); border-radius:var(--r-sm); margin-bottom:var(--sp-4); text-align:center; font-family:var(--font-mono); font-size:1rem; text-transform:uppercase;">
          <div style="display:flex; gap:var(--sp-3); justify-content:center;">
            <button type="button" id="btn-cancel-login" class="btn-ghost btn-sm">Cancel</button>
            <button type="button" id="btn-submit-login" class="btn-primary btn-sm">Login</button>
          </div>
        </div>
      </div>
      
      <!-- Two-column layout -->
      <div class="moment-layout" id="moment-layout">
        <!-- ══════════════════════════════
             LEFT COLUMN — Selected Memory Player
        ══════════════════════════════ -->
        <aside class="moment-left-col" id="player-panel" aria-label="Selected Memory">
          <!-- Player Card (shown when a memory is selected or empty) -->
          <div class="moment-player-card">
            <!-- Empty state: no memories yet -->
            <div id="player-empty-state" class="player-empty-state">
              <div class="player-empty-icon">
                <i class="fa-solid fa-microphone-lines"></i>
              </div>
              <p class="player-empty-text">No memories yet.</p>
            </div>
            <!-- Active state: memory selected -->
            <div id="player-active-state" style="display:none;">
              <div class="playback-content" style="position:relative; transform:none; background:transparent; width:100%; padding:0; box-shadow:none;">
                <div class="playback-polaroid">
                  <div class="tape tape--tl" aria-hidden="true"></div>
                  <div class="tape tape--tr" aria-hidden="true"></div>
                  <div class="playback-cover-wrapper" id="playback-cover-wrapper">
                    <img id="playback-cover" src="" alt="Memory Cover" style="width:100%; height:100%; object-fit:cover; display:block;" />
                    <div id="playback-stickers" class="playback-stickers"></div>
                  </div>
                  <div class="playback-info">
                    <h3 id="playback-guest-name">Guest Name</h3>
                    <div class="playback-controls">
                      <button id="playback-play-btn" class="playback-play-btn" aria-label="Play/Pause"><i class="fa-solid fa-pause"></i></button>
                      <div class="playback-progress-wrap">
                        <div class="playback-time" id="playback-time-curr">0:00</div>
                        <div class="playback-track" id="playback-track">
                          <div class="playback-fill" id="playback-fill"></div>
                        </div>
                        <div class="playback-time" id="playback-time-tot">0:00</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /moment-player-card -->
          <!-- Add Memory CTA (active moments only) -->
          <div id="header-cta-container" style="display:none; margin-top:var(--sp-3);">
            <a href="./create-memory.php?slug=<?= htmlspecialchars($_GET['slug'] ?? '') ?>" class="btn-primary" id="btn-open-recording-header" style="width:100%; justify-content:center; text-decoration: none;">
              <i class="fa-solid fa-plus"></i> Add Your Memory
            </a>
          </div>
        </aside><!-- /left col -->

        <!-- ══════════════════════════════
             RIGHT COLUMN — Moment Info + Memory Collection
        ══════════════════════════════ -->
        <section class="moment-right-col" id="playlist-panel" aria-label="Memory collection">
          <!-- Moment Header -->
          <div class="moment-info-header">
            <div>
              <h1 id="moment-title" class="moment-header-title"></h1>
              <p id="moment-meta" class="moment-header-meta"></p>
            </div>
            <div class="moment-state-badge" id="moment-state-badge"></div>
          </div>

          <!-- Host Controls Panel (host only) -->
          <div id="host-controls" class="host-controls-bar" style="display:none;">
            <div style="display:flex; align-items:center; gap:var(--sp-2);">
              <i class="fa-solid fa-shield-halved" style="color:var(--blue-400);"></i>
              <div>
                <span style="font-size:0.9rem; font-weight:600; color:var(--charcoal);">Host Mode</span>
                <span style="font-size:0.78rem; color:var(--blue-800); margin-left:var(--sp-2);">Administrative controls unlocked.</span>
              </div>
            </div>
            <div class="host-actions" style="flex-wrap: wrap;">
              <button type="button" class="btn-ghost btn-sm" id="btn-exit-host">Exit</button>
              <button type="button" class="btn-ghost btn-sm" id="btn-edit-moment">Edit</button>
              <button type="button" class="btn-primary btn-danger btn-sm" id="btn-close-moment">Close Moment</button>
              <button type="button" class="btn-primary btn-sm" id="btn-download-all" style="background-color: var(--blue-600); border-color: var(--blue-600);"><i class="fa-solid fa-file-zipper" style="margin-right: 4px;"></i> Download All</button>
            </div>
          </div>

          <!-- Upcoming state notice (inline, compact) -->
          <div id="upcoming-hero" class="state-notice state-notice--upcoming" style="display:none;">
            <div class="state-notice-icon"><i class="fa-regular fa-clock"></i></div>
            <div>
              <p class="state-notice-title">Moment Has Not Started Yet</p>
              <p id="upcoming-date" class="state-notice-sub">Recording opens on ...</p>
              <p class="state-notice-hint">Come back when the moment begins.</p>
            </div>
          </div>

          <!-- Closed state notice (inline, compact) -->
          <div id="closed-hero" class="state-notice state-notice--closed" style="display:none;">
            <div class="state-notice-icon"><i class="fa-solid fa-box-archive"></i></div>
            <div>
              <p class="state-notice-title">Memory Capsule Closed</p>
              <p id="closed-summary" class="state-notice-sub"></p>
            </div>
          </div>

          <!-- Collection header row -->
          <div class="collection-header" id="collection-header" style="display:none;">
            <span class="collection-title">Memories</span>
            <span class="playlist-count" id="playlist-count">0</span>
          </div>
          <!-- Memory list / empty state -->
          <div id="playlist-scroll" role="list">
            <!-- Injected by JS -->
          </div>
        </section><!-- /right col -->
      </div><!-- /moment-layout -->

      <!-- Footer: Host Login (low-priority, unobtrusive) -->
      <div class="moment-footer-meta">
        <button type="button" class="host-footer-link" id="btn-unlock-host">Host Login</button>
      </div>
    </div><!-- /moment-page-wrapper -->
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
      const urlParams = new URLSearchParams(window.location.search);
      const slug = urlParams.get('slug');
      const loader = document.getElementById('page-loader');
      const errorState = document.getElementById('error-state');
      const errorTitle = document.getElementById('error-title');
      const errorDesc = document.getElementById('error-desc');
      const mainContent = document.getElementById('main-content');
      const momentTitle = document.getElementById('moment-title');
      const momentMeta = document.getElementById('moment-meta');
      const momentBadge = document.getElementById('moment-state-badge');
      const playlistScroll = document.getElementById('playlist-scroll');
      const playlistCount = document.getElementById('playlist-count');
      const collectionHeader = document.getElementById('collection-header');
      const ctaContainer = document.getElementById('header-cta-container');
      const upcomingHero = document.getElementById('upcoming-hero');
      const upcomingDate = document.getElementById('upcoming-date');
      const closedHero = document.getElementById('closed-hero');
      const closedSummary = document.getElementById('closed-summary');
      const playerEmptyEl = document.getElementById('player-empty-state');
      const playerActiveEl = document.getElementById('player-active-state');
      if (!slug) {
        showError('Invalid Link', 'No memory capsule ID provided.');
        return;
      }

      function showError(title, desc) {
        loader.style.display = 'none';
        mainContent.hidden = true;
        errorTitle.textContent = title;
        errorDesc.textContent = desc;
        errorState.style.display = 'flex';
      }

      function formatDate(dateString) {
        if (!dateString) return '';
        const d = new Date(dateString.replace(' ', 'T'));
        const options = {
          day: 'numeric',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        };
        return d.toLocaleDateString('en-US', options);
      }

      function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
      }

      function formatDuration(seconds) {
        if (!seconds) return '0:00';
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m}:${s.toString().padStart(2, '0')}`;
      }
      async function fetchMoment() {
        try {
          const urlParams = new URLSearchParams(window.location.search);
          const hostKey = urlParams.get('host_key');
          let isHost = false;
          if (hostKey) {
            const vRes = await fetch('./api/validate_host.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `slug=${encodeURIComponent(slug)}&host_key=${encodeURIComponent(hostKey)}`
            });
            const vData = await vRes.json();
            if (vData.success && vData.data && vData.data.valid) {
              isHost = true;
            }
          }
          const res = await fetch(`./api/list_memories.php?slug=${encodeURIComponent(slug)}`);
          const result = await res.json();
          if (!result.success) {
            showError('Not Found', result.error || "We couldn't find this memory capsule.");
            return;
          }
          const {
            moment,
            state,
            memories
          } = result.data;
          window.memoriesData = memories;
          document.title = `${moment.title} — Memoraudie`;
          momentTitle.textContent = moment.title;
          const startDate = formatDate(moment.start_time);
          const endDate = formatDate(moment.end_time);
          momentMeta.innerHTML = `${capitalize(moment.category)} &nbsp;&middot;&nbsp; ${startDate} – ${endDate}`;
          momentBadge.textContent = `● ${capitalize(state)}`;

          // Badge colour
          if (state === 'active') {
            momentBadge.className = 'moment-state-badge moment-state-badge--active';
          } else {
            momentBadge.className = 'moment-state-badge';
            momentBadge.style.background = 'var(--charcoal-light)';
            momentBadge.style.color = 'var(--white)';
          }

          // Host controls
          if (isHost) {
            const hostPanel = document.getElementById('host-controls');
            hostPanel.style.display = 'flex';
            const btnExitHost = document.getElementById('btn-exit-host');
            if (btnExitHost) {
              const newBtnExit = btnExitHost.cloneNode(true);
              btnExitHost.parentNode.replaceChild(newBtnExit, btnExitHost);
              newBtnExit.addEventListener('click', () => {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('host_key');
                window.location.href = currentUrl.toString();
              });
            }
            const btnClose = document.getElementById('btn-close-moment');
            if (state === 'closed') {
              btnClose.disabled = true;
              btnClose.style.opacity = '0.5';
              btnClose.style.cursor = 'not-allowed';
              btnClose.innerHTML = 'Already Closed';
            } else if (state === 'upcoming') {
              btnClose.disabled = true;
              btnClose.style.opacity = '0.5';
              btnClose.style.cursor = 'not-allowed';
              btnClose.innerHTML = 'Moment Upcoming';
            } else {
              const newBtnClose = btnClose.cloneNode(true);
              btnClose.parentNode.replaceChild(newBtnClose, btnClose);
              newBtnClose.addEventListener('click', async () => {
                if (confirm("Are you sure you want to close this moment early? No one will be able to record new memories.")) {
                  const cres = await fetch('./api/close_moment.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `slug=${encodeURIComponent(slug)}&host_key=${encodeURIComponent(hostKey)}`
                  });
                  const cdata = await cres.json();
                  if (cdata.success) {
                    window.location.reload();
                  } else {
                    alert("Failed to close: " + cdata.error);
                  }
                }
              });
            }
            const btnEdit = document.getElementById('btn-edit-moment');
            const btnDownloadAll = document.getElementById('btn-download-all');
            if (btnDownloadAll) {
              btnDownloadAll.addEventListener('click', () => {
                window.location.href = `./api/download_all.php?slug=${encodeURIComponent(slug)}&host_key=${encodeURIComponent(hostKey)}`;
              });
            }
            const editModal = document.getElementById('edit-moment-modal');
            const btnCancelEdit = document.getElementById('btn-cancel-edit');
            const btnSaveEdit = document.getElementById('btn-save-edit');
            btnEdit.addEventListener('click', () => {
              document.getElementById('edit-title').value = moment.title;
              document.getElementById('edit-start').value = moment.start_time.replace(' ', 'T').substring(0, 16);
              document.getElementById('edit-end').value = moment.end_time.replace(' ', 'T').substring(0, 16);
              editModal.style.display = 'flex';
            });
            btnCancelEdit.addEventListener('click', () => {
              editModal.style.display = 'none';
            });
            const newBtnSaveEdit = btnSaveEdit.cloneNode(true);
            btnSaveEdit.parentNode.replaceChild(newBtnSaveEdit, btnSaveEdit);
            newBtnSaveEdit.addEventListener('click', async () => {
              newBtnSaveEdit.disabled = true;
              newBtnSaveEdit.textContent = 'Saving...';
              const newTitle = document.getElementById('edit-title').value;
              const newStart = document.getElementById('edit-start').value.replace('T', ' ') + ':00';
              const newEnd = document.getElementById('edit-end').value.replace('T', ' ') + ':00';
              const eres = await fetch('./api/update_moment.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `slug=${encodeURIComponent(slug)}&host_key=${encodeURIComponent(hostKey)}&title=${encodeURIComponent(newTitle)}&start_time=${encodeURIComponent(newStart)}&end_time=${encodeURIComponent(newEnd)}`
              });
              const edata = await eres.json();
              if (edata.success) {
                window.location.reload();
              } else {
                alert("Update failed: " + edata.error);
                newBtnSaveEdit.disabled = false;
                newBtnSaveEdit.textContent = 'Save Changes';
              }
            });
          }
          // ── State layout logic ──────────────────────────────────
          if (state === 'upcoming') {

            // No CTA, show waiting room notice, left player is quiet
            ctaContainer.style.display = 'none';
            upcomingHero.style.display = 'flex';
            upcomingDate.textContent = `Recording opens on ${startDate}`;

            // Left column: show empty player (no memories to play)
            playerEmptyEl.style.display = 'flex';
            playerActiveEl.style.display = 'none';
          } else if (state === 'active') {
            ctaContainer.style.display = 'block';
            upcomingHero.style.display = 'none';
          } else if (state === 'closed') {
            ctaContainer.style.display = 'none';
            closedHero.style.display = 'flex';
            closedSummary.textContent = memories.length > 0 ?
              `${memories.length} memor${memories.length === 1 ? 'y' : 'ies'} collected. Guests can still listen to all recordings.` :
              'No memories were recorded for this moment.';
          }
          // ── Playlist ───────────────────────────────────────────
          if (memories.length === 0) {

            // Single empty state — only in the right column playlist area
            playerEmptyEl.style.display = 'flex';
            playerActiveEl.style.display = 'none';

            // Only show CTA-linked empty state copy for active moments
            if (state === 'active') {
              playlistScroll.innerHTML = `
                <div class="playlist-empty-state">
                  <p class="playlist-empty-title">Be the first voice in this memory capsule.</p>
                  <a href="./create-memory.php?slug=${encodeURIComponent(slug)}" class="btn-primary" style="text-decoration:none;">
                    <i class="fa-solid fa-plus"></i> Add Your Memory
                  </a>
                </div>
              `;
            } else {
              playlistScroll.innerHTML = `
                <div class="playlist-empty-state">
                  <p class="playlist-empty-title">No memories recorded.</p>
                </div>
              `;
            }
          } else {

            // Show collection header
            playlistCount.textContent = memories.length;
            collectionHeader.style.display = 'flex';

            // Build clean card list — no numbering, no table
            playlistScroll.innerHTML = memories.map((m, index) => {
              const hasCover = !!m.cover_url;
              const thumbStyle = hasCover ?
                `background-image:url('${m.cover_url}'); background-size:cover; background-position:center;` :
                `background:var(--taupe-light);`;
              const thumbIcon = hasCover ? '' : '<i class="fa-solid fa-microphone-lines" style="font-size:1.1rem; color:var(--taupe);"></i>';
              const downloadBtn = `
                <a href="./api/download_memory.php?slug=${encodeURIComponent(slug)}&memory_id=${m.id}" class="memory-card-action" aria-label="Download memory" onclick="event.stopPropagation();" title="Download Audio">
                  <i class="fa-solid fa-download"></i>
                </a>
              `;
              const deleteBtn = isHost ? `
                <button class="memory-card-delete" aria-label="Delete memory" onclick="event.stopPropagation(); window.deleteMemory(${m.id})">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              ` : '';
              return `
                <div class="memory-card" data-memory-id="${m.id || index}" role="button" tabindex="0"
                     onclick="window.playMemory(${index})"
                     onkeydown="if(event.key==='Enter'||event.key===' ')window.playMemory(${index})">
                  <div class="memory-card-thumb" style="${thumbStyle}">${thumbIcon}</div>
                  <div class="memory-card-body">
                    <div class="memory-card-name">${capitalize(m.guest_name || 'Anonymous')}</div>
                    <div class="memory-card-label">Voice Memory</div>
                  </div>
                  <div class="memory-card-duration">${formatDuration(m.audio_duration)}</div>
                  <div style="display:flex; gap:4px; margin-left:var(--sp-2);">
                    ${downloadBtn}
                    ${deleteBtn}
                  </div>
                </div>
              `;
            }).join('');
          }

          // Load first memory if available (without auto-playing)
          if (memories.length > 0 && state !== 'upcoming') {
            setTimeout(() => {
              window.loadMemory(0);
            }, 100);
          }

          // Host login button (footer)
          const btnUnlockHost = document.getElementById('btn-unlock-host');
          if (btnUnlockHost) {
            if (isHost) {
              btnUnlockHost.closest('.moment-footer-meta').style.display = 'none';
            } else {
              const loginModal = document.getElementById('host-login-modal');
              const btnCancelLogin = document.getElementById('btn-cancel-login');
              const btnSubmitLogin = document.getElementById('btn-submit-login');
              const loginInput = document.getElementById('host-login-input');
              const newBtnUnlock = btnUnlockHost.cloneNode(true);
              btnUnlockHost.parentNode.replaceChild(newBtnUnlock, btnUnlockHost);
              newBtnUnlock.addEventListener('click', () => {
                loginModal.style.display = 'flex';
                loginInput.focus();
              });
              btnCancelLogin.addEventListener('click', () => {
                loginModal.style.display = 'none';
                loginInput.value = '';
              });
              btnSubmitLogin.addEventListener('click', () => {
                const input = loginInput.value;
                if (input && input.trim() !== '') {
                  const currentUrl = new URL(window.location.href);
                  currentUrl.searchParams.set('host_key', input.trim());
                  window.location.href = currentUrl.toString();
                }
              });
              loginInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') btnSubmitLogin.click();
              });
            }
          }

          // Host delete
          window.deleteMemory = async (id) => {
            if (confirm("Delete this memory permanently?")) {
              const urlParams = new URLSearchParams(window.location.search);
              const hostKey = urlParams.get('host_key');
              const res = await fetch('./api/delete_memory.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `memory_id=${id}&slug=${encodeURIComponent(slug)}&host_key=${encodeURIComponent(hostKey)}`
              });
              const data = await res.json();
              if (data.success) {
                fetchMoment();
              } else {
                alert("Failed to delete memory.");
              }
            }
          };
          loader.style.display = 'none';
          mainContent.hidden = false;
        } catch (err) {
          showError('Network Error', 'Failed to connect to the server. Please try again later.');
        }
      }
      fetchMoment();
    });
  </script>
  <script src="assets/js/interaction.js?v=<?= time() ?>"></script>
  <script src="assets/js/player.js?v=<?= time() ?>"></script>
</body>

</html>
