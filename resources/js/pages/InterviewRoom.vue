<template>
  <div class="room-root" :class="{ 'chat-open': isChatOpen }">

    <!-- ═══════════════════════ HEADER ═══════════════════════ -->
    <header class="room-header">
      <div class="header-left">
        <div class="logo-pill">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M15 10l5-5v14l-5-5v-4z"/>
            <rect x="2" y="3" width="11" height="18" rx="3"/>
          </svg>
        </div>
        <div class="header-title">
          <span class="room-label">Room #{{ roomId }}</span>
          <span class="conn-state" :class="connStateClass">{{ connStateLabel }}</span>
        </div>
      </div>

      <div class="header-right">
        <!-- Status badge -->
        <div id="connectionStatus" class="status-badge" :class="statusBadgeClass">
          <span class="status-dot"></span>
          {{ statusText }}
        </div>

        <div class="divider-v"></div>

        <!-- Chat toggle -->
        <button class="icon-btn" @click="toggleChat" title="Chat">
          <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
          </svg>
          <span v-if="unreadCount > 0" class="chat-badge">{{ unreadCount }}</span>
        </button>
      </div>
    </header>

    <!-- ═══════════════════════ VIDEO GRID ═══════════════════════ -->
    <main class="video-grid">

      <!-- Local stream -->
      <div class="video-card video-card--local" :class="{ 'cam-off': isVideoOff }">
        <video ref="localVideo" autoplay playsinline muted class="video-el mirror"></video>

        <transition name="fade-scale">
          <div v-if="isVideoOff" class="cam-placeholder">
            <div class="avatar-ring">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 16v1a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h2m5.66 0H14a2 2 0 012 2v3.34l1 1L23 7v10"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </div>
            <p>Camera off</p>
          </div>
        </transition>

        <div class="video-label video-label--you">
          <span class="dot dot--blue"></span>
          You · {{ roleLabel }}
        </div>
      </div>

      <!-- Remote stream -->
      <div class="video-card video-card--remote">
        <video ref="remoteVideo" autoplay playsinline class="video-el"></video>

        <transition name="fade-scale">
          <div v-if="!hasRemoteStream" class="cam-placeholder">
            <div class="avatar-ring avatar-ring--pulse">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <p>{{ isReady ? 'Establishing connection…' : 'Awaiting participant…' }}</p>
            <span class="sub-label">Secure peer-to-peer tunnel</span>
          </div>
        </transition>

        <div class="video-label video-label--peer">
          <span class="dot dot--indigo"></span>
          {{ peerRoleLabel }}
        </div>
      </div>
    </main>

    <!-- ═══════════════════════ CONTROLS ═══════════════════════ -->
    <footer class="controls">
      <div class="controls-inner">

        <!-- Mute -->
        <button class="ctrl-btn" :class="{ 'ctrl-btn--off': isMuted }" @click="toggleAudio" title="Toggle mic">
          <svg v-if="!isMuted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/>
            <path d="M19 10v2a7 7 0 01-14 0v-2"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
            <line x1="8" y1="23" x2="16" y2="23"/>
          </svg>
          <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="1" y1="1" x2="23" y2="23"/>
            <path d="M9 9v3a3 3 0 005.12 2.12M15 9.34V4a3 3 0 00-5.94-.6"/>
            <path d="M17 16.95A7 7 0 015 12v-2m14 0v2a7 7 0 01-.11 1.23"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
          </svg>
        </button>

        <!-- Camera -->
        <button class="ctrl-btn" :class="{ 'ctrl-btn--off': isVideoOff }" @click="toggleVideo" title="Toggle camera">
          <svg v-if="!isVideoOff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M23 7l-7 5 7 5V7z"/>
            <rect x="1" y="5" width="15" height="14" rx="2"/>
          </svg>
          <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 16v1a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h2m5.66 0H14a2 2 0 012 2v3.34l1 1L23 7v10"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
          </svg>
        </button>

        <div class="divider-v divider-v--short"></div>

        <!-- Manual call (if ready but no stream) -->
        <button
          v-if="isReady && !hasRemoteStream && currentUser?.role === 'recruiter'"
          class="ctrl-btn ctrl-btn--call"
          @click="initiateManualCall"
          title="Start call"
        >
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 15.5c-1.2 0-2.5-.2-3.6-.6-.3-.1-.7 0-.9.2l-2.2 2.2c-2.8-1.5-5.1-3.8-6.6-6.6l2.2-2.2c.2-.2.3-.6.2-.9-.4-1.1-.6-2.4-.6-3.6C8.5 3.4 8 3 7.5 3H4C3.4 3 3 3.4 3 4c0 9.4 7.6 17 17 17 .6 0 1-.4 1-1v-3.5c0-.5-.4-1-1-1z"/>
          </svg>
          Start Call
        </button>

        <!-- Hang up -->
        <button class="ctrl-btn ctrl-btn--hangup" @click="handleLeave" title="Leave">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M18.36 6.64a9 9 0 10-12.73 0"/>
            <line x1="12" y1="2" x2="12" y2="12"/>
          </svg>
          Leave
        </button>
      </div>
    </footer>

    <!-- ═══════════════════════ CHAT SIDEBAR ═══════════════════════ -->
    <aside class="chat-sidebar" :class="{ 'chat-sidebar--open': isChatOpen }">
      <div class="chat-header">
        <span>Live Chat</span>
        <button class="icon-btn" @click="closeChat">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>

      <div ref="chatMessages" class="chat-messages">
        <p class="chat-notice">Messages are not saved after this session.</p>
        <div
          v-for="(msg, i) in messages"
          :key="i"
          class="chat-bubble-wrap"
          :class="msg.isMe ? 'chat-bubble-wrap--me' : 'chat-bubble-wrap--them'"
        >
          <div class="chat-bubble" :class="msg.isMe ? 'chat-bubble--me' : 'chat-bubble--them'">
            {{ msg.text }}
          </div>
        </div>
      </div>

      <div class="chat-input-row">
        <input
          v-model="chatDraft"
          placeholder="Write a message…"
          class="chat-input"
          @keyup.enter="sendMessage"
        />
        <button class="chat-send" @click="sendMessage">
          <svg viewBox="0 0 20 20" fill="currentColor" style="transform:rotate(90deg)">
            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
          </svg>
        </button>
      </div>
    </aside>

    <!-- ═══════════════════════ REVIEW MODAL ═══════════════════════ -->
    <transition name="modal">
      <div v-if="showReviewModal" class="modal-backdrop">
        <div class="modal-card">
          <div class="modal-glow"></div>

          <div class="modal-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <h2>Session Ended</h2>
          <p class="modal-sub">Please leave feedback about the other participant.</p>

          <!-- Pass/Fail (recruiter only) -->
          <div v-if="currentUser?.role === 'recruiter'" class="modal-section">
            <label class="field-label">Decision</label>
            <div class="decision-row">
              <button
                class="decision-btn"
                :class="{ 'decision-btn--pass': interviewDecision === true }"
                @click="interviewDecision = true"
              >✅ Passed</button>
              <button
                class="decision-btn"
                :class="{ 'decision-btn--fail': interviewDecision === false }"
                @click="interviewDecision = false"
              >❌ Failed</button>
            </div>
          </div>

          <!-- Stars -->
          <div class="modal-section">
            <label class="field-label">Rating</label>
            <div class="stars">
              <button
                v-for="i in 5"
                :key="i"
                class="star-btn"
                :class="{ 'star-btn--on': reviewRating >= i }"
                @click="reviewRating = i"
              >★</button>
            </div>
          </div>

          <!-- Comment -->
          <div class="modal-section">
            <label class="field-label">Notes</label>
            <textarea
              v-model="reviewComment"
              class="modal-textarea"
              placeholder="Describe the session…"
            ></textarea>
          </div>

          <button
            class="submit-btn"
            :disabled="isSubmittingReview || !reviewComment || (currentUser?.role === 'recruiter' && interviewDecision === null)"
            @click="submitReview"
          >
            <span v-if="!isSubmittingReview">Submit Report</span>
            <span v-else class="spinner-row">
              <svg class="spin" viewBox="0 0 24 24"><circle class="spin-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="spin-path" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Processing…
            </span>
          </button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

