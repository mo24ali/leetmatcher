<template>
  <div class="min-h-screen bg-gray-50 font-sans">

    <!-- Page hero -->
    <section class="bg-white border-b border-gray-200 py-10">
      <div class="max-w-5xl mx-auto px-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight mb-1">Browse Jobs</h1>
        <p class="text-sm text-gray-500">Open positions matched to your profile skills</p>
      </div>
    </section>

    <div class="max-w-5xl mx-auto px-6 py-8">

      <!-- Filter bar -->
      <div class="flex flex-wrap gap-3 mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="Search by title, company, or skill…"
          class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900
                 placeholder-gray-400 outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100
                 transition bg-white"
        />
        <select
          v-model="filterType"
          class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 bg-white
                 outline-none focus:border-gray-600 transition"
        >
          <option value="">All Types</option>
          <option value="Remote">Remote</option>
          <option value="On-site">On-site</option>
          <option value="Hybrid">Hybrid</option>
        </select>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-20 gap-3 text-gray-400 text-sm">
        <span class="w-5 h-5 border-2 border-gray-200 border-t-gray-600 rounded-full animate-spin"></span>
        Loading jobs…
      </div>

      <!-- Job grid -->
      <div v-else-if="filteredJobs.length" class="grid gap-4 sm:grid-cols-2">
        <div
          v-for="job in filteredJobs"
          :key="job.id"
          class="bg-white border border-gray-200 rounded-2xl p-5 flex flex-col gap-4
                 hover:border-gray-400 hover:shadow-sm transition-all duration-150"
        >
          <div class="flex justify-between items-start gap-3">
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 text-[0.95rem] truncate">{{ job.title }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ job.company }}</p>
            </div>
            <span
              class="shrink-0 text-[0.65rem] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full"
              :class="typeBadgeClass(job.type)"
            >{{ job.type }}</span>
          </div>

          <p v-if="job.description" class="text-xs text-gray-600 leading-relaxed line-clamp-3">
            {{ job.description }}
          </p>

          <div class="flex flex-wrap gap-1.5">
            <span
              v-for="skill in (job.skills ?? [])"
              :key="skill"
              class="text-[0.72rem] bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-medium"
            >{{ skill }}</span>
          </div>

          <div class="flex justify-between items-center pt-1 border-t border-gray-100">
            <span class="text-[0.75rem] text-gray-400">
              Deadline: {{ job.deadline ?? 'Open' }}
            </span>
            <button
              class="text-xs font-semibold bg-gray-900 text-white px-4 py-1.5 rounded-lg
                     hover:bg-gray-700 transition disabled:opacity-50"
              :disabled="applying === job.id"
              @click="applyToJob(job)"
            >
              {{ applying === job.id ? 'Applying…' : 'Apply' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-20">
        <p class="text-gray-400 text-sm mb-3">No jobs found matching your criteria.</p>
        <router-link
          to="/profile"
          class="inline-block text-xs font-semibold bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition"
        >
          Update your profile to improve matches
        </router-link>
      </div>

      <!-- Success toast -->
      <transition name="toast">
        <div
          v-if="toastMsg"
          class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm font-medium
                 px-5 py-3 rounded-xl shadow-lg z-50"
        >
          {{ toastMsg }}
        </div>
      </transition>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()

const loading    = ref(true)
const jobs       = ref([])
const search     = ref('')
const filterType = ref('')
const applying   = ref(null)
const toastMsg   = ref('')

function typeBadgeClass(type) {
  if (type === 'Remote')  return 'bg-blue-50 text-blue-700'
  if (type === 'Hybrid')  return 'bg-purple-50 text-purple-700'
  return 'bg-gray-100 text-gray-600'
}

const filteredJobs = computed(() => {
  const q = search.value.toLowerCase()
  return jobs.value.filter(job => {
    const matchesQuery = !q ||
      job.title?.toLowerCase().includes(q) ||
      job.company?.toLowerCase().includes(q) ||
      (job.skills ?? []).some(s => s.toLowerCase().includes(q))

    const matchesType = !filterType.value || job.type === filterType.value

    return matchesQuery && matchesType
  })
})

async function loadJobs() {
  loading.value = true
  try {
    // const data = await auth.apiFetch('/v1/applicant/recommended-jobs')
    // jobs.value = data
    jobs.value = []
  } catch (err) {
    console.error('Failed to load jobs:', err)
  } finally {
    loading.value = false
  }
}

async function applyToJob(job) {
  applying.value = job.id
  try {
    await auth.apiFetch('/v1/applications', {
      method: 'POST',
      body: JSON.stringify({ project_id: job.id }),
    })
    showToast('Application submitted!')
  } catch (err) {
    showToast(err.message || 'Application failed.')
  } finally {
    applying.value = null
  }
}

function showToast(msg) {
  toastMsg.value = msg
  setTimeout(() => toastMsg.value = '', 3500)
}

onMounted(loadJobs)
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.toast-enter-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.toast-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.toast-enter-from   { opacity: 0; transform: translateY(8px); }
.toast-leave-to     { opacity: 0; transform: translateY(8px); }
</style>
