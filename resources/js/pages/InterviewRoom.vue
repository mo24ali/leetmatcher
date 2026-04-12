<template>
    <div class="min-h-screen bg-slate-950 text-white flex flex-col font-sans">
        <!-- Header -->
        <header
            class="h-16 border-b border-white/10 flex items-center justify-between px-6 bg-slate-900/50 backdrop-blur-xl"
        >
            <div class="flex items-center gap-4">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <svg
                        class="w-5 h-5 text-white"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path d="M15 10l5-5v14l-5-5v-4z" />
                        <rect
                            x="2"
                            y="3"
                            width="11"
                            height="18"
                            rx="2"
                            ry="2"
                        />
                    </svg>
                </div>
                <div>
                    <h1
                        class="text-sm font-black uppercase tracking-widest text-white/90"
                    >
                        Interview Room
                    </h1>
                    <p
                        class="text-[0.65rem] text-blue-400 font-bold uppercase tracking-tighter"
                    >
                        Live Session #{{ roomId }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Requirements: Header Navigation -->
                <button 
                    @click="goToBlog"
                    class="px-4 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-full text-[0.65rem] font-black uppercase tracking-widest transition-all mr-4"
                >
                    blogPost
                </button>
                <div
                    class="px-3 py-1 bg-red-500/10 border border-red-500/20 rounded-full flex items-center gap-2"
                >
                    <div
                        class="w-2 h-2 bg-red-500 rounded-full animate-pulse"
                    ></div>
                    <span
                        class="text-[0.65rem] font-black uppercase tracking-widest text-red-500"
                        >Live Recording</span
                    >
                </div>
            </div>
        </header>

        <!-- Video Grid -->
        <main
            class="flex-1 p-6 flex items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-slate-900 to-slate-950"
        >
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-6xl h-full max-h-[700px]"
            >
                <!-- Local Participant -->
                <div
                    class="relative bg-slate-900 rounded-3xl border border-white/5 overflow-hidden shadow-2xl group"
                >
                    <video
                        ref="localVideo"
                        autoplay
                        playsinline
                        muted
                        class="w-full h-full object-cover"
                    ></video>
                    <div
                        class="absolute bottom-6 left-6 flex items-center gap-3"
                    >
                        <div
                            class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-2xl flex items-center gap-2 border border-white/10"
                        >
                            <span
                                class="w-2 h-2 bg-blue-500 rounded-full"
                            ></span>
                            <span
                                class="text-xs font-bold uppercase tracking-widest"
                                >You ({{ currentUser?.role === 'recruiter' ? 'Interviewer' : 'Candidate' }})</span
                            >
                        </div>
                    </div>
                    <div
                        v-if="!isCamOn"
                        class="absolute inset-0 bg-slate-900 flex flex-center items-center justify-center flex-col gap-4"
                    >
                        <div
                            class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center text-slate-500"
                        >
                            <svg
                                class="w-10 h-10"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10"
                                />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-slate-500"
                        >
                            Camera is off
                        </p>
                    </div>
                </div>

                <!-- Remote Participant -->
                <div
                    class="relative bg-slate-900 rounded-3xl border border-white/5 overflow-hidden shadow-2xl group"
                >
                    <video
                        ref="remoteVideo"
                        autoplay
                        playsinline
                        class="w-full h-full object-cover bg-slate-800"
                    ></video>
                    <div
                        class="absolute bottom-6 left-6 flex items-center gap-3"
                    >
                        <div
                            class="bg-black/40 backdrop-blur-md px-4 py-2 rounded-2xl flex items-center gap-2 border border-white/10"
                        >
                            <span
                                class="w-2 h-2 bg-indigo-500 rounded-full"
                            ></span>
                            <span
                                class="text-xs font-bold uppercase tracking-widest"
                                >{{ currentUser?.role === 'recruiter' ? 'Candidate' : 'Recruiter' }}</span
                            >
                        </div>
                    </div>
                    <!-- Placeholder if no remote stream -->
                    <div
                        v-if="!hasRemoteStream"
                        class="absolute inset-0 bg-slate-900 flex items-center justify-center flex-col gap-4"
                    >
                        <div
                            class="animate-pulse flex flex-col items-center gap-4"
                        >
                            <div
                                class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center text-slate-600"
                            >
                                <svg
                                    class="w-10 h-10"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                    />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <p
                                class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-500"
                            >
                                Waiting for peer connection...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Controls -->
        <footer
            class="h-24 bg-slate-950/80 backdrop-blur-3xl border-t border-white/5 flex items-center justify-center relative px-6"
        >
            <div class="flex items-center gap-4">
                <button
                    @click="toggleMic"
                    class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 shadow-xl"
                    :class="
                        isMicOn
                            ? 'bg-slate-800 hover:bg-slate-700 text-white'
                            : 'bg-red-500 text-white shadow-red-500/20'
                    "
                >
                    <svg
                        v-if="isMicOn"
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"
                        />
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
                        <line x1="12" y1="19" x2="12" y2="23" />
                        <line x1="8" y1="23" x2="16" y2="23" />
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="1" y1="1" x2="23" y2="23" />
                        <path
                            d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"
                        />
                        <path
                            d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"
                        />
                        <line x1="12" y1="19" x2="12" y2="23" />
                        <line x1="8" y1="23" x2="16" y2="23" />
                    </svg>
                </button>

                <button
                    @click="toggleCam"
                    class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 shadow-xl"
                    :class="
                        isCamOn
                            ? 'bg-slate-800 hover:bg-slate-700 text-white'
                            : 'bg-red-500 text-white shadow-red-500/20'
                    "
                >
                    <svg
                        v-if="isCamOn"
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M23 7l-7 5 7 5V7z" />
                        <rect
                            x="1"
                            y="5"
                            width="15"
                            height="14"
                            rx="2"
                            ry="2"
                        />
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10"
                        />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>

                <div class="w-px h-10 bg-white/10 mx-2"></div>

                <button
                    v-if="!callStarted"
                    @click="startCall()"
                    class="bg-green-500 hover:bg-green-600 text-white px-8 h-14 rounded-3xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-green-500/20 flex items-center gap-3"
                >
                    Start the Call
                </button>
                <button
                    @click="leaveCall"
                    class="bg-red-500 hover:bg-red-600 text-white px-8 h-14 rounded-3xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-red-500/20 flex items-center gap-3"
                >
                    <svg
                        class="w-4 h-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                        <line x1="12" y1="2" x2="12" y2="12" />
                    </svg>
                    Leave Call
                </button>
            </div>

            <div class="absolute right-10 flex items-center gap-6">
                <div class="flex flex-col items-end">
                    <span
                        class="text-[0.6rem] font-black uppercase tracking-widest text-slate-500"
                        >Quality</span
                    >
                    <span
                        class="text-xs font-bold text-green-500 uppercase tracking-tight"
                        >HD Optimized</span
                    >
                </div>
            </div>
        </footer>

        <!-- Review System Modal -->
        <transition name="fade">
            <div
                v-if="showReviewModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-md"
            >
                <div class="bg-slate-900 border border-white/10 rounded-3xl p-8 w-full max-w-md shadow-2xl">
                    <h2 class="text-2xl font-black uppercase tracking-widest mb-2">Interview Completed</h2>
                    <p class="text-slate-400 text-sm mb-6">Please leave a review for your peer based on today's session.</p>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[0.65rem] font-black uppercase tracking-widest text-slate-500 mb-3">Rating</label>
                            <div class="flex gap-2">
                                <button 
                                    v-for="i in 5" 
                                    :key="i"
                                    @click="reviewRating = i"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all"
                                    :class="reviewRating >= i ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-500 hover:bg-slate-700'"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[0.65rem] font-black uppercase tracking-widest text-slate-500 mb-3">Comments</label>
                            <textarea
                                v-model="reviewComment"
                                class="w-full bg-slate-800 border border-white/5 rounded-2xl p-4 text-sm focus:outline-none focus:border-blue-500 transition-all resize-none"
                                rows="4"
                                placeholder="Share your experience..."
                            ></textarea>
                        </div>

                        <button
                            @click="submitReview"
                            :disabled="isSubmittingReview"
                            class="w-full h-14 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-blue-600/20"
                        >
                            {{ isSubmittingReview ? 'Submitting...' : 'Submit Review' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

// State Management
const router = useRouter();
const route = useRoute();
const roomId = ref(route.params.id);

const localVideo = ref(null);
const remoteVideo = ref(null);

const isMicOn = ref(true);
const isCamOn = ref(true);
const hasRemoteStream = ref(false);
const callStarted = ref(false);

const currentUser = ref(null);
const interviewData = ref(null);

const showReviewModal = ref(false);
const reviewRating = ref(5);
const reviewComment = ref("");
const isSubmittingReview = ref(false);

// WebRTC & Signaling Variables
let localStream = null;
let peerConnection = null;
let channel = null;

const rtcConfig = {
    iceServers: [
        { urls: "stun:stun.l.google.com:19302" },
        { urls: "stun:stun1.l.google.com:19302" },
    ],
};

/**
 * [initMedia]
 * Purpose: Initializes the user's camera and microphone.
 * Flow: Requests media access from the browser and attaches the resulting stream to the local video element.
 */
async function initMedia() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true,
        });
        if (localVideo.value) {
            localVideo.value.srcObject = localStream;
        }
    } catch (err) {
        console.error("Failed to get local stream:", err);
    }
}

