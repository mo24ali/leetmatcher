<template>
  <div class="interviews-page max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Toast Notification -->
    <transition name="toast">
      <div
        v-if="toast.show"
        :class="['toast-notification', toast.type === 'error' ? 'toast-error' : 'toast-success']"
      >
        <span>{{ toast.message }}</span>
        <button @click="toast.show = false" class="toast-close">×</button>
      </div>
    </transition>

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Interviews</h1>
      <p class="text-gray-500 mt-1">Manage all your scheduled interviews here</p>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-gray-500">Loading interviews...</p>
    </div>

    <div v-else-if="interviews.length === 0" class="text-center py-16 bg-gray-50 rounded-2xl border border-gray-100">
      <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900">No interviews scheduled</h3>
      <p class="mt-1 text-gray-500 max-w-sm mx-auto">When your applications are moved forward, interviews will appear here.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="interview in interviews"
        :key="interview.id"
        class="flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Card Header -->
        <div class="p-5 border-b border-gray-100">
          <div class="flex justify-between items-start mb-2">
            <span class="text-xs font-bold px-2 py-1 bg-blue-50 text-blue-700 rounded-md uppercase tracking-wide">
              App #{{ interview.application_id }}
            </span>
            <span :class="statusClass(interview)" class="text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wide">
              {{ statusText(interview) }}
            </span>
          </div>

          <h2 class="text-lg font-bold text-gray-900 mt-2 truncate" :title="interview.application?.project?.title">
            {{ interview.application?.project?.title || 'Unknown Project' }}
          </h2>

          <div class="mt-3 space-y-1 text-sm text-gray-600">
            <p v-if="auth.role.value === 'recruiter'" class="flex items-center gap-2">
              <span class="font-medium w-20">Applicant:</span>
              <span class="text-gray-900">{{ interview.application?.student?.name }}</span>
            </p>
            <p v-else class="flex items-center gap-2">
              <span class="font-medium w-20">Recruiter:</span>
              <span class="text-gray-900">{{ interview.application?.project?.recruiter?.name || 'Recruiter' }}</span>
            </p>
          </div>
        </div>

        <!-- Card Body: Date & Countdown -->
        <div class="p-5 flex-grow bg-gray-50/50 flex flex-col justify-center">
          <div class="text-center">
            <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-1">Scheduled for</p>

            <!-- Inline edit form -->
            <div v-if="editingId === interview.id" class="mt-2 text-left">
              <input
                type="datetime-local"
                v-model="editForm.scheduled_at"
                class="w-full border border-gray-300 rounded-md text-sm p-2 mb-2 focus:outline-none focus:border-blue-500"
              />
              <div class="flex gap-2">
                <button
                  @click="saveDate(interview)"
                  :disabled="savingId === interview.id"
                  class="flex-1 bg-blue-600 text-white text-xs font-bold py-2 rounded-lg disabled:opacity-50 hover:bg-blue-700 transition"
                >
                  <span v-if="savingId === interview.id" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                  {{ savingId === interview.id ? 'Saving…' : 'Save' }}
                </button>
                <button @click="cancelEdit" class="flex-1 bg-gray-200 text-gray-700 text-xs font-bold py-2 rounded-lg hover:bg-gray-300 transition">
                  Cancel
                </button>
              </div>
            </div>

            <!-- Date display -->
            <div v-else class="group relative">
              <p class="text-lg font-bold text-gray-900">
                {{ formatDateTime(interview.scheduled_at) }}
              </p>
              <button
                @click="startEdit(interview)"
                class="mt-1 text-xs text-blue-600 hover:text-blue-800 underline opacity-0 group-hover:opacity-100 transition-opacity"
              >
                {{ auth.role.value === 'recruiter' ? 'Reschedule' : 'Request reschedule' }}
              </button>
            </div>

            <!-- Countdown -->
            <div class="mt-4 pt-4 border-t border-gray-200 border-dashed">
              <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-1">Time Remaining</p>
              <p class="font-mono text-xl font-bold bg-gray-900 text-white py-1.5 px-3 rounded-lg inline-block w-auto mx-auto shadow-sm">
                {{ getCountdown(interview.scheduled_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Card Footer: Actions -->
        <div class="p-5 border-t border-gray-100 flex flex-col gap-2">
          <router-link
            :to="isJoinable(interview.scheduled_at) ? '/interview-room/' + interview.id : ''"
            class="block w-full text-center py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 no-underline"
            :class="isJoinable(interview.scheduled_at)
              ? 'bg-blue-600 text-white hover:bg-blue-700'
              : 'bg-gray-200 text-gray-500 pointer-events-none'"
          >
            {{ isJoinable(interview.scheduled_at) ? 'Join Interview Room' : 'Not time yet' }}
          </router-link>

          <!-- Recruiter: finalize outcome after interview -->
          <div
            v-if="auth.role.value === 'recruiter' && isPast(interview.scheduled_at) && interview.application?.status === 'in_progress'"
            class="flex gap-2"
          >
            <button
              @click="finalizeApplication(interview, 'accepted')"
              class="flex-1 bg-green-100 text-green-700 px-3 py-2 rounded-lg text-xs font-bold hover:bg-green-200 transition"
            >
              Candidate Passed
            </button>
            <button
              @click="finalizeApplication(interview, 'rejected')"
              class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-lg text-xs font-bold hover:bg-red-200 transition"
            >
              Candidate Failed
            </button>
          </div>

          <!-- Recruiter: delete interview -->
          <button
            v-if="auth.role.value === 'recruiter'"
            @click="deleteInterview(interview)"
            :disabled="deletingId === interview.id"
            class="w-full py-2 rounded-lg text-xs font-bold border border-red-200 text-red-600 hover:bg-red-50 transition disabled:opacity-50"
          >
            <span v-if="deletingId === interview.id" class="inline-block w-3 h-3 border-2 border-red-400 border-t-transparent rounded-full animate-spin mr-1"></span>
            {{ deletingId === interview.id ? 'Deleting…' : 'Delete Interview' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()
const loading  = ref(true)
const interviews = ref([])
const editingId  = ref(null)
const savingId   = ref(null)
const deletingId = ref(null)
const editForm = reactive({ scheduled_at: '' })

// Toast state
const toast = reactive({ show: false, message: '', type: 'success' })

function showToast(message, type = 'success', duration = 3500) {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => { toast.show = false }, duration)
}

// Live clock for countdown
let timer = null
const now = ref(new Date().getTime())

//  Data loading 

async function loadInterviews() {
  loading.value = true
  try {
    const data = await auth.apiFetch('/v1/interviews')
    interviews.value = data.data || data
  } catch (error) {
    console.error('Failed to load interviews', error)
    showToast('Failed to load interviews', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadInterviews()
  timer = setInterval(() => { now.value = new Date().getTime() }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

//  Formatting helpers 

function formatDateTime(datetime) {
  const d = new Date(datetime)
  return d.toLocaleString('en-US', {
    weekday: 'short', month: 'short', day: 'numeric',
    hour: 'numeric', minute: '2-digit'
  })
}

function getCountdown(datetime) {
  const target = new Date(datetime).getTime()
  const diff = target - now.value

  if (diff < 0) return 'Started'

  const days  = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const mins  = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const secs  = Math.floor((diff % (1000 * 60)) / 1000)

  if (days > 0) return `${days}d ${hours}h ${mins}m`
  return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}

function isJoinable(datetime) {
  const target = new Date(datetime).getTime()
  return now.value >= (target - 15 * 60 * 1000)
}

function isPast(datetime) {
  // Considered "past" 1 hour after scheduled start
  return now.value > new Date(datetime).getTime() + 60 * 60 * 1000
}

function statusClass(interview) {
  const target = new Date(interview.scheduled_at).getTime()
  if (now.value > target + 60 * 60 * 1000)     return 'bg-gray-100 text-gray-700'   // Past
  if (now.value >= target - 15 * 60 * 1000)    return 'bg-green-100 text-green-700' // Ongoing
  return 'bg-purple-100 text-purple-700'                                              // Upcoming
}

function statusText(interview) {
  const target = new Date(interview.scheduled_at).getTime()
  if (now.value > target + 60 * 60 * 1000)    return 'Past'
  if (now.value >= target - 15 * 60 * 1000)   return 'Ongoing'
  return 'Upcoming'
}

//  Edit / Update 

function startEdit(interview) {
  editingId.value = interview.id
  // datetime-local input needs YYYY-MM-DDTHH:mm
  editForm.scheduled_at = new Date(interview.scheduled_at).toISOString().slice(0, 16)
}

function cancelEdit() {
  editingId.value = null
  editForm.scheduled_at = ''
}

async function saveDate(interview) {
  if (!editForm.scheduled_at) return showToast('Please select a date and time.', 'error')

  savingId.value = interview.id
  try {
    const formatted = new Date(editForm.scheduled_at).toISOString().slice(0, 19).replace('T', ' ')
    const res = await auth.apiFetch(`/v1/interviews/${interview.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ scheduled_at: formatted }),
    })

    // Update local state with server response so card re-renders immediately
    const updated = res.interview ?? res
    const idx = interviews.value.findIndex(i => i.id === interview.id)
    if (idx !== -1) {
      interviews.value[idx] = { ...interviews.value[idx], scheduled_at: updated.scheduled_at ?? formatted }
    }

    cancelEdit()
    showToast('Interview rescheduled successfully.')
  } catch (err) {
    showToast(err.message || 'Failed to update interview date.', 'error')
  } finally {
    savingId.value = null
  }
}

//  Delete 

async function deleteInterview(interview) {
  const projectTitle = interview.application?.project?.title || `Interview #${interview.id}`
  if (!confirm(`Delete the interview for "${projectTitle}"?\nThis action cannot be undone.`)) return

  deletingId.value = interview.id
  try {
    await auth.apiFetch(`/v1/interviews/${interview.id}`, { method: 'DELETE' })
    interviews.value = interviews.value.filter(i => i.id !== interview.id)
    showToast('Interview deleted successfully.')
  } catch (err) {
    showToast(err.message || 'Failed to delete interview.', 'error')
  } finally {
    deletingId.value = null
  }
}

//  Finalize application outcome 

async function finalizeApplication(interview, finalStatus) {
  const label = finalStatus === 'accepted' ? 'Pass' : 'Fail'
  if (!confirm(`Mark this candidate as "${label}"?`)) return
  try {
    await auth.apiFetch(`/v1/applications/${interview.application_id}`, {
      method: 'PATCH',
      body: JSON.stringify({ status: finalStatus }),
    })
    if (interview.application) interview.application.status = finalStatus
    showToast(`Candidate successfully ${finalStatus === 'accepted' ? 'passed' : 'rejected'}.`)
  } catch (err) {
    showToast(err.message || 'Failed to finalize application.', 'error')
  }
}
</script>

<style scoped>
/* Toast */
.toast-notification {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  z-index: 9999;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem 1.25rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  max-width: 360px;
}
.toast-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
.toast-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
.toast-close   { background: none; border: none; cursor: pointer; font-size: 1.1rem; line-height: 1; opacity: 0.6; padding: 0; }
.toast-close:hover { opacity: 1; }

/* Toast transition */
.toast-enter-active { transition: all 0.3s ease; }
.toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(12px); }
</style>
