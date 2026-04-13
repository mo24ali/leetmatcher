import { ref, onUnmounted } from 'vue';

export function useWebRTC() {
    const localStream = ref(null);
    const remoteStream = ref(null);
    const peerConnection = ref(null);
    const connectionState = ref('disconnected');
    const isMuted = ref(false);
    const isVideoOff = ref(false);

    const rtcConfig = {
        iceServers: [
            { urls: "stun:stun.l.google.com:19302" },
            { urls: "stun:stun1.l.google.com:19302" },
            { urls: "stun:stun2.l.google.com:19302" },
        ],
    };

    const startLocalStream = async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true,
            });
            localStream.value = stream;
            return stream;
        } catch (error) {
            console.error("Failed to get local stream:", error);
            throw error;
        }
    };

    const createPeerConnection = (onIceCandidate, onTrack) => {
        if (peerConnection.value) {
            console.warn("Peer connection already exists. Closing old one.");
            closeConnection();
        }

        const pc = new RTCPeerConnection(rtcConfig);

        pc.onicecandidate = (event) => {
            if (event.candidate) {
                onIceCandidate(event.candidate);
            }
        };

        pc.ontrack = (event) => {
            if (event.streams && event.streams[0]) {
                remoteStream.value = event.streams[0];
                onTrack(event.streams[0]);
            }
        };

        pc.onconnectionstatechange = () => {
            connectionState.value = pc.connectionState;
            console.log("Connection State:", pc.connectionState);
        };

        if (localStream.value) {
            localStream.value.getTracks().forEach((track) => {
                pc.addTrack(track, localStream.value);
            });
        }

        peerConnection.value = pc;
        return pc;
    };

    const createOffer = async () => {
        if (!peerConnection.value) return;
        const offer = await peerConnection.value.createOffer();
        await peerConnection.value.setLocalDescription(offer);
        return offer;
    };

    const handleOffer = async (offer) => {
        if (!peerConnection.value) return;
        await peerConnection.value.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await peerConnection.value.createAnswer();
        await peerConnection.value.setLocalDescription(answer);
        return answer;
    };

    const handleAnswer = async (answer) => {
        if (!peerConnection.value) return;
        await peerConnection.value.setRemoteDescription(new RTCSessionDescription(answer));
    };

    const handleIceCandidate = async (candidate) => {
        if (peerConnection.value && candidate) {
            try {
                await peerConnection.value.addIceCandidate(new RTCIceCandidate(candidate));
            } catch (e) {
                console.error("Error adding ice candidate", e);
            }
        }
    };

    const toggleAudio = () => {
        if (localStream.value) {
            const audioTrack = localStream.value.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                isMuted.value = !audioTrack.enabled;
            }
        }
    };

    const toggleVideo = () => {
        if (localStream.value) {
            const videoTrack = localStream.value.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                isVideoOff.value = !videoTrack.enabled;
            }
        }
    };

    const closeConnection = () => {
        if (peerConnection.value) {
            peerConnection.value.close();
            peerConnection.value = null;
        }
        if (localStream.value) {
            localStream.value.getTracks().forEach(track => track.stop());
            localStream.value = null;
        }
        remoteStream.value = null;
        connectionState.value = 'disconnected';
    };

    onUnmounted(() => {
        closeConnection();
    });

    return {
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
    };
}
