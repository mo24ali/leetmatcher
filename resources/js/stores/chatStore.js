import { reactive, ref } from 'vue';

const state = reactive({
    isPanelOpen: false,
    activeUserId: null,
});

function openChat(userId) {
    state.activeUserId = userId;
    state.isPanelOpen = true;
}

function closeChat() {
    state.isPanelOpen = false;
    state.activeUserId = null;
}

export function useChatStore() {
    return {
        state,
        openChat,
        closeChat
    };
}
