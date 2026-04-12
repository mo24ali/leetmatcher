/**
 *
 *
 * laravel Echo / websocket in here
 */

import Echo from "laravel-echo";
import { send } from "vite";
let channel = null;
channel = Echo.private();

channel.listeForWhisper();
channel.whisper();

async function startCall() {
    const offer = await peerConnection.createOffer();
    // setLocalDescription ????
    await peerConnection.setLocalDescription(offer);
    channel.whisper("offer", offer);
}
function listenSignaling() {
    channel.listenForWhisper("offer", async (offer) => {
        if (!peerConnection) return;

        await peerConnection.setRemoteDescription(
            new RTCSessionDescription(offer),
        );

        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);

        channel.whisper("answer", answer);
    });
    // answer (response offer)
    channel.listenForWhisper("answer", async (answer) => {
        await peerConnection.setRemoteDescription(
            new RTCSessionDescription(answer),
        );
    });

    // ICE stands for Interactive Connectivity Establishment
    channel.listenForWhisper("ice", async (candidate) => {
        try {
            // ICECandidate is a possible address + port where sender can reach receiver
            await peerConnection.addIceCandidate(
                new RTCIceCandidate(candidate),
            );
        } catch (err) {
            console.error("ICE error: ", err);
        }
    });
}

export function useSignaling(roomId) {
    const channel = Echo.private(`interview.${roomId}`);

    function sendOffer(offer) {}
    function sendAnswer(answer) {}
    function sendIce(candidate) {}

    function onOffer(callback) {}
    function onAnswer(callback) {}
    function onIce(callback) {}

    return {
        sendOffer,
        sendAnswer,
        sendIce,
        onOffer,
        onAnswer,
        onIce,
    };
}
