<template>
    <Header />
    <router-view />
    <ChatPanel 
        :isOpen="chatStore.state.isPanelOpen"
        :activeConversation="activeConversation"
        :messages="messages"
        :loading="loading"
        @close="chatStore.closeChat"
        @send="sendMessage"
    />
</template>

<script setup>
import { watch } from 'vue'
import Header from './components/Header.vue'
import ChatPanel from './components/ChatPanel.vue'
import { useChatStore } from '@/stores/chatStore'
import { useChat } from '@/composables/useChat'

const chatStore = useChatStore()
const { activeConversation, messages, loading, openConversation, sendMessage } = useChat()

// Watch store to trigger opening of conversation
watch(() => chatStore.state.activeUserId, (newId) => {
    if (newId) {
        openConversation(newId)
    }
})
</script>

<style>
</style>