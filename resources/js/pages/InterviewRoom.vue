

<template>
  <div class="min-h-screen bg-slate-950 text-white flex flex-col font-sans">
    <!-- Header -->
    <header class="h-16 border-b border-white/10 flex items-center justify-between px-6 bg-slate-900/50 backdrop-blur-xl">
      <div class="flex items-center gap-4">
        <div class="bg-blue-600 p-2 rounded-lg">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 10l5-5v14l-5-5v-4z"/><rect x="2" y="3" width="11" height="18" rx="2" ry="2"/></svg>
        </div>
        <div>
          <h1 class="text-sm font-black uppercase tracking-widest text-white/90">Interview Room</h1>
          <p class="text-[0.65rem] text-blue-400 font-bold uppercase tracking-tighter">Live Session #{{ roomId }}</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="px-3 py-1 bg-red-500/10 border border-red-500/20 rounded-full flex items-center gap-2">
          <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
          <span class="text-[0.65rem] font-black uppercase tracking-widest text-red-500">Live Recording</span>
        </div>
      </div>
    </header>

    <!-- Video Grid -->
    <main class="flex-1 p-6 flex items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-slate-900 to-slate-950">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-6xl h-full max-h-[700px]">
        <!-- Local Participant -->
        <div class="relative bg-slate-900 rounded-3xl border border-white/5 overflow-hidden shadow-2xl group">
          <video ref="localVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
          <div class="absolute bottom-6 left-6 flex items-center gap-3">
            <div class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-2xl flex items-center gap-2 border border-white/10">
              <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
              <span class="text-xs font-bold uppercase tracking-widest">You (Candidate)</span>
            </div>
          </div>
          <div v-if="!isCamOn" class="absolute inset-0 bg-slate-900 flex flex-center items-center justify-center flex-col gap-4">
            <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center text-slate-500">
               <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Camera is off</p>
          </div>
        </div>

        <!-- Remote Participant -->
        <div class="relative bg-slate-900 rounded-3xl border border-white/5 overflow-hidden shadow-2xl group">
          <video ref="remoteVideo" autoplay playsinline class="w-full h-full object-cover bg-slate-800"></video>
          <div class="absolute bottom-6 left-6 flex items-center gap-3">
            <div class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-2xl flex items-center gap-2 border border-white/10">
              <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
              <span class="text-xs font-bold uppercase tracking-widest">Recruiter (Interviewer)</span>
            </div>
          </div>
          <!-- Placeholder if no remote stream -->
          <div v-if="!hasRemoteStream" class="absolute inset-0 bg-slate-900 flex items-center justify-center flex-col gap-4">
             <div class="animate-pulse flex flex-col items-center gap-4">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center text-slate-600">
                  <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <p class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-500">Waiting for peer connection...</p>
             </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Controls -->
    <footer class="h-24 bg-slate-950/80 backdrop-blur-3xl border-t border-white/5 flex items-center justify-center relative px-6">
      <div class="flex items-center gap-4">
        <button 
          @click="toggleMic" 
          class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 shadow-xl"
          :class="isMicOn ? 'bg-slate-800 hover:bg-slate-700 text-white' : 'bg-red-500 text-white shadow-red-500/20'"
        >
          <svg v-if="isMicOn" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
          <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/><path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"/><path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
        </button>

        <button 
          @click="toggleCam" 
          class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 shadow-xl"
          :class="isCamOn ? 'bg-slate-800 hover:bg-slate-700 text-white' : 'bg-red-500 text-white shadow-red-500/20'"
        >
          <svg v-if="isCamOn" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
          <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </button>

        <div class="w-px h-10 bg-white/10 mx-2"></div>

        <button 
          @click="leaveCall" 
          class="bg-red-500 hover:bg-red-600 text-white px-8 h-14 rounded-3xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-red-500/20 flex items-center gap-3"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
          Leave Call
        </button>
      </div>

      <div class="absolute right-10 flex items-center gap-6">
         <div class="flex flex-col items-end">
            <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-500">Quality</span>
            <span class="text-xs font-bold text-green-500 uppercase tracking-tight">HD Optimized</span>
         </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const roomId = ref(Math.floor(Math.random() * 900000) + 100000);

const localVideo = ref(null);
const remoteVideo = ref(null);

const isMicOn = ref(true);
const isCamOn = ref(true);
const hasRemoteStream = ref(false);

let localStream = null;
let peerConnection = null;

const rtcConfig = {
  iceServers: [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' }
  ]
};

async function initMedia() {
  try {
    localStream = await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: true
    });
    if (localVideo.value) {
      localVideo.value.srcObject = localStream;
    }
  } catch (err) {
    console.error("Media Init Error:", err);
  }
}

function createPeerConnection() {
  if (!localStream) return;
  
  peerConnection = new RTCPeerConnection(rtcConfig);

  localStream.getTracks().forEach(track => {
    peerConnection.addTrack(track, localStream);
  });

  peerConnection.ontrack = (event) => {
    if (remoteVideo.value) {
      remoteVideo.value.srcObject = event.streams[0];
      hasRemoteStream.value = true;
    }
  };

  peerConnection.onicecandidate = (event) => {
    if (event.candidate) {
      // Signaling logic would go here
    }
  };
}

function toggleMic() {
  if (!localStream) return;
  isMicOn.value = !isMicOn.value;
  localStream.getAudioTracks().forEach(track => track.enabled = isMicOn.value);
}

function toggleCam() {
  if (!localStream) return;
  isCamOn.value = !isCamOn.value;
  localStream.getVideoTracks().forEach(track => track.enabled = isCamOn.value);
}

function leaveCall() {
  if (localStream) {
    localStream.getTracks().forEach(track => track.stop());
  }
  if (peerConnection) {
    peerConnection.close();
  }
  router.push('/dashboard/applicant');
}

onMounted(async () => {
  await initMedia();
  createPeerConnection();
});

onUnmounted(() => {
  if (localStream) {
    localStream.getTracks().forEach(track => track.stop());
  }
});
</script>

<style scoped>
/* Animated background glow effects would go here */
</style>