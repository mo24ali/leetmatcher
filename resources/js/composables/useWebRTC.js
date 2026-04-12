/***
 *
 *
 * peer-to-peer connection
 */

import "./useSignaling";
let localStream = null;
let peerConnection = null;

const rtcConfig = {
    iceServers: [
        { urls: "stun:stun.l.google.com:19302" },
        { urls: "stun:stun1.l.google.com:19302" },
    ],
};

function createPeerConnection() {}

function createOffer() {}

function createAnswer() {}

export function useWebRTC() {
    let peerConnection = null;
    let localStream = null;

    async function init(localStream) {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true,
            });
            if (localVideo.value) {
                localVideo.value.srcObject = localStream;
            }
        } catch (err) {
            console.error("Media Init Error:", err);
        }
    }
    function createConnection(onIce, onTrack) {
        if (!localStream) return;

        peerConnection = new RTCPeerConnection(rtcConfig);

        localStream.getTracks().forEach((track) => {
            peerConnection.addTrack(track, localStream);
        });

        peerConnection.ontrack = (event) => {
            if (remoteVideo.value) {
                remoteVideo.value.srcObject = event.streams[0];
                hasRemoteStream.value = true;
            }
        };

        peerConnection.onicecandidate = (event) => {
            if (event.candidate && channel) {
                // Signaling logic would go here
                // whisper ????
                channel.whisper("ice", event.candidate);
            }
        };
    }

    async function createOffer() {
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        return offer;
    }
    async function createAnswer(offer) {
        await peerConnection.setRemoteDescription(

            // RTCSessionDescription  used to create a description that contains , 
            // the audio/video codecs, encryption keys, ICE indo , media directions
            // codec (coder-decoder) the hardware or software that encrypt or decrypt the transfered media
            new RTCSessionDescription(offer),
        );

        const answer = await peerConnection.createAnswer();

        await peerConnection.setLocalDescription(answer);
        return answer;
    }
    async function setRemoteAnswer(answer) {
        await peerConnection.setRemoteDescription(
            new RTCSessionDescription(answer),
        );
    }
    async function addIceCandidate(candidate) {
        await peerConnection.addIceCandidate(new (candidate));
    }

    return {
        init,
        createConnection,
        createOffer,
        createAnswer,
        setRemoteAnswer,
        addIceCandidate,
    };
}