// ─── Router ────────────────────────────────────────────────────────────────
const router = useRouter();
const route  = useRoute();
const roomId = route.params.id;

// ─── Template refs ──────────────────────────────────────────────────────────
const localVideo   = ref(null);
const remoteVideo  = ref(null);
const chatMessages = ref(null);

// ─── App state ──────────────────────────────────────────────────────────────
const currentUser      = ref(null);
const interviewData    = ref(null);

// ─── WebRTC state (ported from Blade) ───────────────────────────────────────
let localStream       = null;
let peerConnection    = null;
let callStarted       = false;
let queuedCandidates  = [];

const hasRemoteStream  = ref(false);
const connectionState  = ref("new");   // new | connecting | connected | disconnected | failed | closed
const isMuted          = ref(false);
const isVideoOff       = ref(false);
const isReady          = ref(false);   // true when peer is in the room

// ─── Chat state ─────────────────────────────────────────────────────────────
const isChatOpen   = ref(false);
const chatDraft    = ref("");
const messages     = ref([]);
const unreadCount  = ref(0);

// ─── Review state ────────────────────────────────────────────────────────────
const showReviewModal    = ref(false);
const reviewRating       = ref(5);
const reviewComment      = ref("");
const isSubmittingReview = ref(false);
const interviewDecision  = ref(null);

