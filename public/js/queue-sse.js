/**
 * queue-sse.js
 * Reusable SSE client untuk papan antrian & dashboard admin.
 *
 * Cara pakai — taruh sebelum tag ini di blade:
 *
 *   <script>
 *     window.QueueSSEConfig = {
 *       streamUrl : '...route...',
 *       mode      : 'board' | 'admin',
 *       csrfToken : '...token...',
 *       poliName  : '...',          // board only
 *       lastQueue : null,           // board only — nomor antrian saat ini
 *     };
 *   </script>
 *   <script src="{{ asset('js/queue-sse.js') }}"></script>
 */

(function () {
    const cfg = window.QueueSSEConfig;
    if (!cfg || !cfg.streamUrl) {
        console.error('QueueSSEConfig tidak ditemukan.');
        return;
    }

    // ── Shared: connection state ──────────────────────────────
    let source;
    let retryTimeout;
    let retryDelay = 3000;

    function setConnStatus(connected) {
        const dot   = document.getElementById('conn-dot');
        const label = document.getElementById('conn-label');
        if (!dot || !label) return;
        // Selalu tampilkan Realtime — reconnect terjadi di background
        dot.style.background = '#4ade80';
        label.textContent    = cfg.mode === 'board' ? 'Terhubung' : 'Realtime';
    }

    // ── Board mode ────────────────────────────────────────────
    let lastQueueNumber = cfg.lastQueue ?? null;

    function playNotification(queueNumber, customerName) {
        const audio = document.getElementById('audio-dingdong');
        if (!audio) return;
        audio.currentTime = 0;
        const promise = audio.play();
        if (promise !== undefined) {
            promise
                .then(() => { audio.onended = () => speakQueue(queueNumber, customerName); })
                .catch(() => speakQueue(queueNumber, customerName));
        } else {
            speakQueue(queueNumber, customerName);
        }
    }

    function speakQueue(queueNumber, customerName) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const numSpaced = queueNumber.split('').join(' ');
        const text      = `Nomor antrian ${numSpaced}, ${customerName}, silakan menuju ${cfg.poliName}.`;
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang   = 'id-ID';
        utterance.rate   = 0.85;
        utterance.pitch  = 1.1;
        utterance.volume = 1;
        window.speechSynthesis.speak(utterance);
    }

    let lastCalledAt = null;

    function updateBoard(state) {
        const waitingEl = document.getElementById('board-waiting');
        const lateEl    = document.getElementById('board-late');
        if (waitingEl) waitingEl.textContent = state.waiting_count ?? 0;
        if (lateEl)    lateEl.textContent    = state.late_count ?? 0;

        const numEl     = document.getElementById('board-number');
        const nameEl    = document.getElementById('board-name');
        const noQueueEl = document.getElementById('no-queue-msg');

        if (state.current) {
            const newNumber   = state.current.queue_number;
            const newCalledAt = state.current.called_at;
            const isNew       = newNumber !== lastQueueNumber;
            const isRecall    = !isNew && newCalledAt !== lastCalledAt;

            if (numEl)  { numEl.textContent  = newNumber; numEl.style.display  = ''; }
            if (nameEl) { nameEl.textContent = state.current.customer_name; nameEl.style.display = ''; }
            if (noQueueEl) noQueueEl.style.display = 'none';

            if (isNew || isRecall) {
                if (numEl) {
                    numEl.classList.remove('flash');
                    void numEl.offsetWidth;
                    numEl.classList.add('flash');
                }
                playNotification(newNumber, state.current.customer_name);
                lastQueueNumber = newNumber;
                lastCalledAt    = newCalledAt;
            }
        } else {
            if (numEl)  numEl.style.display  = 'none';
            if (nameEl) nameEl.style.display = 'none';
            if (noQueueEl) noQueueEl.style.display = '';
            lastQueueNumber = null;
            lastCalledAt    = null;
        }

        if (state.updated_at) {
            const el = document.getElementById('last-updated');
            if (el) el.textContent = 'Update: ' + new Date(state.updated_at).toLocaleTimeString('id-ID');
        }
    }

    // ── Admin mode ────────────────────────────────────────────
    function updateAdmin(state) {
        // Stats
        const statWaiting = document.getElementById('stat-waiting');
        const statLate    = document.getElementById('stat-late');
        if (statWaiting) statWaiting.textContent = state.waiting_count ?? 0;
        if (statLate)    statLate.textContent    = state.late_count ?? 0;

        // Current queue
        const numEl  = document.getElementById('current-number');
        const nameEl = document.getElementById('current-name');
        if (state.current && numEl) {
            numEl.textContent  = state.current.queue_number;
            nameEl.textContent = state.current.customer_name;
        }

        // Waiting list
        const waitingList = document.getElementById('waiting-list');
        if (waitingList && state.waiting_list !== undefined) {
            if (state.waiting_list.length === 0) {
                waitingList.innerHTML = `
                    <div class="empty-state">
                        <span class="mdi mdi-check-all"></span>
                        Tidak ada antrian menunggu
                    </div>`;
            } else {
                waitingList.innerHTML = state.waiting_list.map(q => `
                    <div class="queue-item">
                        <span class="q-num">${q.queue_number}</span>
                        <span class="q-name">${q.customer_name}</span>
                        <div class="q-actions">
                            <form action="/antrian/mark-late/${q.id}" method="POST">
                                <input type="hidden" name="_token" value="${cfg.csrfToken}">
                                <button type="submit" class="btn-sm btn-late">
                                    <span class="mdi mdi-account-off"></span> Late
                                </button>
                            </form>
                        </div>
                    </div>`).join('');
            }
            const wHeader = document.getElementById('waiting-header-count');
            if (wHeader) wHeader.textContent = `Antrian Menunggu (${state.waiting_count ?? 0})`;
        }

        // Done list
        const doneList = document.getElementById('done-list');
        if (doneList && state.done_list !== undefined) {
            if (state.done_list.length === 0) {
                doneList.innerHTML = `
                    <div class="empty-state">
                        <span class="mdi mdi-clipboard-check-outline"></span>
                        Belum ada antrian selesai hari ini
                    </div>`;
            } else {
                doneList.innerHTML = state.done_list.map(q => {
                    const time = q.finished_at
                        ? new Date(q.finished_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                        : '';
                    return `
                        <div class="queue-item">
                            <span class="q-num">${q.queue_number}</span>
                            <span class="q-name">${q.customer_name}</span>
                            <span class="q-time">${time}</span>
                        </div>`;
                }).join('');
            }
            const dHeader = document.getElementById('done-header-count');
            if (dHeader) dHeader.textContent = `Antrian Selesai (${state.done_count ?? 0})`;
        }
        if (lateList && state.late_list !== undefined) {
            if (state.late_list.length === 0) {
                lateList.innerHTML = `
                    <div class="empty-state">
                        <span class="mdi mdi-check"></span>
                        Tidak ada antrian terlambat
                    </div>`;
            } else {
                lateList.innerHTML = state.late_list.map(q => `
                    <div class="queue-item">
                        <span class="q-num">${q.queue_number}</span>
                        <span class="q-name">${q.customer_name}</span>
                        <div class="q-actions">
                            <form action="/antrian/call-late/${q.id}" method="POST">
                                <input type="hidden" name="_token" value="${cfg.csrfToken}">
                                <button type="submit" class="btn-sm btn-recall">
                                    <span class="mdi mdi-phone"></span> Recall
                                </button>
                            </form>
                        </div>
                    </div>`).join('');
            }
            const lHeader = document.getElementById('late-header-count');
            if (lHeader) lHeader.textContent = `Antrian Terlambat (${state.late_count ?? 0})`;
        }
    }

    // ── Countdown timer ───────────────────────────────────
    let countdownVal  = 2;
    let countdownTimer = null;

    function startCountdown() {
        clearInterval(countdownTimer);
        countdownVal = 2;
        updateCountdownEl();

        countdownTimer = setInterval(function () {
            countdownVal--;
            if (countdownVal < 0) countdownVal = 0;
            updateCountdownEl();
        }, 1000);
    }

    function updateCountdownEl() {
        const el = document.getElementById('sse-countdown');
        if (el) el.textContent = countdownVal + 's';
    }

    // ── SSE connection ────────────────────────────────────────
    function connectSSE() {
        clearTimeout(retryTimeout);
        source = new EventSource(cfg.streamUrl);

        source.onopen = function () {
            retryDelay = 3000;
            setConnStatus(true);
        };

        source.onmessage = function (event) {
            try {
                const state = JSON.parse(event.data);
                if (cfg.mode === 'board') {
                    updateBoard(state);
                } else if (cfg.mode === 'admin') {
                    updateAdmin(state);
                }
            } catch (e) {
                console.error('SSE parse error:', e);
            }
        };

        source.onerror = function () {
            source.close();
            setConnStatus(false);
            retryDelay = Math.min(retryDelay * 2, 30000);
            console.warn(`SSE lost. Retry in ${retryDelay / 1000}s...`);
            retryTimeout = setTimeout(connectSSE, retryDelay);
        };
    }

    connectSSE();

})();
