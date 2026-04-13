<template>
    <div class="min-h-screen bg-slate-950 text-white flex flex-col font-sans selection:bg-blue-500/30">
        <!-- Header -->
        <header class="h-16 border-b border-white/5 flex items-center justify-between px-8 bg-slate-900/40 backdrop-blur-2xl sticky top-0 z-40">
            <div class="flex items-center gap-5">
                <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M15 10l5-5v14l-5-5v-4z" />
                        <rect x="2" y="3" width="11" height="18" rx="3" ry="3" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-black uppercase tracking-[0.2em] text-white/90 leading-none mb-1">
                        Interview Room
                    </h1>
                    <div class="flex items-center gap-2">
                        <span class="text-[0.6rem] text-blue-400 font-bold uppercase tracking-widest">Live Session #{{ roomId }}</span>
                        <span class="w-1 h-1 bg-white/20 rounded-full"></span>
                        <span class="text-[0.6rem] text-white/40 font-medium transition-colors" :class="connectionStateClass">
                            {{ connectionStateLabel }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-[0.55rem] font-black uppercase tracking-widest text-slate-500 mb-0.5">Participant Status</span>
                    <div class="flex items-center gap-2">
                         <div v-if="isReady" class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-[0.65rem] font-bold text-green-500/80 uppercase">Peer Online</span>
                        </div>
                        <div v-else class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 bg-slate-600 rounded-full"></div>
                            <span class="text-[0.65rem] font-bold text-slate-500 uppercase">Waiting...</span>
                        </div>
                    </div>
                </div>
                
                <div class="h-8 w-px bg-white/10"></div>

                <div class="px-3.5 py-1.5 bg-red-500/10 border border-red-500/20 rounded-full flex items-center gap-2.5">
                    <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                    <span class="text-[0.65rem] font-black uppercase tracking-[0.1em] text-red-500">Live Recording</span>
                </div>
            </div>
        </header>

        <!-- Video Grid -->
        <main class="flex-1 p-6 md:p-10 flex items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_50%_50%,_rgba(30,41,59,0.5)_0%,_rgba(2,6,23,1)_100%)]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 w-full max-w-7xl h-full max-h-[720px]">
                
                <!-- Local Participant -->
                <div class="relative bg-slate-900/50 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl group transition-all duration-500 hover:border-blue-500/20">
                    <video
                        ref="localVideo"
                        autoplay
                        playsinline
                        muted
                        class="w-full h-full object-cover mirror-mode transition-transform duration-700 group-hover:scale-[1.02]"
                    ></video>
                    
                    <!-- Overlay Info -->
                    <div class="absolute bottom-6 left-6 flex flex-col gap-2">
                        <div class="bg-black/60 backdrop-blur-xl px-4 py-2 rounded-2xl flex items-center gap-2.5 border border-white/10 shadow-xl">
                            <div class="w-2 h-2 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                            <span class="text-[0.7rem] font-black uppercase tracking-widest">
                                You ({{ currentUser?.role === "recruiter" ? "Interviewer" : "Candidate" }})
                            </span>
                        </div>
                    </div>

                    <!-- Camera Off State -->
                    <transition name="scale-fade">
                        <div v-if="isVideoOff" class="absolute inset-0 bg-slate-900 flex flex-col items-center justify-center gap-6 z-10">
                            <div class="w-24 h-24 bg-slate-800/50 rounded-full flex items-center justify-center text-slate-500 border border-white/5 shadow-inner">
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </div>
                            <p class="text-[0.7rem] font-black uppercase tracking-[0.3em] text-slate-500">Camera is off</p>
                        </div>
                    </transition>
                </div>

                <!-- Remote Participant -->
                <div class="relative bg-slate-900/50 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl group transition-all duration-500 hover:border-indigo-500/20">
                    <video
                        ref="remoteVideo"
                        autoplay
                        playsinline
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]"
                    ></video>
                    
                    <!-- Overlay Info -->
                    <div class="absolute bottom-6 left-6 flex flex-col gap-2">
                        <div class="bg-black/60 backdrop-blur-xl px-4 py-2 rounded-2xl flex items-center gap-2.5 border border-white/10 shadow-xl">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.6)]"></div>
                            <span class="text-[0.7rem] font-black uppercase tracking-widest">
                                {{ currentUser?.role === "recruiter" ? "Candidate" : "Interviewer" }}
                            </span>
                        </div>
                    </div>

                    <!-- Placeholder if no remote stream -->
                    <transition name="scale-fade">
                        <div v-if="!remoteStream" class="absolute inset-0 bg-slate-900 flex items-center justify-center z-10">
                            <div class="flex flex-col items-center gap-6">
                                <div class="w-24 h-24 bg-slate-800/50 rounded-full flex items-center justify-center text-slate-600 border border-white/5 relative">
                                    <div class="absolute inset-0 rounded-full border border-blue-500/20 animate-ping"></div>
                                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-[0.7rem] font-black uppercase tracking-[0.3em] text-slate-500 mb-2">
                                        {{ isReady ? 'Establishing Connection...' : 'Awaiting Participant...' }}
                                    </p>
                                    <p class="text-[0.6rem] text-slate-600 uppercase tracking-widest">Secure peer-to-peer tunnel</p>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </main>

        <!-- Controls -->
        <footer class="h-28 bg-slate-950/80 backdrop-blur-3xl border-t border-white/5 flex items-center justify-center relative px-8">
            <div class="flex items-center gap-6">
                <!-- Audio Toggle -->
                <button
                    @click="toggleAudio"
                    class="group relative w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 overflow-hidden shadow-lg"
                    :class="!isMuted ? 'bg-slate-800 hover:bg-slate-700 hover:scale-110' : 'bg-red-500/90 hover:bg-red-500 hover:scale-110 shadow-red-500/20'"
                    title="Toggle Microphone"
                >
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg v-if="!isMuted" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z" />
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
                        <line x1="12" y1="19" x2="12" y2="23" />
                        <line x1="8" y1="23" x2="16" y2="23" />
                    </svg>
                    <svg v-else class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="1" y1="1" x2="23" y2="23" />
                        <path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6" />
                        <path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23" />
                        <line x1="12" y1="19" x2="12" y2="23" />
                    </svg>
                </button>

                <!-- Video Toggle -->
                <button
                    @click="toggleVideo"
                    class="group relative w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 overflow-hidden shadow-lg"
                    :class="!isVideoOff ? 'bg-slate-800 hover:bg-slate-700 hover:scale-110' : 'bg-red-500/90 hover:bg-red-500 hover:scale-110 shadow-red-500/20'"
                    title="Toggle Camera"
                >
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg v-if="!isVideoOff" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 7l-7 5 7 5V7z" />
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                    </svg>
                    <svg v-else class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>

                <div class="w-px h-10 bg-white/10 mx-2"></div>

                <!-- Call Actions -->
                <button
                    v-if="!remoteStream && isReady"
                    @click="initiateCall"
                    class="group relative bg-blue-600 hover:bg-blue-500 text-white px-10 h-14 rounded-2xl font-black uppercase text-[0.7rem] tracking-[0.2em] transition-all shadow-xl shadow-blue-600/20 active:scale-95 overflow-hidden"
                >
                    <span class="relative z-10 flex items-center gap-3">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20,15.5C18.8,15.5 17.5,15.3 16.4,14.9C16.3,14.9 16.2,14.9 16.1,14.9C15.8,14.9 15.6,15 15.4,15.2L13.2,17.4C10.4,15.9 8.1,13.6 6.6,10.8L8.8,8.6C9,8.4 9.1,8.2 9.1,7.9C9.1,7.8 9,7.7 9,7.6C8.6,6.5 8.4,5.2 8.4,4C8.4,3.4 8,3 7.5,3H4C3.4,3 3,3.4 3,4C3,13.4 10.6,21 20,21C20.6,21 21,20.6 21,20V16.5C21,16 20.6,15.5 20,15.5Z"/></svg>
                        Start Encounter
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/20 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                </button>

                <button
                    @click="handleLeave"
                    class="group relative bg-slate-900 border border-red-500/30 hover:bg-red-500 text-red-500 hover:text-white px-10 h-14 rounded-2xl font-black uppercase text-[0.7rem] tracking-[0.2em] transition-all shadow-xl active:scale-95 overflow-hidden"
                >
                    <span class="relative z-10 flex items-center gap-3">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18.36 6.64a9 9 0 1 1-12.73 0" /><line x1="12" y1="2" x2="12" y2="12" /></svg>
                        Leave Area
                    </span>
                </button>
            </div>

            <!-- Quality Stats -->
            <div class="absolute right-10 hidden xl:flex items-center gap-8">
                <div class="flex flex-col items-end">
                    <span class="text-[0.55rem] font-black uppercase tracking-[0.2em] text-slate-500 mb-0.5">Stream Quality</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[0.65rem] font-bold text-green-500 uppercase tracking-tight">4K Ultra HD</span>
                        <div class="flex gap-0.5">
                            <div class="w-1 h-2 bg-green-500 rounded-sm"></div>
                            <div class="w-1 h-3 bg-green-500 rounded-sm"></div>
                            <div class="w-1 h-4 bg-green-500 rounded-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Review System Modal -->
        <transition name="modal">
            <div v-if="showReviewModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-xl transition-opacity"></div>
                
                <!-- Content -->
                <div class="relative bg-slate-900 border border-white/10 rounded-[2rem] p-10 w-full max-w-lg shadow-[0_32px_64px_rgba(0,0,0,0.5)] overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[100px] -translate-y-1/2 translate-x-1/2 rounded-full"></div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 mb-8">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h2 class="text-3xl font-black uppercase tracking-tight mb-3">Interview Ended</h2>
                        <p class="text-slate-400 text-sm leading-relaxed mb-10">
                            Your session has concluded. Please provide feedback on the performance and professional conduct of the other participant.
                        </p>

                        <div class="space-y-8">
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 mb-4">Quality Score</label>
                                <div class="flex gap-3">
                                    <button
                                        v-for="i in 5"
                                        :key="i"
                                        @click="reviewRating = i"
                                        class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300"
                                        :class="reviewRating >= i ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20 scale-110' : 'bg-slate-800 text-slate-600 hover:bg-slate-700'"
                                    >
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 mb-4">Performance Notes</label>
                                <textarea
                                    v-model="reviewComment"
                                    class="w-full bg-slate-800/50 border border-white/5 rounded-2xl p-5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 transition-all resize-none min-h-[120px]"
                                    placeholder="Briefly describe the candidate's skills or recruiter's clarity..."
                                ></textarea>
                            </div>

                            <button
                                @click="submitReview"
                                :disabled="isSubmittingReview || !reviewComment"
                                class="w-full h-16 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 disabled:cursor-not-allowed text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] transition-all shadow-xl shadow-blue-600/20 active:scale-[0.98]"
                            >
                                <span v-if="!isSubmittingReview">Submit Final Report</span>
                                <span v-else class="flex items-center justify-center gap-3">
                                    <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useWebRTC } from "../composables/useWebRTC";
