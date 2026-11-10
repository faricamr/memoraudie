document.addEventListener('DOMContentLoaded', () => {
  // Phase 3 WebRTC & Interaction Logic

  // URLs & Data
  const urlParams = new URLSearchParams(window.location.search);
  const slug = urlParams.get('slug');
  
  // State variables
  let audioBlob = null;
  let coverBlob = null;
        document.getElementById('timestamp-overlay').style.display = 'none';
  let isRecording = false;
  let mediaRecorder = null;
  let audioChunks = [];
  let recordTimer = null;
  let recordSeconds = 0;
  const MAX_RECORD_SECONDS = 180;
  
  // Audio Elements
  const recBtn = document.getElementById('rec-btn');
  const recRipple = document.getElementById('rec-ripple');
  const recStatus = document.getElementById('rec-status');
  const recTimerEl = document.getElementById('rec-timer');
  const recProgress = document.getElementById('rec-progress');

  // Camera Elements
  const btnStartCamera = document.getElementById('btn-start-camera');
  const btnSwitchCamera = document.getElementById('btn-switch-camera');
  const btnMirrorCamera = document.getElementById('btn-mirror-camera');
  const btnCapture = document.getElementById('btn-capture');
  const cameraPlaceholder = document.getElementById('camera-placeholder');
  const cameraVideo = document.getElementById('camera-video');
  const cameraCanvas = document.getElementById('camera-canvas');
  const coverPreview = document.getElementById('cover-preview');
  let videoStream = null;
  let isMirrored = true;
  let currentFacingMode = 'user';

  // Sticker Elements
  const stickerToolbar = document.getElementById('sticker-toolbar');
  const stickerOverlay = document.getElementById('sticker-overlay');
  let stickersData = [];
  let stickerIdCounter = 0;
  
  // Save Elements
  const guestNameInput = document.getElementById('guest-name');
  const btnSaveMemory = document.getElementById('btn-save-memory');
  const saveHelperText = document.getElementById('save-helper-text');

  // Wait for elements to exist before proceeding. This logic is generic and works on create-memory.php.


  


  // Only init if we are on an active moment
  

  // ----------------------------------------------------
  // 1. Audio Recording
  // ----------------------------------------------------
  if(recBtn) {
    recBtn.addEventListener('click', async () => {
      if (!isRecording) {
        await startRecording();
      } else {
        stopRecording();
      }
    });
  }

  async function startRecording() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
      
      mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) audioChunks.push(e.data);
      };
      
      mediaRecorder.onstop = () => {
        audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
        stream.getTracks().forEach(track => track.stop());
        checkSaveReady();
      };

      audioChunks = [];
      mediaRecorder.start();
      isRecording = true;
      recordSeconds = 0;
      
      recBtn.classList.add('recording');
      recRipple.style.display = 'block';
      recStatus.textContent = 'Recording... Tap to stop';
      
      recordTimer = setInterval(updateTimer, 1000);
      updateTimer();
    } catch (err) {
      alert("Microphone access is required to record a memory.");
    }
  }

  function stopRecording() {
    if (mediaRecorder && isRecording) {
      mediaRecorder.stop();
      isRecording = false;
      clearInterval(recordTimer);
      
      recBtn.classList.remove('recording');
      recRipple.style.display = 'none';
      recStatus.textContent = 'Recording finished! Tap to re-record.';
    }
  }

  function updateTimer() {
    recordSeconds++;
    const m = Math.floor(recordSeconds / 60);
    const s = recordSeconds % 60;
    recTimerEl.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')} / 03:00`;
    
    const pct = (recordSeconds / MAX_RECORD_SECONDS) * 100;
    recProgress.style.width = `${pct}%`;
    
    if (recordSeconds >= MAX_RECORD_SECONDS) {
      stopRecording();
    }
  }

  // ----------------------------------------------------
  // 2. Camera & Cover
  // ----------------------------------------------------
  async function startCamera(facing = currentFacingMode) {
    if (videoStream) {
      videoStream.getTracks().forEach(track => track.stop());
    }
    try {
      videoStream = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: { ideal: facing }, width: { ideal: 1080 }, height: { ideal: 1080 } } 
      });
      currentFacingMode = facing;
      cameraVideo.srcObject = videoStream;
      cameraPlaceholder.style.display = 'none';
      coverPreview.style.display = 'none';
      cameraVideo.style.display = 'block';
      cameraVideo.style.transform = isMirrored ? 'scaleX(-1)' : 'none';
      
      btnStartCamera.style.display = 'none';
      if (btnSwitchCamera) btnSwitchCamera.style.display = 'flex';
      btnCapture.style.display = 'flex';
      btnCapture.innerHTML = '<i class="fa-solid fa-camera"></i> Capture';
      if (btnMirrorCamera) btnMirrorCamera.disabled = false;
    } catch (err) {
      alert("Camera access is required to take a cover photo.");
    }
  }

  if(btnStartCamera) {
    btnStartCamera.addEventListener('click', () => startCamera());
  }

  if(btnSwitchCamera) {
    btnSwitchCamera.addEventListener('click', () => {
      const nextFacing = currentFacingMode === 'user' ? 'environment' : 'user';
      isMirrored = (nextFacing === 'user');
      startCamera(nextFacing);
    });
  }

  if(btnMirrorCamera) {
    btnMirrorCamera.addEventListener('click', () => {
      isMirrored = !isMirrored;
      cameraVideo.style.transform = isMirrored ? 'scaleX(-1)' : 'none';
    });
  }

  if(btnCapture) {

    btnCapture.addEventListener('click', () => {
      if (videoStream && cameraVideo.style.display !== 'none') {
        // Countdown
        let countdown = 3;
        btnCapture.disabled = true;
        if (btnMirrorCamera) btnMirrorCamera.disabled = true;
        
        const cdOverlay = document.getElementById('countdown-overlay');
        const cdNumber = document.getElementById('countdown-number');
        
        if (cdOverlay && cdNumber) {
            cdNumber.textContent = countdown;
            cdOverlay.style.display = 'flex';
            
            const cdInterval = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                   cdNumber.textContent = countdown;
                } else {
                   clearInterval(cdInterval);
                   cdOverlay.style.display = 'none';
                   executeCapture();
                }
            }, 1000);
        } else {
            executeCapture();
        }
        
      } else {
        // Retake
        coverBlob = null;
        coverBlob = null;
        document.getElementById('timestamp-overlay').style.display = 'none';
        btnCapture.innerHTML = '<i class="fa-solid fa-camera"></i> Capture';
        if (btnMirrorCamera) btnMirrorCamera.disabled = false;
        btnStartCamera.click();
        checkSaveReady();
      }
    });

    function executeCapture() {
        const ctx = cameraCanvas.getContext('2d');
        const vw = cameraVideo.videoWidth || 640;
        const vh = cameraVideo.videoHeight || 480;
        
        const size = Math.min(vw, vh);
        const startX = (vw - size) / 2;
        const startY = (vh - size) / 2;
        
        // Kompresi resolusi standar album cover (maksimal 720px)
        const targetSize = Math.min(size, 720);
        cameraCanvas.width = targetSize;
        cameraCanvas.height = targetSize;
        
        ctx.save();
        if (isMirrored) {
            ctx.translate(targetSize, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(cameraVideo, startX, startY, size, size, 0, 0, targetSize, targetSize);
        ctx.restore();
        
        cameraCanvas.toBlob((blob) => {
          coverBlob = blob;
          coverPreview.src = URL.createObjectURL(blob);
          
          cameraVideo.style.display = 'none';
          coverPreview.style.display = 'block';
          
          videoStream.getTracks().forEach(track => track.stop());
          videoStream = null;
          
          btnCapture.disabled = false;
          btnCapture.innerHTML = '<i class="fa-solid fa-rotate-right"></i> Retake';
          checkSaveReady();
          
          // Show static timestamp overlay
          showTimestampOverlay();
        }, 'image/jpeg', 0.80);
    }
    
    function showTimestampOverlay() {
       const now = new Date();
       const months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
       const d = now.getDate().toString().padStart(2, '0');
       const m = months[now.getMonth()];
       const y = now.getFullYear();
       let h = now.getHours();
       const mins = now.getMinutes().toString().padStart(2, '0');
       const secs = now.getSeconds().toString().padStart(2, '0');
       const dateText = `${d} ${m} ${y}`;
       const timeText = `${h.toString().padStart(2,'0')}:${mins}:${secs}`;
       
       const tsOverlay = document.getElementById('timestamp-overlay');
       if (tsOverlay) {
           tsOverlay.innerHTML = `${dateText}<br>${timeText}`;
           tsOverlay.dataset.dateText = dateText;
           tsOverlay.dataset.timeText = timeText;
           tsOverlay.style.display = 'block';
       }
    }

  }

  // ----------------------------------------------------
  // 3. Stickers (Drag & Drop MVP)
  // ----------------------------------------------------
  const stickerBtns = document.querySelectorAll('.sticker-btn');
  stickerBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // Only allow adding stickers if a cover has been captured
      if (!coverBlob) {
        alert("Please capture a cover photo before adding stickers.");
        return;
      }
      const src = btn.getAttribute('data-sticker');
      const alt = btn.getAttribute('aria-label');
      addStickerToCover(src, alt);
    });
  });

  
  let activeSticker = null;

  function addStickerToCover(src, alt) {
    const wrapper = document.createElement('div');
    const id = `sticker-${stickerIdCounter++}`;
    wrapper.id = id;
    wrapper.className = 'placed-sticker';
    
    // Add image
    const img = document.createElement('img');
    img.src = `assets/stickers/${src}`;
    img.alt = alt;
    img.style.width = '100px'; // default size
    img.style.height = 'auto';
    img.style.pointerEvents = 'none'; // let wrapper handle drag
    wrapper.appendChild(img);

    // Default center
    wrapper.style.left = '50%';
    wrapper.style.top = '50%';
    wrapper.style.transform = 'translate(-50%, -50%) rotate(0deg)';
    
    // For storing
    const dataObj = { id, type: src, x: 50, y: 50, scale: 1, rotation: 0 };
    stickersData.push(dataObj);
    
    stickerOverlay.appendChild(wrapper);
    makeDraggable(wrapper, dataObj, img);
    activateSticker(wrapper, dataObj, img);
  }

  function activateSticker(wrapper, dataObj, img) {
    if (activeSticker) {
      activeSticker.wrapper.classList.remove('active');
      if (activeSticker.controls) {
        activeSticker.controls.forEach(c => c.remove());
      }
    }
    
    wrapper.classList.add('active');
    
    const btnDel = document.createElement('div');
    btnDel.className = 'sticker-control sticker-control-delete';
    btnDel.onclick = (e) => {
      e.stopPropagation();
      wrapper.remove();
      stickersData = stickersData.filter(s => s.id !== dataObj.id);
      activeSticker = null;
    };
    
    const btnResize = document.createElement('div');
    btnResize.className = 'sticker-control sticker-control-resize';
    btnResize.innerHTML = '<i class="fa-solid fa-expand" style="font-size:10px;"></i>';
    
    const btnRotate = document.createElement('div');
    btnRotate.className = 'sticker-control sticker-control-rotate';
    btnRotate.innerHTML = '<i class="fa-solid fa-sync-alt" style="font-size:10px;"></i>';

    wrapper.appendChild(btnDel);
    wrapper.appendChild(btnResize);
    wrapper.appendChild(btnRotate);
    activeSticker = { wrapper, controls: [btnDel, btnResize, btnRotate] };
  }

  // Deactivate if clicking outside
  document.addEventListener('click', (e) => {
    if (activeSticker && !activeSticker.wrapper.contains(e.target) && !e.target.closest('.sticker-btn')) {
      activeSticker.wrapper.classList.remove('active');
      if (activeSticker.controls) activeSticker.controls.forEach(c => c.remove());
      activeSticker = null;
    }
  });

  function makeDraggable(el, dataObj, img) {
    let mode = null; // 'drag', 'resize', 'rotate', 'pinch'
    let startX, startY;
    let initialX, initialY, initialScale, initialRotation;
    let initialPinchDist = 0;
    let initialPinchAngle = 0;
    
    const updateTransform = () => {
      el.style.left = `${dataObj.x}%`;
      el.style.top = `${dataObj.y}%`;
      el.style.transform = `translate(-50%, -50%) rotate(${dataObj.rotation}deg)`;
      img.style.width = `${100 * dataObj.scale}px`;
    };
    
    updateTransform();
    
    const startInteraction = (e) => {
      if (e.target.classList.contains('sticker-control-delete')) return;
      
      e.preventDefault();
      activateSticker(el, dataObj, img);
      
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      
      startX = clientX;
      startY = clientY;
      
      initialX = dataObj.x;
      initialY = dataObj.y;
      initialScale = dataObj.scale || 1;
      initialRotation = dataObj.rotation || 0;
      
      if (e.touches && e.touches.length === 2) {
        mode = 'pinch';
        initialPinchDist = Math.hypot(
          e.touches[1].clientX - e.touches[0].clientX,
          e.touches[1].clientY - e.touches[0].clientY
        );
        initialPinchAngle = Math.atan2(
          e.touches[1].clientY - e.touches[0].clientY,
          e.touches[1].clientX - e.touches[0].clientX
        );
        return;
      }
      
      if (e.target.classList.contains('sticker-control-resize')) {
        mode = 'resize';
      } else if (e.target.classList.contains('sticker-control-rotate')) {
        mode = 'rotate';
      } else {
        mode = 'drag';
      }
    };
    
    const moveInteraction = (e) => {
      if (!mode) return;
      
      if (mode === 'pinch') {
        if (e.touches && e.touches.length === 2) {
          const currentDist = Math.hypot(
            e.touches[1].clientX - e.touches[0].clientX,
            e.touches[1].clientY - e.touches[0].clientY
          );
          const currentAngle = Math.atan2(
            e.touches[1].clientY - e.touches[0].clientY,
            e.touches[1].clientX - e.touches[0].clientX
          );
          
          if (initialPinchDist > 0) {
            dataObj.scale = Math.max(0.3, Math.min(4, initialScale * (currentDist / initialPinchDist)));
          }
          
          const angleDiff = (currentAngle - initialPinchAngle) * (180 / Math.PI);
          dataObj.rotation = initialRotation + angleDiff;
          
          updateTransform();
        }
        return;
      }
      
      if (e.touches && e.touches.length !== 1) return; // Ignore multi-touch for drag/resize/rotate

      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      const dx = clientX - startX;
      const dy = clientY - startY;
      
      const rect = stickerOverlay.getBoundingClientRect();
      const centerX = rect.left + (initialX / 100) * rect.width;
      const centerY = rect.top + (initialY / 100) * rect.height;
      
      if (mode === 'drag') {
        let pctX = (dx / rect.width) * 100;
        let pctY = (dy / rect.height) * 100;
        dataObj.x = Math.max(0, Math.min(100, initialX + pctX));
        dataObj.y = Math.max(0, Math.min(100, initialY + pctY));
      } 
      else if (mode === 'resize') {
        const startDist = Math.hypot(startX - centerX, startY - centerY);
        const currentDist = Math.hypot(clientX - centerX, clientY - centerY);
        if (startDist > 0) {
          dataObj.scale = Math.max(0.3, Math.min(4, initialScale * (currentDist / startDist)));
        }
      } else if (mode === 'rotate') {
        const startAngle = Math.atan2(startY - centerY, startX - centerX);
        const currentAngle = Math.atan2(clientY - centerY, clientX - centerX);
        const angleDiff = (currentAngle - startAngle) * (180 / Math.PI);
        dataObj.rotation = initialRotation + angleDiff;
      }
      
      updateTransform();
    };
    
    const stopInteraction = () => {
      mode = null;
    };

    el.addEventListener('mousedown', startInteraction);
    document.addEventListener('mousemove', moveInteraction);
    document.addEventListener('mouseup', stopInteraction);
    
    el.addEventListener('touchstart', startInteraction, {passive: false});
    document.addEventListener('touchmove', moveInteraction, {passive: false});
    document.addEventListener('touchend', stopInteraction);
  }


  // ----------------------------------------------------
  // 4. Validation & Upload
  // ----------------------------------------------------
  if(guestNameInput) {
    guestNameInput.addEventListener('input', checkSaveReady);
  }

  function checkSaveReady() {
    if(!guestNameInput) return;
    const hasName = guestNameInput.value.trim().length > 0;
    const hasAudio = !!audioBlob;
    const hasCover = !!coverBlob;
    
    if (hasName && hasAudio && hasCover) {
      btnSaveMemory.disabled = false;
      btnSaveMemory.removeAttribute('aria-disabled');
      saveHelperText.textContent = "Ready to save!";
      saveHelperText.style.color = 'var(--blue-600)';
    } else {
      btnSaveMemory.disabled = true;
      btnSaveMemory.setAttribute('aria-disabled', 'true');
      saveHelperText.style.color = '';
      
      let msg = [];
      if (!hasName) msg.push('name');
      if (!hasAudio) msg.push('audio');
      if (!hasCover) msg.push('cover photo');
      saveHelperText.textContent = `Complete ${msg.join(', ')} first.`;
    }
  }

  if(btnSaveMemory) {
    btnSaveMemory.addEventListener('click', async () => {
      if (btnSaveMemory.disabled) return;
      
      // Disable UI during upload
      btnSaveMemory.disabled = true;
      const btnCancelMemory = document.getElementById('btn-cancel-memory');
      if (btnCancelMemory) btnCancelMemory.disabled = true;
      const originalText = btnSaveMemory.innerHTML;
      btnSaveMemory.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';
      
      
      // Bake stickers onto the image
      const finalCanvas = document.createElement('canvas');
      finalCanvas.width = cameraCanvas.width;
      finalCanvas.height = cameraCanvas.height;
      const ctx = finalCanvas.getContext('2d');
      
      // Draw raw image
      ctx.drawImage(cameraCanvas, 0, 0);
      
      
      // Draw stickers
      const overlayRect = stickerOverlay.getBoundingClientRect();
      const scaleX = finalCanvas.width / overlayRect.width;
      const scaleY = finalCanvas.height / overlayRect.height;
      
      stickersData.forEach(st => {
         const el = document.getElementById(st.id);
         if (el) {
             const cx = (st.x / 100) * finalCanvas.width;
             const cy = (st.y / 100) * finalCanvas.height;
                 
             if (st.type === 'text') {
                 const span = el.querySelector('span');
                 if (span) {
                     ctx.font = `bold ${Math.floor(finalCanvas.height * 0.05 * (st.scale || 1))}px "Courier New", Courier, monospace`;
                     ctx.fillStyle = '#fff';
                     ctx.textAlign = 'center';
                     ctx.textBaseline = 'middle';
                     
                     ctx.fillStyle = '#000';
                     const offset = 2;
                     ctx.fillText(st.text, cx+offset, cy+offset);
                     ctx.fillText(st.text, cx-offset, cy-offset);
                     ctx.fillText(st.text, cx+offset, cy-offset);
                     ctx.fillText(st.text, cx-offset, cy+offset);
                     
                     ctx.fillStyle = '#fff';
                     ctx.fillText(st.text, cx, cy);
                 }
              } else {
                  const img = el.querySelector('img');
                  if (img) {
                      const baseW = 100;
                      const baseH = img.naturalWidth ? img.naturalHeight * (baseW / img.naturalWidth) : baseW;
                      const dw = baseW * (st.scale || 1) * scaleX;
                      const dh = baseH * (st.scale || 1) * scaleY;
                      
                      ctx.save();
                      ctx.translate(cx, cy);
                      if (st.rotation) ctx.rotate(st.rotation * Math.PI / 180);
                      ctx.drawImage(img, -dw/2, -dh/2, dw, dh);
                      ctx.restore();
                  }
              }
         }
      });
      // Draw timestamp
      const tsOverlay = document.getElementById('timestamp-overlay');
      if (tsOverlay && tsOverlay.style.display !== 'none' && tsOverlay.dataset.dateText) {
          const fontSize = Math.floor(finalCanvas.height * 0.05);
          ctx.font = `bold ${fontSize}px "Courier New", Courier, monospace`;
          ctx.textAlign = 'left';
          ctx.textBaseline = 'bottom';
          
          const dateText = tsOverlay.dataset.dateText;
          const timeText = tsOverlay.dataset.timeText;
          const cx = finalCanvas.width * 0.03;
          const cy1 = finalCanvas.height - (finalCanvas.height * 0.03) - (fontSize * 1.2);
          const cy2 = finalCanvas.height - (finalCanvas.height * 0.03);
          
          ctx.fillStyle = '#000';
          const offset = 2;
          ctx.fillText(dateText, cx+offset, cy1+offset);
          ctx.fillText(dateText, cx-offset, cy1-offset);
          ctx.fillText(dateText, cx+offset, cy1-offset);
          ctx.fillText(dateText, cx-offset, cy1+offset);
          
          ctx.fillText(timeText, cx+offset, cy2+offset);
          ctx.fillText(timeText, cx-offset, cy2-offset);
          ctx.fillText(timeText, cx+offset, cy2-offset);
          ctx.fillText(timeText, cx-offset, cy2+offset);
          
          ctx.fillStyle = '#fff';
          ctx.fillText(dateText, cx, cy1);
          ctx.fillText(timeText, cx, cy2);
      }
      
      finalCanvas.toBlob(async (bakedBlob) => {
          const formData = new FormData();
          formData.append('slug', slug);
          formData.append('guest_name', guestNameInput.value.trim());
          formData.append('audio', audioBlob, 'audio.webm');
          formData.append('cover', bakedBlob, 'cover.jpg');
          
          // Serialize stickers anyway (for future dynamic animations)
          const cleanStickers = stickersData.map(s => ({ type: s.type, x: s.x, y: s.y, scale: s.scale, rotation: s.rotation }));
          formData.append('stickers', JSON.stringify(cleanStickers));
          
          try {
            const res = await fetch('api/upload_memory.php', {
              method: 'POST',
              body: formData
            });
            const result = await res.json();
            
            if (result.success) {
             btnSaveMemory.innerHTML = '<i class="fa-solid fa-check-circle"></i> Saved!';
             setTimeout(() => {
                const successOverlay = document.getElementById('success-overlay');
                if (successOverlay) {
                  successOverlay.style.display = 'flex';
                } else {
                  window.location.reload();
                }
              }, 500);
            } else {
              alert("Upload failed: " + result.error);
              btnSaveMemory.innerHTML = originalText;
              btnSaveMemory.disabled = false;
              if (btnCancelMemory) btnCancelMemory.disabled = false;
            }
          } catch (err) {
            alert("Network error during upload.");
            btnSaveMemory.innerHTML = originalText;
            btnSaveMemory.disabled = false;
            if (btnCancelMemory) btnCancelMemory.disabled = false;
          }
      }, 'image/jpeg', 0.80);
    });
  }

});