/**
 * [createPeerConnection]
 * Purpose: Sets up the core RTCPeerConnection object for P2P communication.
 * Flow: Initializes the connection, attaches local media tracks, and sets up listeners for remote tracks and ICE candidates.
 */
function createPeerConnection() {
    peerConnection = new RTCPeerConnection(rtcConfig);

    // Add local tracks to the connection to be transmitted to the peer
    if (localStream) {
        localStream.getTracks().forEach((track) => {
            peerConnection.addTrack(track, localStream);
        });
    }

    // [ontrack] - Triggered when the remote peer adds their tracks to the connection
    peerConnection.ontrack = (event) => {
        if (remoteVideo.value) {
            remoteVideo.value.srcObject = event.streams[0];
            hasRemoteStream.value = true;
        }
    };

    /**
     * [onicecandidate]
     * Purpose: Handles the generation of Interactive Connectivity Establishment (ICE) candidates.
     * Flow: Browsers exchange candidates to find the best network path for the P2P connection. 
     * These candidates are "whispered" to the peer through the signaling channel.
     */
    peerConnection.onicecandidate = (event) => {
        if (event.candidate && channel) {
            channel.whisper("ice-candidate", event.candidate);
        }
    };

    // Monitor connection state to detect when the session ends
    peerConnection.onconnectionstatechange = () => {
        if (peerConnection.connectionState === 'disconnected' || peerConnection.connectionState === 'closed' || peerConnection.connectionState === 'failed') {
            endCall();
        }
    };
}