import { useSignaling } from "../composables/useSignaling";

// State
const router = useRouter();
const route = useRoute();
const roomId = route.params.id;

// UI Elements
const localVideo = ref(null);
const remoteVideo = ref(null);
const currentUser = ref(null);
const interviewData = ref(null);

// Hooks
const {
    localStream,
    remoteStream,
    connectionState,
    isMuted,
    isVideoOff,
    startLocalStream,
    createPeerConnection,
    createOffer,
    handleOffer,
    handleAnswer,
    handleIceCandidate,
    toggleAudio,
    toggleVideo,
    closeConnection
} = useWebRTC();

const {
    init: initSignaling,
    isReady,
    sendSignal,
    onSignal,
    leave: leaveSignaling
} = useSignaling(roomId);

// Review System State
const showReviewModal = ref(false);
const reviewRating = ref(5);
const reviewComment = ref("");
const isSubmittingReview = ref(false);

// Computed
const connectionStateLabel = computed(() => {
    switch (connectionState.value) {
        case 'new': return 'Initializing...';
        case 'connecting': return 'Connecting...';
        case 'connected': return 'Encrypted Connection';
        case 'disconnected': return 'Peer Disconnected';
        case 'failed': return 'Connection Error';
        case 'closed': return 'Session Closed';
        default: return 'Ready to Link';
    }
});

