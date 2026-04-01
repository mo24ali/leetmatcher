<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 font-sans">

    <!-- HEADER -->
    <div class="w-full max-w-4xl mb-6 flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">
        {{ isRecruiter ? 'Top Talent Matches' : 'Recommended Opportunities' }}
      </h2>
      <p class="text-sm text-gray-500">
        Showing {{ index + 1 }} of {{ items.length }}
      </p>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20">
      <div class="w-12 h-12 border-4 border-gray-200 border-t-gray-900 rounded-full animate-spin mb-4"></div>
      <p class="text-gray-500 font-medium">Finding your best matches...</p>
    </div>

    <!-- CARD CONTAINER -->
    <div v-else-if="items.length > 0" class="relative w-full max-w-4xl">

      <!-- LEFT ARROW -->
      <button
        v-if="index > 0"
        @click="prev"
        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 bg-white border border-gray-200 w-12 h-12 rounded-full shadow-lg hover:bg-gray-100 flex items-center justify-center transition-all z-10"
        title="Previous"
      >
        <span class="text-2xl text-gray-600">‹</span>
      </button>

      <!-- CARD -->
      <div v-if="currentItem" class="bg-white border border-gray-100 rounded-[2rem] p-10 shadow-xl transition-all duration-500 transform hover:scale-[1.01]">
        
        <!-- ROLE-BASED CONTENT -->
        <div v-if="!isRecruiter" class="job-seeker-view">
          <!-- JOB SEEKER VIEW: Recruiter Info (Top-Left) -->
          <div class="flex items-center gap-5 mb-10">
            <img
              :src="currentItem.recruiter?.avatar || `https://i.pravatar.cc/150?u=${currentItem.id}`"
              class="w-14 h-14 rounded-2xl object-cover border-2 border-gray-50 shadow-sm"
              alt="Recruiter profile"
            />
            <div>
              <p class="text-lg font-bold text-gray-900 leading-tight">
                {{ currentItem.recruiter?.name }}
              </p>
              <p class="text-sm font-medium text-blue-600">
                {{ currentItem.recruiter?.company }}
              </p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ currentItem.recruiter?.details }}
              </p>
            </div>
          </div>

          <!-- JOB SEEKER VIEW: Job Details (Center) -->
          <div class="space-y-6 mb-10">
            <div>
              <h3 class="text-3xl font-black text-gray-900 tracking-tight mb-3">
                {{ currentItem.title }}
              </h3>
              <p class="text-gray-600 leading-relaxed text-lg">
                {{ currentItem.description }}
              </p>
            </div>

            <div class="space-y-3">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Requirements</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(skill, i) in currentItem.skills || []"
                  :key="i"
                  class="px-4 py-1.5 text-sm bg-gray-900 text-white rounded-full font-medium"
                >
                  {{ skill }}
                </span>
              </div>
            </div>
          </div>

          <!-- JOB SEEKER VIEW: Actions (Bottom) -->
          <div class="flex gap-4">
            <button
              class="flex-1 h-14 bg-gray-900 text-white rounded-2xl font-bold text-base hover:bg-black transition-colors shadow-lg shadow-gray-200"
            >
              Apply Now
            </button>
            <button
              class="flex-1 h-14 border-2 border-gray-200 rounded-2xl text-base font-bold text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Contact Recruiter
            </button>
          </div>
        </div>

        <div v-else class="recruiter-view">
          <!-- RECRUITER VIEW: Applicant Profile (Top-Left) -->
          <div class="flex items-center gap-5 mb-10">
            <img
              :src="currentItem.avatar || `https://i.pravatar.cc/150?u=${currentItem.id}`"
              class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md"
              alt="Applicant profile picture"
            />
            <div>
              <p class="text-xl font-bold text-gray-900 leading-tight">
                {{ currentItem.name }}
              </p>
              <p class="text-sm font-medium text-blue-600">
                Potential Match
              </p>
            </div>
          </div>

          <!-- RECRUITER VIEW: Applicant Info (Center-to-Bottom) -->
          <div class="space-y-8 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Core Skills</p>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(skill, i) in currentItem.skills || []"
                    :key="i"
                    class="px-3 py-1 text-sm bg-blue-50 text-blue-700 border border-blue-100 rounded-lg font-semibold"
                  >
                    {{ skill }}
                  </span>
                </div>
              </div>
              
              <div class="space-y-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Experience</p>
                <p class="text-gray-700 font-medium">{{ currentItem.experience }}</p>
              </div>
            </div>

            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Qualifications</p>
              <p class="text-gray-800 text-lg leading-snug font-medium">
                {{ currentItem.qualifications }}
              </p>
            </div>
          </div>

          <!-- RECRUITER VIEW: Actions (Bottom) -->
          <div class="flex gap-4">
            <button
              class="w-full h-14 bg-blue-600 text-white rounded-2xl font-bold text-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100"
            >
              Contact Applicant
            </button>
          </div>
        </div>
      </div>

      <!-- RIGHT ARROW -->
      <button
        v-if="index < items.length - 1"
        @click="next"
        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 bg-white border border-gray-200 w-12 h-12 rounded-full shadow-lg hover:bg-gray-100 flex items-center justify-center transition-all z-10"
        title="Next"
      >
        <span class="text-2xl text-gray-600">›</span>
      </button>

    </div>

    <!-- EMPTY STATE -->
    <div v-if="!loading && items.length === 0" class="text-center py-20">
      <p class="text-gray-400 text-lg">No matches found at the moment.</p>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()
const isRecruiter = computed(() => auth.role.value === 'recruiter')

const items = ref([])
const loading = ref(true)
const index = ref(0)
const currentItem = computed(() => items.value[index.value] || null)

async function fetchMatches() {
  loading.value = true
  try {
    const endpoint = isRecruiter.value ? '/v1/recruiter/matches' : '/v1/projects/recommended'
    items.value = await auth.apiFetch(endpoint)
  } catch (err) {
    console.error('Failed to fetch matches:', err)
  } finally {
    loading.value = false
  }
}

function next() {
  if (index.value < items.value.length - 1) {
    index.value++
  }
}

function prev() {
  if (index.value > 0) {
    index.value--
  }
}

onMounted(() => {
  fetchMatches()
})
</script>

<style scoped>
/* Smooth transitions for card changes if needed */
.job-seeker-view, .recruiter-view {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>