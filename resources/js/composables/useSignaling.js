import { ref, onUnmounted } from 'vue';

export function useSignaling(roomId) {
    const channel = ref(null);
    const isReady = ref(false);

    const init = () => {
        if (!window.Echo) {
            console.error('Laravel Echo is not initialized. Check bootstrap.js');
            return;
        }

        channel.value = window.Echo.join(`interview.${roomId}`);

        channel.value.here((users) => {
            console.log('Users currently in room:', users);
            isReady.value = true;
        })
        .joining((user) => {
            console.log('User joined:', user);
        })
        .leaving((user) => {
            console.log('User left:', user);
        })
        .error((error) => {
            console.error('Signaling Channel Error:', error);
        });
    };

    const sendSignal = (event, data) => {
        if (channel.value) {
            channel.value.whisper(event, data);
        }
    };

    const onSignal = (event, callback) => {
        if (channel.value) {
            channel.value.listenForWhisper(event, callback);
        }
    };

    const leave = () => {
        if (channel.value) {
            window.Echo.leave(`interview.${roomId}`);
            channel.value = null;
            isReady.value = false;
        }
    };

    onUnmounted(() => {
        leave();
    });

    return {
        init,
        isReady,
        channel,
        sendSignal,
        onSignal,
        leave
    };
}