const connectionStateClass = computed(() => {
    if (connectionState.value === 'connected') return 'text-green-500';
    if (connectionState.value === 'failed') return 'text-red-500';
    return 'text-white/40';
});

// Initialization
onMounted(async () => {
    try {
        // 1. Fetch Auth & Interview Data
        const [userRes, interviewRes] = await Promise.all([
            axios.get("/api/user"),
            axios.get(`/api/v1/interviews/${roomId}`),
        ]);
        currentUser.value = userRes.data;
        interviewData.value = interviewRes.data;
        
        // 2. Init Media
        const stream = await startLocalStream();
        if (localVideo.value) {
            localVideo.value.srcObject = stream;
        }

        // 3. Init Signaling
        initSignaling();
        setupSignalingHandlers();

    } catch (err) {
        console.error("Initialization Failed:", err);
    }
});

// Signaling Logic
function setupSignalingHandlers() {
    onSignal("offer", async (offer) => {
        console.log("Received Offer");
        createPeerConnection(
            (candidate) => sendSignal("ice-candidate", candidate),
            (stream) => { if (remoteVideo.value) remoteVideo.value.srcObject = stream; }
        );
        const answer = await handleOffer(offer);
        sendSignal("answer", answer);
    });

    onSignal("answer", async (answer) => {
        console.log("Received Answer");
        await handleAnswer(answer);
    });

    onSignal("ice-candidate", async (candidate) => {
        console.log("Received ICE Candidate");
        await handleIceCandidate(candidate);
    });

    onSignal("peer-left", () => {
        console.log("Peer left call");
        handleEndCall();
    });
}