// ─── Computed labels ─────────────────────────────────────────────────────────
const roleLabel = computed(() =>
  currentUser.value?.role === "recruiter" ? "Interviewer" : "Candidate"
);
const peerRoleLabel = computed(() =>
  currentUser.value?.role === "recruiter" ? "Candidate" : "Interviewer"
);

const connStateLabel = computed(() => ({
  new:          "Initializing…",
  connecting:   "Connecting…",
  connected:    "Encrypted Connection",
  disconnected: "Peer Disconnected",
  failed:       "Connection Error",
  closed:       "Session Closed",
}[connectionState.value] ?? "Ready"));

const connStateClass = computed(() => ({
  connected: "state--ok",
  failed:    "state--err",
}[connectionState.value] ?? ""));

const statusText = computed(() => {
  if (connectionState.value === "connected") return "En ligne ❤️";
  if (connectionState.value === "failed" || connectionState.value === "disconnected") return "Connexion perdue…";
  if (!isReady.value) return "En attente de l'autre participant…";
  return "Connexion au serveur…";
});

const statusBadgeClass = computed(() => {
  if (connectionState.value === "connected") return "badge--green";
  if (connectionState.value === "failed")    return "badge--red";
  return "badge--yellow";
});

// ─── Lifecycle ───────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const [userRes, interviewRes] = await Promise.all([
      axios.get("/api/user"),
      axios.get(`/api/v1/interviews/${roomId}`),
    ]);
    currentUser.value   = userRes.data;
    interviewData.value = interviewRes.data;
  } catch (err) {
    console.error("Could not fetch user / interview data:", err);
  }

  // ── 1. Get local media ───────────────────────────────────────────────────
  try {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    localVideo.value.srcObject = localStream;
  } catch (err) {
    console.warn("Camera/mic unavailable:", err);
    connectionState.value = "failed";
  }

  // ── 2. Echo connection monitoring ────────────────────────────────────────
  window.Echo.connector.pusher.connection.bind("state_change", ({ current }) => {
    if (current === "connecting")   connectionState.value = "connecting";
    if (current === "unavailable")  connectionState.value = "failed";
  });

  // ── 3. Join presence channel ─────────────────────────────────────────────
  const channel = window.Echo.join(`interview.${roomId}`);

  // ── 4. Presence events (ported from Blade) ───────────────────────────────
  channel.here((users) => {
    console.log(`${users.length} user(s) in room.`);
    if (users.length > 1) {
      isReady.value = true;
      if (!callStarted && currentUser.value?.role === 'recruiter') {
        startCall(channel);
      }
    }
  })
  .joining((user) => {
    console.log(`${user.name} joined — ready to receive offer.`);
    isReady.value = true;
    if (!callStarted && currentUser.value?.role === 'recruiter') {
      startCall(channel);
    }
  })
  .leaving((user) => {
    console.log(`${user.name} left.`);
    isReady.value      = false;
    hasRemoteStream.value = false;
    connectionState.value = "disconnected";
    if (remoteVideo.value) remoteVideo.value.srcObject = null;
    if (peerConnection) {
      peerConnection.close();
      peerConnection = null;
    }
    callStarted      = false;
    queuedCandidates = [];
  });

  // ── 5. Chat whisper ───────────────────────────────────────────────────────
  channel.listenForWhisper("ChatMessage", (data) => {
    messages.value.push({ text: data.text, isMe: false });
    if (!isChatOpen.value) unreadCount.value++;
    scrollChat();
  });

  // ── 6. WebRTC signaling whisper (ported from Blade) ───────────────────────
  channel.listenForWhisper("WebRTCSignal", async (data) => {
    console.log("Signal received:", data.type);

    // Peer refreshed — reset and re-answer
    if (data.type === "offer" && peerConnection) {
      console.log("New offer received (peer refreshed). Resetting…");
      peerConnection.close();
      peerConnection    = null;
      callStarted       = false;
      queuedCandidates  = [];
    }

    if (!peerConnection) createPeerConnection(channel);

    try {
      if (data.type === "offer") {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        channel.whisper("WebRTCSignal", { type: "answer", sdp: answer });
        await drainCandidateQueue();

      } else if (data.type === "answer") {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
        await drainCandidateQueue();

      } else if (data.type === "candidate") {
        const candidate = new RTCIceCandidate(data.candidate);
        if (peerConnection.remoteDescription) {
          await peerConnection.addIceCandidate(candidate);
        } else {
          queuedCandidates.push(candidate);
        }
      }
    } catch (err) {
      console.error("WebRTC error:", err);
    }
  });

  // Store channel on window for manual call trigger (keep Blade parity)
  window.__echoChannel = channel;
});

