<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="translate-x-full"
    enter-to-class="translate-x-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="translate-x-0"
    leave-to-class="translate-x-full"
  >
    <div v-if="isOpen" class="fixed inset-y-0 right-0 w-full sm:w-[400px] bg-white shadow-2xl z-[60] flex flex-col border-l border-gray-100">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
            {{ activeConversation?.otherUser?.name?.[0] || '?' }}
          </div>
          <div>
            <h3 class="text-sm font-bold text-gray-900">{{ activeConversation?.otherUser?.name || 'Messaging' }}</h3>
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-green-500"></span>
              <span class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Online</span>
            </div>
          </div>
        </div>
        <button @click="close" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
          <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M18 6L6 18M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Messages Area -->
      <div class="flex-grow overflow-y-auto p-6 space-y-4 bg-gray-50/50" ref="messageContainer">
        <div v-if="loading" class="flex justify-center py-10">
          <div class="w-6 h-6 border-2 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
        </div>
        
        <template v-else>
          <div v-for="msg in messages" :key="msg.id" 
               class="flex flex-col"
               :class="[msg.sender_id === auth.state.user?.id ? 'items-end' : 'items-start']">
            <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm shadow-sm"
                 :class="[msg.sender_id === auth.state.user?.id ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-900 border border-gray-100 rounded-bl-none']">
              {{ msg.content }}
            </div>
            <span class="text-[0.6rem] text-gray-400 mt-1 px-1 font-medium">
              {{ formatTime(msg.created_at) }}
            </span>
          </div>
        </template>
      </div>

      <!-- Footer / Input -->
      <div class="p-4 bg-white border-t border-gray-100">
        <form @submit.prevent="handleSend" class="flex items-center gap-2">
          <input 
            v-model="newMessage"
            type="text" 
            placeholder="Type your message..."
            class="flex-grow bg-gray-100 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-600 transition-all outline-none"
          />
          <button 
            type="submit" 
            :disabled="!newMessage.trim()"
            class="w-11 h-11 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all disabled:opacity-50 disabled:scale-95 active:scale-90"
          >
            <svg class="w-5 h-5 translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, nextTick, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const props = defineProps({
  isOpen: Boolean,
  activeConversation: Object,
  messages: Array,
  loading: Boolean
})

const emit = defineEmits(['close', 'send'])

const auth = useAuthStore()
const newMessage = ref('')
const messageContainer = ref(null)

function close() {
  emit('close')
}

function handleSend() {
  if (!newMessage.value.trim()) return
  emit('send', newMessage.value)
  newMessage.value = ''
}

function formatTime(timestamp) {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

function scrollToBottom() {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight
    }
  })
}

watch(() => props.messages, scrollToBottom, { deep: true })
watch(() => props.isOpen, (newVal) => {
  if (newVal) scrollToBottom()
})
</script>
