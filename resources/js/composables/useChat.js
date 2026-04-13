import { ref, onMounted, onUnmounted, reactive } from 'vue';
import { useAuthStore } from '@/stores/authStore';

export function useChat() {
  const auth = useAuthStore();
  const conversations = ref([]);
  const activeConversation = ref(null);
  const messages = ref([]);
  const loading = ref(false);
  const isPanelOpen = ref(false);

  async function loadConversations() {
    try {
      const data = await auth.apiFetch('/v1/conversations');
      conversations.value = data;
    } catch (err) {
      console.error('Failed to load conversations:', err);
    }
  }

  async function openConversation(userId) {
    loading.value = true;
    isPanelOpen.value = true;
    try {
      // Find or start conversation
      const conv = await auth.apiFetch('/v1/conversations/start', {
        method: 'POST',
        body: JSON.stringify({ user_id: userId })
      });
      
      activeConversation.value = conv;
      await loadMessages(conv.id);
      
      // Subscribe to real-time updates
      subscribeToChannel(conv.id);
    } catch (err) {
      console.error('Failed to start conversation:', err);
    } finally {
      loading.value = false;
    }
  }

  async function loadMessages(conversationId) {
    try {
      const data = await auth.apiFetch(`/v1/conversations/${conversationId}`);
      messages.value = data;
    } catch (err) {
      console.error('Failed to load messages:', err);
    }
  }

  async function sendMessage(content) {
    if (!activeConversation.value || !content.trim()) return;

    try {
      const msg = await auth.apiFetch(`/v1/conversations/${activeConversation.value.id}/messages`, {
        method: 'POST',
        body: JSON.stringify({ content })
      });
      
      messages.value.push(msg);
      
      // Update last message in list
      const conv = conversations.value.find(c => c.id === activeConversation.value.id);
      if (conv) {
        conv.last_message_at = msg.created_at;
      }
    } catch (err) {
      console.error('Failed to send message:', err);
    }
  }

  function subscribeToChannel(conversationId) {
    // Leave previous channel if any
    window.Echo.leave(`chat.${conversationId}`);

    window.Echo.private(`chat.${conversationId}`)
      .listen('MessageSent', (e) => {
        if (activeConversation.value?.id === conversationId) {
          messages.value.push(e.message);
        }
        loadConversations(); // Refresh list to show new message hint
      });
  }

  function closePanel() {
    isPanelOpen.value = false;
    activeConversation.value = null;
    messages.value = [];
  }

  return {
    conversations,
    activeConversation,
    messages,
    loading,
    isPanelOpen,
    loadConversations,
    openConversation,
    sendMessage,
    closePanel
  };
}