// ─── WebRTC helpers ──────────────────────────────────────────────────────────
const rtcConfig = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

function createPeerConnection(channel) {
  peerConnection = new RTCPeerConnection(rtcConfig);

  if (localStream) {
    localStream.getTracks().forEach((track) =>
      peerConnection.addTrack(track, localStream)
    );
  } else {
    peerConnection.addTransceiver("audio", { direction: "recvonly" });
    peerConnection.addTransceiver("video", { direction: "recvonly" });
  }

  peerConnection.ontrack = (event) => {
    console.log("Remote stream received!");
    if (event.streams && event.streams[0]) {
      remoteVideo.value.srcObject = event.streams[0];
    } else {
      const inboundStream = new MediaStream();
      inboundStream.addTrack(event.track);
      remoteVideo.value.srcObject = inboundStream;
    }
    hasRemoteStream.value = true;

    // Explicitly trigger play to override strict browser autoplay restrictions
    nextTick(() => {
      if (remoteVideo.value) {
        remoteVideo.value.play().catch(e => console.warn("Autoplay block (interaction needed):", e));
      }
    });
  };

  peerConnection.onconnectionstatechange = () => {
    const state = peerConnection.connectionState;
    connectionState.value = state;
    console.log("Connection state:", state);
    if (state === 'failed' || state === 'disconnected') {
      hasRemoteStream.value = false;
    }
  };

  peerConnection.onicecandidate = (event) => {
    if (event.candidate) {
      channel.whisper("WebRTCSignal", { type: "candidate", candidate: event.candidate });
    }
  };
}

async function startCall(channel) {
  if (callStarted) return;
  callStarted = true;
  console.log("Initiating call…");
  createPeerConnection(channel);
  const offer = await peerConnection.createOffer();
  await peerConnection.setLocalDescription(offer);
  channel.whisper("WebRTCSignal", { type: "offer", sdp: offer });
}

async function drainCandidateQueue() {
  while (queuedCandidates.length > 0) {
    await peerConnection.addIceCandidate(queuedCandidates.shift());
  }
}

function initiateManualCall() {
  if (window.__echoChannel) {
    startCall(window.__echoChannel);
  }
}

// ─── Media controls ──────────────────────────────────────────────────────────
function toggleAudio() {
  if (!localStream) return;
  const track = localStream.getAudioTracks()[0];
  if (!track) return;
  track.enabled = !track.enabled;
  isMuted.value = !track.enabled;
}

