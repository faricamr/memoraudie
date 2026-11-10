document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const activeState = document.getElementById('player-active-state');
  const emptyState = document.getElementById('player-empty-state');
  
  
  
  const coverImg = document.getElementById('playback-cover');
  const guestName = document.getElementById('playback-guest-name');
  
  const btnPlay = document.getElementById('playback-play-btn');
  const timeCurr = document.getElementById('playback-time-curr');
  const timeTot = document.getElementById('playback-time-tot');
  const trackFill = document.getElementById('playback-fill');
  const track = document.getElementById('playback-track');

  const btnPlayAll = document.getElementById('btn-play-all');
  if (btnPlayAll) {
    btnPlayAll.addEventListener('click', playEntireMoment);
  }

  // Audio Object
  let audio = new Audio();
  let currentMemoryIndex = -1;
  let isContinuousPlay = false;

  window.playMemory = (index) => {
    isContinuousPlay = false;
    loadMemoryInternal(index, true);
  };

  window.loadMemory = (index) => {
    isContinuousPlay = false;
    loadMemoryInternal(index, false);
  };

  function playEntireMoment() {
    if (!window.memoriesData || window.memoriesData.length === 0) return;
    isContinuousPlay = true;
    loadMemoryInternal(0, true);
  }

  function loadMemoryInternal(index, autoPlay) {
    const memories = window.memoriesData || [];
    if (index < 0 || index >= memories.length) {
      closeModal();
      return;
    }
    
    currentMemoryIndex = index;
    const mem = memories[index];
    
    // UI Updates
    guestName.textContent = mem.guest_name || 'Anonymous';
    coverImg.src = mem.cover_url || '';

    // Audio Setup
    audio.src = mem.audio_url;
    
    if (autoPlay) {
      audio.play().catch(e => console.error("Playback failed. Interaction may be required.", e));
      btnPlay.innerHTML = '<i class="fa-solid fa-pause"></i>';
    } else {
      btnPlay.innerHTML = '<i class="fa-solid fa-play"></i>';
      
      // Attempt to load duration immediately so the timer updates before play
      audio.addEventListener('loadedmetadata', function onLoaded() {
          const dur = audio.duration || 0;
          if (!isNaN(dur)) timeTot.textContent = formatTime(dur);
          audio.removeEventListener('loadedmetadata', onLoaded);
      });
    }
    
    // Show player
    if(activeState) activeState.style.display = 'flex';
    if(emptyState) emptyState.style.display = 'none';

    // Highlight active card in playlist
    document.querySelectorAll('.memory-card').forEach((card, i) => {
      card.classList.toggle('playing', i === index);
    });
  }

  // Audio Events
  audio.addEventListener('timeupdate', () => {
    const curr = audio.currentTime || 0;
    const dur = audio.duration || 0;
    
    timeCurr.textContent = formatTime(curr);
    if (!isNaN(dur)) {
      timeTot.textContent = formatTime(dur);
      trackFill.style.width = `${(curr / dur) * 100}%`;
    }
  });

  audio.addEventListener('ended', () => {
    btnPlay.innerHTML = '<i class="fa-solid fa-play"></i>';
    if (isContinuousPlay && window.memoriesData) {
      if (currentMemoryIndex + 1 < window.memoriesData.length) {
        setTimeout(() => {
          loadMemoryInternal(currentMemoryIndex + 1, true);
        }, 1000); // 1 sec gap between memories
      } else {
        closeModal();
      }
    }
  });

  // Controls
  btnPlay.addEventListener('click', () => {
    if (audio.paused) {
      audio.play();
      btnPlay.innerHTML = '<i class="fa-solid fa-pause"></i>';
    } else {
      audio.pause();
      btnPlay.innerHTML = '<i class="fa-solid fa-play"></i>';
    }
  });

  // Seek
  track.addEventListener('click', (e) => {
    if (audio.duration) {
      const rect = track.getBoundingClientRect();
      const pct = (e.clientX - rect.left) / rect.width;
      audio.currentTime = pct * audio.duration;
    }
  });

  // Close

  function closeModal() {
    audio.pause();
    if(activeState) activeState.style.display = 'none';
    if(emptyState) emptyState.style.display = 'flex';
    isContinuousPlay = false;
  }


  
  

  // Helper
  function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m}:${s.toString().padStart(2, '0')}`;
  }
});
