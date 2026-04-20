<template>
  <div class="relative" ref="dropdownRef">
    <!-- Responsive: Increased from p-2 to p-3 to ensure 48px minimum touch target -->
    <button
      @click="toggleDropdown"
      class="relative p-3 text-gray-500 hover:text-gray-900 transition-colors rounded-full hover:bg-gray-100"
    >
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
    </button>

    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-2"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-2"
    >
      <!-- Responsive: Changed w-80 to 'w-full sm:w-80 max-w-full' to prevent horizontal scroll on small devices -->
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-full sm:w-80 max-w-full bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-50"
      >
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
          <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Notifications</p>
          <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Mark all read</button>
        </div>

        <div v-if="loading" class="flex justify-center p-6">
          <div class="w-5 h-5 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
        </div>

        <template v-else-if="notifications.length">
          <div class="max-h-[320px] overflow-y-auto">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="px-5 py-3 border-b border-gray-50 transition-colors cursor-pointer"
              :class="notification.is_read ? 'opacity-70 hover:bg-gray-50' : 'bg-blue-50/20 hover:bg-blue-50/40'"
              @click="markAsRead(notification)"
            >
              <div class="flex items-start gap-3">
                <div class="mt-0.5 text-orange-500" v-if="notification.type === 'warning'">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-0.5 text-blue-500" v-else>
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-800" :class="{ 'font-semibold': !notification.is_read }">{{ notification.message }}</p>
                  <p class="text-[0.65rem] text-gray-400 mt-1">{{ formatTime(notification.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>
        </template>
        
        <div v-else class="px-5 py-8 text-center">
          <p class="text-sm text-gray-500">No notifications yet.</p>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()
const isOpen = ref(false)
const dropdownRef = ref(null)
const notifications = ref([])
const unreadCount = ref(0)
const loading = ref(false)

function toggleDropdown() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    fetchNotifications()
  }
}

async function fetchUnreadCount() {
  if (!auth.isAuthenticated.value) return
  try {
    const res = await auth.apiFetch('/v1/notifications/unread-count')
    unreadCount.value = res.count
  } catch(e) {
    console.error('Failed to get unread count', e)
  }
}

async function fetchNotifications() {
  loading.value = true
  try {
    const res = await auth.apiFetch('/v1/notifications')
    notifications.value = res
  } catch(e) {
    console.error('Failed to fetch notifications', e)
  } finally {
    loading.value = false
  }
}

async function markAsRead(notification) {
  if (notification.is_read) return
  
  notification.is_read = true
  unreadCount.value = Math.max(0, unreadCount.value - 1)
  
  try {
    await auth.apiFetch(`/v1/notifications/${notification.id}/read`, {
      method: 'PATCH'
    })
  } catch(e) {
    console.error('Failed to mark read', e)
    notification.is_read = false
    unreadCount.value++
  }
}

async function markAllAsRead() {
  const unreadNotifs = notifications.value.filter(n => !n.is_read)
  if (!unreadNotifs.length) return
  
  notifications.value.forEach(n => n.is_read = true)
  unreadCount.value = 0
  
  try {
    await auth.apiFetch('/v1/notifications/read-all', {
      method: 'POST'
    })
  } catch(e) {
    console.error('Failed to mark all as read', e)
    fetchNotifications() // Revert state by fetching
  }
}

function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

function formatTime(isoString) {
  const date = new Date(isoString)
  return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

let intervalId = null

onMounted(() => {
  window.addEventListener('mousedown', handleClickOutside)
  if (auth.isAuthenticated.value) {
    fetchUnreadCount()
    // Poll every 30 seconds for new notifications
    intervalId = setInterval(fetchUnreadCount, 30000)
  }
})

onUnmounted(() => {
  window.removeEventListener('mousedown', handleClickOutside)
  if (intervalId) clearInterval(intervalId)
})
</script>