function toggleVideo() {
  if (!localStream) return;
  const track = localStream.getVideoTracks()[0];
  if (!track) return;
  track.enabled = !track.enabled;
  isVideoOff.value = !track.enabled;
}

// ─── Chat helpers ────────────────────────────────────────────────────────────
function toggleChat() {
  isChatOpen.value  = !isChatOpen.value;
  if (isChatOpen.value) unreadCount.value = 0;
}
function closeChat() {
  isChatOpen.value = false;
}
function sendMessage() {
  const text = chatDraft.value.trim();
  if (!text || !window.__echoChannel) return;
  messages.value.push({ text, isMe: true });
  window.__echoChannel.whisper("ChatMessage", { text });
  chatDraft.value = "";
  scrollChat();
}
async function scrollChat() {
  await nextTick();
  if (chatMessages.value)
    chatMessages.value.scrollTop = chatMessages.value.scrollHeight;
}

// ─── Leave / review ──────────────────────────────────────────────────────────
function handleLeave() {
  if (window.__echoChannel)
    window.__echoChannel.whisper("WebRTCSignal", { type: "peer-left" });
  if (peerConnection) peerConnection.close();
  if (localStream)    localStream.getTracks().forEach((t) => t.stop());
  window.Echo.leave(`interview.${roomId}`);
  showReviewModal.value = true;
}

async function submitReview() {
  isSubmittingReview.value = true;
  try {
    const reviewedUserId = currentUser.value.role === "recruiter"
      ? interviewData.value.application.student_id
      : interviewData.value.application.project.recruiter_id;

    const promises = [
      axios.post("/api/v1/reviews", {
        reviewed_user_id: reviewedUserId,
        rating:           reviewRating.value,
        comment:          reviewComment.value,
      }),
    ];

    if (currentUser.value.role === "recruiter") {
      promises.push(
        axios.post(`/api/v1/interviews/${roomId}/result`, {
          passed: interviewDecision.value,
        })
      );
    }

    await Promise.all(promises);
    router.push("/dashboard");
  } catch (err) {
    console.error("Review submit failed:", err);
    router.push("/dashboard");
  } finally {
    isSubmittingReview.value = false;
  }
}
</script>

<style scoped>
/* ── Fonts ─────────────────────────────────────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

/* ── Tokens ─────────────────────────────────────────────────────────────────── */
:root {
  --bg:         #07080d;
  --surface:    #0f1117;
  --surface2:   #161820;
  --border:     rgba(255,255,255,0.06);
  --text:       #e8eaf0;
  --muted:      #4a4f6a;
  --accent:     #3b82f6;
  --accent2:    #6366f1;
  --red:        #ef4444;
  --green:      #22c55e;
  --yellow:     #eab308;
  --radius-xl:  1.75rem;
  --radius-lg:  1rem;
  --radius-md:  0.6rem;
  --chat-w:     22rem;
  --header-h:   4rem;
  --footer-h:   6rem;
  --font-sans:  'Syne', sans-serif;
  --font-mono:  'JetBrains Mono', monospace;
}

/* ── Reset / Base ───────────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
button { cursor: pointer; background: none; border: none; color: inherit; font: inherit; }
input, textarea { font: inherit; }

/* ── Root shell ─────────────────────────────────────────────────────────────── */
.room-root {
  font-family: var(--font-sans);
  background: var(--bg);
  color: var(--text);
  height: 100dvh;
  display: grid;
  grid-template-rows: var(--header-h) 1fr var(--footer-h);
  grid-template-columns: 1fr;
  overflow: hidden;
  position: relative;
  /* subtle radial glow */
  background-image: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,130,246,0.06) 0%, transparent 70%);
}

/* ── Header ─────────────────────────────────────────────────────────────────── */
.room-header {
  grid-row: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  border-bottom: 1px solid var(--border);
  background: rgba(7,8,13,0.85);
  backdrop-filter: blur(20px);
  z-index: 20;
}

.header-left { display: flex; align-items: center; gap: 0.75rem; }

