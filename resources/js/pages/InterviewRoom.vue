<template>
  <div class="h-screen w-full bg-[#07080d] text-[#e8eaf0] font-syne antialiased overflow-hidden flex flex-col relative">
    
    <header class="h-16 flex items-center justify-between px-6 border-b border-white/10 bg-black/40 backdrop-blur-xl z-20 shrink-0">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M15 10l5-5v14l-5-5v-4z"/>
            <rect x="2" y="3" width="11" height="18" rx="3"/>
          </svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[0.65rem] font-bold font-mono tracking-widest text-blue-400 uppercase">Room #{{ roomId }}</span>
          <span class="text-[0.7rem] font-bold tracking-wider uppercase opacity-60" :class="connectionState === 'connected' ? 'text-green-400' : 'text-neutral-400'">
            {{ connStateLabel }}
          </span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-[0.65rem] font-bold font-mono tracking-wider transition-all duration-500" :class="statusBadgeClassTailwind">
          <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
          {{ statusText }}
        </div>

        <div class="w-px h-8 bg-white/10"></div>

        <button @click="toggleChat" class="relative p-2.5 rounded-xl hover:bg-white/10 text-neutral-400 hover:text-white transition-all">
          <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
          </svg>
          <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 min-w-[1.1rem] h-4.5 bg-red-500 text-white text-[0.6rem] font-black rounded-full border-2 border-[#07080d] flex items-center justify-center px-1">
            {{ unreadCount }}
          </span>
        </button>
      </div>
    </header>

    <main class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 lg:p-6 bg-gradient-to-b from-neutral-900/50 to-[#07080d] overflow-hidden transition-all duration-500" :class="{ 'pr-[22rem]': isChatOpen }">
      
      <!-- Local Stream Wrapper -->
      <div class="relative group aspect-video bg-neutral-950 rounded-3xl border border-white/5 overflow-hidden shadow-2xl transition-all duration-300 hover:border-blue-500/30">
        <video ref="localVideo" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]"></video>
        
        <transition name="fade-scale">
          <div v-if="isVideoOff" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 bg-neutral-950">
            <div class="w-20 h-20 rounded-full border border-white/10 flex items-center justify-center text-neutral-600">
              <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 16v1a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h2m5.66 0H14a2 2 0 012 2v3.34l1 1L23 7v10"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </div>
            <span class="text-[0.65rem] font-black tracking-widest text-neutral-500 uppercase">Camera Off</span>
          </div>
        </transition>

        <div class="absolute bottom-4 left-4 flex items-center gap-2 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-xl border border-white/10 text-[0.65rem] font-black tracking-widest uppercase z-20">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></span>
          You · {{ roleLabel }}
        </div>
      </div>

      <!-- Remote Stream Wrapper -->
      <div class="relative group aspect-video bg-neutral-950 rounded-3xl border border-white/5 overflow-hidden shadow-2xl transition-all duration-300 hover:border-indigo-500/30">
        <video ref="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
        
        <transition name="fade-scale">
          <div v-if="!hasRemoteStream" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-6 bg-neutral-950">
            <div class="relative">
              <div class="absolute inset-[-8px] rounded-full border border-blue-500/30 animate-ping"></div>
              <div class="w-20 h-20 rounded-full border border-white/10 flex items-center justify-center text-neutral-400">
                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
            </div>
            <div class="text-center">
              <p class="text-[0.7rem] font-black tracking-widest text-neutral-400 uppercase">{{ isReady ? 'Connecting...' : 'Awaiting Participant...' }}</p>
              <p class="text-[0.6rem] font-bold tracking-[0.2em] text-neutral-600 mt-2 uppercase italic">E2EE Secured Tunnel</p>
            </div>
          </div>
        </transition>

        <div class="absolute bottom-4 left-4 flex items-center gap-2 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-xl border border-white/10 text-[0.65rem] font-black tracking-widest uppercase z-20">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"></span>
          {{ peerRoleLabel }}
        </div>
      </div>
    </main>

    <footer class="h-24 flex items-center justify-center bg-black/40 backdrop-blur-3xl border-t border-white/10 shrink-0 z-20">
      <div class="flex items-center gap-3">
        <!-- Audio Toggle -->
        <button @click="toggleAudio" class="w-12 h-12 flex items-center justify-center rounded-2xl border transition-all duration-200" :class="isMuted ? 'bg-red-500/10 border-red-500/40 text-red-500 hover:bg-red-500/20' : 'bg-neutral-800 border-white/10 text-white hover:bg-neutral-700 hover:scale-110'">
          <svg v-if="!isMuted" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/>
            <path d="M19 10v2a7 7 0 01-14 0v-2"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
            <line x1="8" y1="23" x2="16" y2="23"/>
          </svg>
          <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="1" y1="1" x2="23" y2="23"/>
            <path d="M9 9v3a3 3 0 005.12 2.12M15 9.34V4a3 3 0 00-5.94-.6"/>
            <path d="M17 16.95A7 7 0 015 12v-2m14 0v2a7 7 0 01-.11 1.23"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
          </svg>
        </button>

        <!-- Video Toggle -->
        <button @click="toggleVideo" class="w-12 h-12 flex items-center justify-center rounded-2xl border transition-all duration-200" :class="isVideoOff ? 'bg-red-500/10 border-red-500/40 text-red-500 hover:bg-red-500/20' : 'bg-neutral-800 border-white/10 text-white hover:bg-neutral-700 hover:scale-110'">
          <svg v-if="!isVideoOff" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M23 7l-7 5 7 5V7z"/>
            <rect x="1" y="5" width="15" height="14" rx="2"/>
          </svg>
          <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 16v1a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2h2m5.66 0H14a2 2 0 012 2v3.34l1 1L23 7v10"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
          </svg>
        </button>

        <div class="w-px h-10 bg-white/10 mx-2"></div>

        <!-- Start Call (Recruiter Only) -->
        <button
          v-if="isReady && !hasRemoteStream && currentUser?.role === 'recruiter'"
          @click="initiateManualCall"
          class="h-12 flex items-center gap-3 px-6 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl text-[0.7rem] font-black tracking-widest uppercase transition-all shadow-lg shadow-blue-500/20"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 15.5c-1.2 0-2.5-.2-3.6-.6-.3-.1-.7 0-.9.2l-2.2 2.2c-2.8-1.5-5.1-3.8-6.6-6.6l2.2-2.2c.2-.2.3-.6.2-.9-.4-1.1-.6-2.4-.6-3.6C8.5 3.4 8 3 7.5 3H4C3.4 3 3 3.4 3 4c0 9.4 7.6 17 17 17 .6 0 1-.4 1-1v-3.5c0-.5-.4-1-1-1z"/>
          </svg>
          Start Call
        </button>

        <!-- Leave Button -->
        <button @click="handleLeave" class="h-12 flex items-center gap-3 px-6 bg-red-500/10 border border-red-500/40 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl text-[0.7rem] font-black tracking-widest uppercase transition-all">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M18.36 6.64a9 9 0 10-12.73 0"/>
            <line x1="12" y1="2" x2="12" y2="12"/>
          </svg>
          Leave
        </button>
      </div>
    </footer>

    <aside class="absolute top-0 right-0 w-[22rem] h-full bg-[#0a0b10] border-l border-white/5 shadow-2xl z-20 flex flex-col transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="isChatOpen ? 'translate-x-0' : 'translate-x-full'">
      <div class="h-16 flex items-center justify-between px-6 border-b border-white/5 shrink-0">
        <span class="text-sm font-black tracking-widest uppercase">Live Chat</span>
        <button @click="closeChat" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>

      <div ref="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
        <p class="text-[0.6rem] text-center text-neutral-500 font-bold tracking-widest uppercase mb-4">Messages are volatile (session-only)</p>
        <div v-for="(msg, i) in messages" :key="i" class="flex" :class="msg.isMe ? 'justify-end' : 'justify-start'">
          <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-[0.85rem] leading-relaxed shadow-sm" :class="msg.isMe ? 'bg-blue-600 text-white rounded-br-md' : 'bg-neutral-800 text-neutral-200 rounded-bl-md'">
            {{ msg.text }}
          </div>
        </div>
      </div>

      <div class="p-4 border-t border-white/5 bg-black/20">
        <div class="flex gap-2">
          <input v-model="chatDraft" placeholder="Type message..." @keyup.enter="sendMessage" class="flex-1 bg-neutral-900 border border-white/5 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500/50 transition-colors" />
          <button @click="sendMessage" class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white rounded-xl hover:bg-blue-500 active:scale-95 transition-all">
            <svg class="w-4 h-4 rotate-90" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <transition name="modal">
      <div v-if="showReviewModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/90 backdrop-blur-xl">
        <div class="relative w-full max-w-lg bg-[#0f1117] border border-white/10 rounded-[2.5rem] p-10 overflow-hidden shadow-[0_32px_100px_rgba(0,0,0,0.8)]">
          <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-500/10 blur-[80px] rounded-full pointer-events-none"></div>
          
          <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 mb-8">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>

          <h2 class="text-3xl font-black tracking-tight mb-2">Session Ended</h2>
          <p class="text-neutral-500 mb-8 text-sm">Please leave feedback to help improve the matching quality.</p>

          <!-- Decisions (Recruiter Only) -->
          <div v-if="currentUser?.role === 'recruiter'" class="mb-8">
            <span class="block text-[0.6rem] font-black tracking-[0.2em] text-neutral-500 uppercase mb-4">Official Decision</span>
            <div class="flex gap-4">
              <button @click="interviewDecision = true" class="flex-1 py-4 px-6 rounded-2xl border transition-all font-bold" :class="interviewDecision === true ? 'bg-green-500 border-transparent text-white shadow-lg shadow-green-500/20' : 'bg-green-500/5 border-green-500/20 text-green-500 hover:bg-green-500/10'">
                ✅ Pass
              </button>
              <button @click="interviewDecision = false" class="flex-1 py-4 px-6 rounded-2xl border transition-all font-bold" :class="interviewDecision === false ? 'bg-red-500 border-transparent text-white shadow-lg shadow-red-500/20' : 'bg-red-500/5 border-red-500/20 text-red-500 hover:bg-red-500/10'">
                ❌ Fail
              </button>
            </div>
          </div>

          <!-- Rating -->
          <div class="mb-8">
            <span class="block text-[0.6rem] font-black tracking-[0.2em] text-neutral-500 uppercase mb-4">Session Rating</span>
            <div class="flex justify-between gap-2">
              <button v-for="i in 5" :key="i" @click="reviewRating = i" class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl transition-all" :class="reviewRating >= i ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/30 scale-110' : 'bg-neutral-800 text-neutral-500 hover:bg-neutral-700'">
                ★
              </button>
            </div>
          </div>

          <!-- Notes -->
          <div class="mb-10">
            <span class="block text-[0.6rem] font-black tracking-[0.2em] text-neutral-500 uppercase mb-4">Detailed Feedback</span>
            <textarea v-model="reviewComment" placeholder="Write your thoughts..." class="w-full bg-neutral-900 border border-white/5 rounded-2xl p-5 text-[0.9rem] min-h-[10rem] focus:outline-none focus:border-blue-500 transition-colors"></textarea>
          </div>

          <button
            @click="submitReview"
            :disabled="isSubmittingReview || !reviewComment || (currentUser?.role === 'recruiter' && interviewDecision === null)"
            class="w-full h-16 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 disabled:cursor-not-allowed text-white text-[0.7rem] font-black tracking-[0.2em] uppercase rounded-2xl shadow-2xl shadow-blue-600/20 active:scale-95 transition-all flex items-center justify-center gap-3"
          >
            <span v-if="!isSubmittingReview">Submit Final Report</span>
            <template v-else>
              <svg class="animate-spin h-5 w-5 text-rcurrent" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Processing...
            </template>
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

const router = useRouter();
const route  = useRoute();
const roomId = route.params.id;

const localVideo   = ref(null);
const remoteVideo  = ref(null);
const chatMessages = ref(null);

const currentUser      = ref(null);
const interviewData    = ref(null);

let localStream       = null;
let peerConnection    = null;
let callStarted       = false;
let queuedCandidates  = [];

const hasRemoteStream  = ref(false);
const connectionState  = ref("new");
const isMuted          = ref(false);
const isVideoOff       = ref(false);
const isReady          = ref(false);

const isChatOpen   = ref(false);
const chatDraft    = ref("");
const messages     = ref([]);
const unreadCount  = ref(0);

const showReviewModal    = ref(false);
const reviewRating       = ref(5);
const reviewComment      = ref("");
const isSubmittingReview = ref(false);
const interviewDecision  = ref(null);

const roleLabel = computed(() =>
  currentUser.value?.role === "recruiter" ? "Interviewer" : "Candidate"
);
const peerRoleLabel = computed(() =>
  currentUser.value?.role === "recruiter" ? "Candidate" : "Interviewer"
);

const connStateLabel = computed(() => ({
  new:          "Initializing...",
  connecting:   "Connecting...",
  connected:    "Secured Connection",
  disconnected: "Peer Disconnected",
  failed:       "Connection Err",
  closed:       "Session Closed",
}[connectionState.value] ?? "Ready"));

const statusText = computed(() => {
  if (connectionState.value === "connected") return "Online";
  if (connectionState.value === "failed" || connectionState.value === "disconnected") return "Disconnected";
  if (!isReady.value) return "Awaiting peer...";
  return "Negotiating...";
});

const statusBadgeClassTailwind = computed(() => {
  if (connectionState.value === "connected") return "bg-green-500/10 border-green-500/20 text-green-500";
  if (connectionState.value === "failed")    return "bg-red-500/10 border-red-500/20 text-red-500";
  return "bg-amber-500/10 border-amber-500/20 text-amber-500";
});

onMounted(async () => {
  try {
    const [userRes, interviewRes] = await Promise.all([
      axios.get("/api/user"),
      axios.get(`/api/v1/interviews/${roomId}`),
    ]);
    currentUser.value   = userRes.data;
    interviewData.value = interviewRes.data;
  } catch (err) {
    console.error("Fetch failure (interview likely closed):", err);
    router.push('/');
  }

  // Media initialization
  try {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    if (localVideo.value) localVideo.value.srcObject = localStream;
  } catch (err) {
    console.warn("Video access failed, falling back to audio only:", err);
    try {
      localStream = await navigator.mediaDevices.getUserMedia({ video: false, audio: true });
      isVideoOff.value = true;
      if (localVideo.value) localVideo.value.srcObject = localStream;
    } catch (fallbackErr) {
      console.warn("Media completely blocked:", fallbackErr);
      connectionState.value = "failed";
    }
  }

  // Echo monitoring
  window.Echo.connector.pusher.connection.bind("state_change", ({ current }) => {
    if (current === "connecting")   connectionState.value = "connecting";
    if (current === "unavailable")  connectionState.value = "failed";
  });

  const channel = window.Echo.join(`interview.${roomId}`);

  channel.here((users) => {
    if (users.length > 1) {
      isReady.value = true;
      if (!callStarted && currentUser.value?.role === 'recruiter') startCall(channel);
    }
  })
  .joining(() => {
    isReady.value = true;
    if (!callStarted && currentUser.value?.role === 'recruiter') startCall(channel);
  })
  .leaving(() => {
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

  channel.listenForWhisper("ChatMessage", (data) => {
    messages.value.push({ text: data.text, isMe: false });
    if (!isChatOpen.value) unreadCount.value++;
    scrollChat();
  });

  channel.listenForWhisper("WebRTCSignal", async (data) => {
    if (data.type === "offer" && peerConnection) {
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
        if (peerConnection.remoteDescription) await peerConnection.addIceCandidate(candidate);
        else queuedCandidates.push(candidate);
      }
    } catch (err) { console.error("WebRTC Signaling Error:", err); }
  });

  window.__echoChannel = channel;
});

const rtcConfig = { iceServers: [{ urls: "stun:stun.l.google.com:19302" }] };

function createPeerConnection(channel) {
  peerConnection = new RTCPeerConnection(rtcConfig);
  if (localStream) {
    localStream.getTracks().forEach((track) => peerConnection.addTrack(track, localStream));
  }
  peerConnection.ontrack = (event) => {
    if (event.streams && event.streams[0]) {
      remoteVideo.value.srcObject = event.streams[0];
    } else {
      const inboundStream = new MediaStream();
      inboundStream.addTrack(event.track);
      remoteVideo.value.srcObject = inboundStream;
    }
    hasRemoteStream.value = true;
    nextTick(() => {
      remoteVideo.value?.play().catch(e => console.warn("Autoplay block:", e));
    });
  };
  peerConnection.onconnectionstatechange = () => {
    connectionState.value = peerConnection.connectionState;
    if (['failed', 'disconnected'].includes(peerConnection.connectionState)) hasRemoteStream.value = false;
  };
  peerConnection.onicecandidate = (event) => {
    if (event.candidate) channel.whisper("WebRTCSignal", { type: "candidate", candidate: event.candidate });
  };
}

async function startCall(channel) {
  if (callStarted) return;
  callStarted = true;
  createPeerConnection(channel);
  const offer = await peerConnection.createOffer();
  await peerConnection.setLocalDescription(offer);
  channel.whisper("WebRTCSignal", { type: "offer", sdp: offer });
}

async function drainCandidateQueue() {
  while (queuedCandidates.length > 0) await peerConnection.addIceCandidate(queuedCandidates.shift());
}

function initiateManualCall() { if (window.__echoChannel) startCall(window.__echoChannel); }

function toggleAudio() {
  if (!localStream) return;
  const track = localStream.getAudioTracks()[0];
  if (track) { track.enabled = !track.enabled; isMuted.value = !track.enabled; }
}

function toggleVideo() {
  if (!localStream) return;
  const track = localStream.getVideoTracks()[0];
  if (track) { track.enabled = !track.enabled; isVideoOff.value = !track.enabled; }
}

function toggleChat() { isChatOpen.value = !isChatOpen.value; if (isChatOpen.value) unreadCount.value = 0; }
function closeChat() { isChatOpen.value = false; }
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
  if (chatMessages.value) chatMessages.value.scrollTop = chatMessages.value.scrollHeight;
}

function handleLeave() {
  if (window.__echoChannel) window.__echoChannel.whisper("WebRTCSignal", { type: "peer-left" });
  if (peerConnection) peerConnection.close();
  if (localStream) localStream.getTracks().forEach((t) => t.stop());
  window.Echo.leave(`interview.${roomId}`);
  showReviewModal.value = true;
}

async function submitReview() {
  if (isSubmittingReview.value) return;
  
  if (!currentUser.value || !interviewData.value) {
    console.warn("State lost during submission. Redirecting...");
    router.push('/');
    return;
  }

  isSubmittingReview.value = true;
  try {
    const isRecruiter = currentUser.value.role === "recruiter";
    const reviewedUserId = isRecruiter
      ? interviewData.value.application?.student_id
      : interviewData.value.application?.project?.recruiter_id;

    if (!reviewedUserId) throw new Error("Could not determine peer ID");

    const promises = [
      axios.post("/api/v1/reviews", {
        reviewed_user_id: reviewedUserId,
        rating:           reviewRating.value,
        comment:          reviewComment.value,
      }),
    ];

    if (isRecruiter) {
      promises.push(
        axios.post(`/api/v1/interviews/${roomId}/result`, {
          passed: interviewDecision.value,
        })
      );
    }

    await Promise.all(promises);
    
    // Redirect to the correct role-based dashboard
    const dest = isRecruiter ? '/dashboard/recruiter' : '/dashboard/applicant';
    router.push(dest);
  } catch (err) {
    console.error("Submit failure:", err);
    router.push('/');
  } finally {
    isSubmittingReview.value = false;
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=Syne:wght@400;500;600;700;800&display=swap');

.font-syne { font-family: 'Syne', sans-serif; }

.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 20px; }

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.95); }

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.5s cubic-bezier(0.22, 1, 0.36, 1), transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}
.modal-enter-from { opacity: 0; transform: translateY(30px); }
.modal-leave-to { opacity: 0; transform: scale(0.95); }
</style>