/**
 * [startCall]
 * Purpose: Initiates the WebRTC handshake as the 'offerer'.
 * Flow:
 * 1. Create an SDP (Session Description Protocol) offer.
 * 2. Set the offer as the local description.
 * 3. Send the offer to the peer via the signaling channel (Echo whisper).
 */
async function startCall() {
    if (!peerConnection) createPeerConnection();
    
    callStarted.value = true;
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    
    // Protocol Flow: Send offer to remote peer
    channel.whisper("offer", offer);
}

/**
 * [listenSignaling]
 * Purpose: Defines the behavior for handling incoming signaling messages from the remote peer.
 * Protocol Flow:
 * - Handling 'offer': If we receive an offer, we set it as the remote description and create an 'answer'.
 * - Handling 'answer': If we sent an offer and get an answer, we set it as the remote description to complete the handshake.
 * - Handling 'ice-candidate': Add remote network nodes to the connection as they are discovered.
 */
function listenSignaling() {
    // 1. [Handle Offer] - The remote peer wants to start a call
    channel.listenForWhisper("offer", async (offer) => {
        if (!peerConnection) createPeerConnection();
        
        await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        
        // Protocol Flow: Send answer back to the peer that sent the offer
        channel.whisper("answer", answer);
    });

    // 2. [Handle Answer] - The remote peer accepted our offer
    channel.listenForWhisper("answer", async (answer) => {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    });

    // 3. [Handle ICE Candidate] - New network paths discovered by the peer
    channel.listenForWhisper("ice-candidate", async (candidate) => {
        try {
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (e) {
            console.error("Error adding ice candidate:", e);
        }
    });

    // 4. [Handle Peer Departure] - The other participant manually left the call
    channel.listenForWhisper("peer-left", () => {
        endCall();
    });
}

/**
 * Mic/Cam UI Toggles
 */
function toggleMic() {
    if (!localStream) return;
    isMicOn.value = !isMicOn.value;
    localStream.getAudioTracks().forEach((track) => (track.enabled = isMicOn.value));
}

function toggleCam() {
    if (!localStream) return;
    isCamOn.value = !isCamOn.value;
    localStream.getVideoTracks().forEach((track) => (track.enabled = isCamOn.value));
}

function goToBlog() {
    router.push("/blog");
}

/**
 * [leaveCall]
 * Purpose: Manually initiates the termination of the session.
 * Flow: Informs the peer that we are leaving, then cleans up local resources.
 */
function leaveCall() {
    if (channel) {
        channel.whisper("peer-left", {});
    }
    endCall();
}

/**
 * [endCall]
 * Purpose: Internal cleanup and state transition to the review system.
 * Flow: Closes connections, stops media tracks, and displays the Review UI.
 */
function endCall() {
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    if (localStream) {
        localStream.getTracks().forEach((track) => track.stop());
        localStream = null;
    }
    
    // Automatically trigger Review System upon interview termination
    showReviewModal.value = true;
}

/**
 * [submitReview]
 * Purpose: Transmits review data (rating and comments) to the backend.
 * Flow: Identifies the other participant and posts the review to the API.
 */
async function submitReview() {
    if (!reviewComment.value) return;
    
    isSubmittingReview.value = true;
    try {
        const reviewedUserId = currentUser.value.role === 'recruiter' 
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
        alert("Could not submit review. Returning to dashboard.");
        router.push("/dashboard");
    } finally {
        isSubmittingReview.value = false;
    }
}

/**
 * Mounting logic: Data fetching, media init, and signaling setup.
 */
onMounted(async () => {
    try {
        // Fetch necessary context: current user and interview details
        const [userRes, interviewRes] = await Promise.all([
            axios.get("/api/user"),
            axios.get(`/api/v1/interviews/${roomId.value}`)
        ]);
        currentUser.value = userRes.data;
        interviewData.value = interviewRes.data;
    } catch (err) {
        console.error("Initialization Failed:", err);
    }

    // Initialize local media
    await initMedia();

    // Connect to the private signaling channel via Laravel Echo
    if (window.Echo) {
        channel = window.Echo.private(`interview.${roomId.value}`);
        listenSignaling();
    } else {
        console.warn("Echo not initialized. Signaling will be unavailable.");
    }
});

onUnmounted(() => {
    if (localStream) {
        localStream.getTracks().forEach((track) => track.stop());
    }
    if (peerConnection) {
        peerConnection.close();
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