.logo-pill {
  width: 2.2rem; height: 2.2rem;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  border-radius: 0.55rem;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 20px rgba(59,130,246,0.25);
}
.logo-pill svg { width: 1rem; height: 1rem; color: #fff; }

.header-title { display: flex; flex-direction: column; gap: 0.15rem; }
.room-label {
  font-family: var(--font-mono);
  font-size: 0.6rem; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--accent);
}
.conn-state { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); }
.state--ok  { color: var(--green) !important; }
.state--err { color: var(--red)   !important; }

.header-right { display: flex; align-items: center; gap: 0.75rem; }

.status-badge {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.3rem 0.8rem;
  border-radius: 999px;
  font-family: var(--font-mono);
  font-size: 0.6rem; font-weight: 700;
  letter-spacing: 0.1em;
  border: 1px solid;
  transition: all 0.4s;
}
.status-dot { width: 0.45rem; height: 0.45rem; border-radius: 50%; background: currentColor; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

.badge--yellow { background: rgba(234,179,8,.12); color: var(--yellow); border-color: rgba(234,179,8,.25); }
.badge--green  { background: rgba(34,197,94,.12);  color: var(--green);  border-color: rgba(34,197,94,.25); }
.badge--red    { background: rgba(239,68,68,.12);   color: var(--red);    border-color: rgba(239,68,68,.25); }

.divider-v { width: 1px; height: 2rem; background: var(--border); }
.divider-v--short { height: 2.5rem; }

.icon-btn {
  width: 2.2rem; height: 2.2rem;
  border-radius: 0.5rem;
  display: flex; align-items: center; justify-content: center;
  color: var(--muted);
  transition: background 0.2s, color 0.2s;
  position: relative;
}
.icon-btn:hover { background: var(--surface2); color: var(--text); }
.icon-btn svg { width: 1.1rem; height: 1.1rem; }

.chat-badge {
  position: absolute; top: -0.15rem; right: -0.15rem;
  background: var(--red); color: #fff;
  font-size: 0.5rem; font-weight: 800;
  min-width: 1rem; height: 1rem;
  border-radius: 999px; border: 2px solid var(--bg);
  display: flex; align-items: center; justify-content: center;
  padding: 0 0.2rem;
}

/* ── Video grid ─────────────────────────────────────────────────────────────── */
.video-grid {
  grid-row: 2;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  padding: 1rem;
  background: radial-gradient(ellipse 80% 80% at 50% 50%, rgba(15,17,23,0.5) 0%, var(--bg) 100%);
  overflow: hidden;
  transition: padding-right 0.35s ease;
}

.room-root.chat-open .video-grid { padding-right: calc(var(--chat-w) + 1rem); }

.video-card {
  position: relative;
  background: var(--surface);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border);
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,0,0,0.35);
  transition: border-color 0.3s;
}
.video-card--local:hover  { border-color: rgba(59,130,246,0.3); }
.video-card--remote:hover { border-color: rgba(99,102,241,0.3); }

.video-el {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
}
.mirror { transform: scaleX(-1); }

/* camera-off overlay */
.cam-placeholder {
  position: absolute; inset: 0;
  background: var(--surface);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 1rem;
  z-index: 2;
}
.cam-placeholder p {
  font-size: 0.65rem; font-weight: 800;
  letter-spacing: 0.25em; text-transform: uppercase;
  color: var(--muted);
}
.sub-label {
  font-size: 0.55rem; letter-spacing: 0.2em; text-transform: uppercase;
  color: rgba(74,79,106,0.6);
}
.avatar-ring {
  width: 5rem; height: 5rem;
  border-radius: 50%;
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  color: var(--muted);
  position: relative;
}
.avatar-ring svg { width: 2.2rem; height: 2.2rem; }
.avatar-ring--pulse::before {
  content: '';
  position: absolute; inset: -4px;
  border-radius: 50%;
  border: 1px solid rgba(59,130,246,0.3);
  animation: ring-ping 2s infinite;
}
@keyframes ring-ping { 0%{opacity:1;transform:scale(1)} 100%{opacity:0;transform:scale(1.4)} }

