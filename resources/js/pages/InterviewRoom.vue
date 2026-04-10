

<template>
    <div class="interview-room">
        <h2>Your camera</h2>
        <div class="videos">
            <div>
                <h3>You camera</h3>
                <video src="" ref="localVideo" autoplay playsinline muted>

                </video>
            </div>
            <div>
                <h3>Remote camera</h3>
                <video src="" ref="remoteVideo" autoplay playsinline muted>

                </video>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, onMounted } from "vue";


const localVideo = ref(null)
const remoteVideo = ref(null)

let localStream = null
let peerConnection = null

const config = {
    iceServers: [
        {
            urls: 'stun:stun.l.google.com:19302'
        }
    ]
}


async function initMedia() {
    try{
        // get access to the mic and cam
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        })

        // show your camera
        localVideo.value.srcObject = localStream
    }catch(err){
        console.error(err)
    }
}

function createPeerConnection(){
    peerConnection = new RTCPeerConnection(config)

    // send your stream to peer

    localStream.getTracks().forEach(track => {
        peerConnection.addTrack(track, localStream)
    })

    peerConnection.ontrack = (event) => {
        remoteVideo.value.srcObject = event.streams[0]
    }

    peerConnection.onicecandidate = (event) => {
        if(event.candidate) {
            console.log('New ICE candidate: ', event.candidate)
            // TODO : send via webSocket 
        }
    }
}


async function start() {
    await initMedia()
    createPeerConnection()
}

onMounted(()=>{
    start()
})

</script>

<style scoped>
.videos {
  display: flex;
  gap: 20px;
}

video {
  width: 300px;
  border-radius: 10px;
  background: black;
}
</style>