const initiateCall = async () => {
    console.log("Initiating Call...");
    createPeerConnection(
        (candidate) => sendSignal("ice-candidate", candidate),
        (stream) => { if (remoteVideo.value) remoteVideo.value.srcObject = stream; }
    );
    const offer = await createOffer();
    sendSignal("offer", offer);
};

// Control Actions
const handleLeave = () => {
    sendSignal("peer-left", {});
    handleEndCall();
};

const handleEndCall = () => {
    closeConnection();
    leaveSignaling();
    showReviewModal.value = true;
};

// Review Submission
async function submitReview() {
    isSubmittingReview.value = true;
    try {
        const reviewedUserId = currentUser.value.role === "recruiter"
            ? interviewData.value.application.student_id
            : interviewData.value.application.project.recruiter_id;

        await axios.post("/api/v1/reviews", {
            reviewed_user_id: reviewedUserId,
            rating: reviewRating.value,
            comment: reviewComment.value,
        });

        router.push("/dashboard");
    } catch (err) {
        console.error("Failed to submit review:", err);
        router.push("/dashboard");
    } finally {
        isSubmittingReview.value = false;
    }
}

// Watch for remote stream updates
watch(remoteStream, (newStream) => {
    if (newStream && remoteVideo.value) {
        remoteVideo.value.srcObject = newStream;
    }
});
</script>

<style scoped>
.mirror-mode {
    transform: scaleX(-1);
}

/* Animations */
.scale-fade-enter-active,
.scale-fade-leave-active {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}
.scale-fade-enter-from,
.scale-fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}
.modal-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