/* video labels */
.video-label {
  position: absolute; bottom: 1rem; left: 1rem;
  display: flex; align-items: center; gap: 0.4rem;
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: var(--radius-md);
  padding: 0.35rem 0.7rem;
  font-size: 0.62rem; font-weight: 800;
  letter-spacing: 0.15em; text-transform: uppercase;
  z-index: 5;
}
.dot { width: 0.4rem; height: 0.4rem; border-radius: 50%; }
.dot--blue   { background: var(--accent); box-shadow: 0 0 6px rgba(59,130,246,0.6); }
.dot--indigo { background: var(--accent2); box-shadow: 0 0 6px rgba(99,102,241,0.6); }

/* ── Controls ────────────────────────────────────────────────────────────────── */
.controls {
  grid-row: 3;
  display: flex; align-items: center; justify-content: center;
  background: rgba(7,8,13,0.9);
  backdrop-filter: blur(30px);
  border-top: 1px solid var(--border);
}

.controls-inner { display: flex; align-items: center; gap: 0.75rem; }

.ctrl-btn {
  width: 3.2rem; height: 3.2rem;
  border-radius: var(--radius-md);
  background: var(--surface2);
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  color: var(--text);
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}
.ctrl-btn:hover { background: #1e2030; transform: scale(1.07); }
.ctrl-btn:active { transform: scale(0.96); }
.ctrl-btn svg { width: 1.2rem; height: 1.2rem; }
.ctrl-btn--off { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.35); color: var(--red); }
.ctrl-btn--off:hover { background: rgba(239,68,68,0.25); }

.ctrl-btn--call {
  display: flex; align-items: center; gap: 0.5rem;
  width: auto; padding: 0 1.2rem;
  background: var(--accent);
  border-color: transparent;
  font-size: 0.65rem; font-weight: 800;
  letter-spacing: 0.15em; text-transform: uppercase;
  box-shadow: 0 4px 20px rgba(59,130,246,0.3);
}
.ctrl-btn--call:hover { background: #2563eb; }

.ctrl-btn--hangup {
  display: flex; align-items: center; gap: 0.5rem;
  width: auto; padding: 0 1.2rem;
  background: rgba(239,68,68,0.1);
  border-color: rgba(239,68,68,0.3);
  color: var(--red);
  font-size: 0.65rem; font-weight: 800;
  letter-spacing: 0.15em; text-transform: uppercase;
}
.ctrl-btn--hangup:hover { background: var(--red); color: #fff; }

/* ── Chat sidebar ────────────────────────────────────────────────────────────── */
.chat-sidebar {
  position: absolute; top: 0; right: 0;
  width: var(--chat-w);
  height: 100%;
  background: rgba(15,17,23,0.96);
  backdrop-filter: blur(24px);
  border-left: 1px solid var(--border);
  display: flex; flex-direction: column;
  z-index: 30;
  transform: translateX(100%);
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.chat-sidebar--open { transform: translateX(0); }

.chat-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--border);
  font-weight: 800; font-size: 0.85rem; letter-spacing: 0.05em;
  flex-shrink: 0;
}

.chat-messages {
  flex: 1; overflow-y: auto;
  padding: 1rem;
  display: flex; flex-direction: column; gap: 0.5rem;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }

.chat-notice {
  text-align: center; font-size: 0.6rem;
  color: var(--muted); letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}

.chat-bubble-wrap { display: flex; }
.chat-bubble-wrap--me   { justify-content: flex-end; }
.chat-bubble-wrap--them { justify-content: flex-start; }

.chat-bubble {
  max-width: 82%;
  font-size: 0.8rem; line-height: 1.45;
  padding: 0.5rem 0.75rem;
  border-radius: 1rem;
}
.chat-bubble--me   { background: var(--accent); color: #fff; border-bottom-right-radius: 0.2rem; }
.chat-bubble--them { background: var(--surface2); color: var(--text); border-bottom-left-radius: 0.2rem; }

.chat-input-row {
  display: flex; gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}

.chat-input {
  flex: 1;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 0.5rem 0.75rem;
  font-size: 0.8rem; color: var(--text);
  outline: none;
  transition: border-color 0.2s;
}
.chat-input:focus { border-color: var(--accent); }
.chat-input::placeholder { color: var(--muted); }

.chat-send {
  width: 2.4rem; height: 2.4rem;
  background: var(--accent);
  border-radius: var(--radius-md);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  transition: background 0.2s;
  flex-shrink: 0;
}
.chat-send:hover { background: #2563eb; }
.chat-send svg { width: 0.9rem; height: 0.9rem; }

/* ── Review modal ────────────────────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed; inset: 0;
  z-index: 100;
  display: flex; align-items: center; justify-content: center;
  padding: 1.5rem;
  background: rgba(7,8,13,0.88);
  backdrop-filter: blur(20px);
}

.modal-card {
  position: relative;
  background: var(--surface);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: var(--radius-xl);
  padding: 2.5rem;
  width: 100%; max-width: 28rem;
  box-shadow: 0 32px 64px rgba(0,0,0,0.5);
  overflow: hidden;
}
.modal-glow {
  position: absolute; top: 0; right: 0;
  width: 16rem; height: 16rem;
  background: rgba(59,130,246,0.07);
  filter: blur(80px);
  border-radius: 50%;
  transform: translate(30%, -40%);
  pointer-events: none;
}

.modal-icon {
  width: 3.5rem; height: 3.5rem;
  background: rgba(59,130,246,0.1);
  border-radius: 0.9rem;
  display: flex; align-items: center; justify-content: center;
  color: var(--accent);
  margin-bottom: 1.5rem;
}
.modal-icon svg { width: 1.8rem; height: 1.8rem; }

.modal-card h2 {
  font-size: 1.6rem; font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 0.5rem;
}
.modal-sub {
  font-size: 0.8rem; color: var(--muted);
  line-height: 1.6; margin-bottom: 1.75rem;
}

.modal-section { margin-bottom: 1.5rem; }
.field-label {
  display: block;
  font-size: 0.58rem; font-weight: 800;
  letter-spacing: 0.2em; text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 0.6rem;
}

.decision-row { display: flex; gap: 0.75rem; }
.decision-btn {
  flex: 1; padding: 0.65rem;
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  font-size: 0.75rem; font-weight: 700;
  color: var(--muted);
  transition: all 0.2s;
}
.decision-btn:hover  { background: var(--surface2); color: var(--text); }
.decision-btn--pass  { background: rgba(34,197,94,.1); border-color: rgba(34,197,94,.4); color: var(--green); }
.decision-btn--fail  { background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.4); color: var(--red); }

.stars { display: flex; gap: 0.4rem; }
.star-btn {
  width: 2.8rem; height: 2.8rem;
  border-radius: var(--radius-md);
  background: var(--surface2);
  border: 1px solid var(--border);
  font-size: 1.3rem; color: var(--muted);
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.star-btn:hover { background: var(--surface); color: var(--text); }
.star-btn--on {
  background: var(--accent);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 2px 12px rgba(59,130,246,0.35);
  transform: scale(1.08);
}

.modal-textarea {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 0.9rem 1rem;
  font-size: 0.8rem; color: var(--text);
  resize: none; min-height: 7rem;
  outline: none;
  transition: border-color 0.2s;
}
.modal-textarea:focus  { border-color: rgba(59,130,246,0.5); }
.modal-textarea::placeholder { color: var(--muted); }

.submit-btn {
  width: 100%; height: 3.4rem;
  background: var(--accent);
  border-radius: var(--radius-md);
  font-size: 0.68rem; font-weight: 800;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: #fff;
  box-shadow: 0 8px 24px rgba(59,130,246,0.3);
  transition: background 0.2s, opacity 0.2s, transform 0.15s;
}
.submit-btn:hover:not(:disabled) { background: #2563eb; }
.submit-btn:active:not(:disabled) { transform: scale(0.98); }
.submit-btn:disabled { opacity: 0.3; cursor: not-allowed; }

.spinner-row { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.spin { width: 1rem; height: 1rem; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.spin-track { opacity: 0.25; fill: none; }
.spin-path  { opacity: 0.75; }

/* ── Transitions ────────────────────────────────────────────────────────────── */
.fade-scale-enter-active, .fade-scale-leave-active {
  transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.22,1,.36,1);
}
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.96); }

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.22,1,.36,1);
}
.modal-enter-from { opacity: 0; transform: translateY(16px); }
.modal-leave-to   { opacity: 0; transform: scale(0.95); }
</style>
