<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-start p-6 font-sans">
    <div class="w-full max-w-6xl mb-6 flex justify-between items-center mt-8">
      <h2 class="text-2xl font-bold text-gray-900">
        {{ isRecruiter ? 'Top Talent Matches' : 'Recommended Opportunities' }}
      </h2>
      <p class="text-sm text-gray-500">
        Found {{ items.length }} {{ isRecruiter ? 'Applicants' : 'Projects' }}
      </p>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 w-full">
      <div class="w-12 h-12 border-4 border-gray-200 border-t-gray-900 rounded-full animate-spin mb-4"></div>
      <p class="text-gray-500 font-medium">Finding your best matches...</p>
    </div>

    <!-- LIST CONTAINER -->
    <div v-else-if="items.length > 0" class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="(item, i) in items" :key="i"
        @click="openDetails(item)"
        class="bg-white border border-gray-100 rounded-3xl p-6 shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer flex flex-col justify-between group"
      >
        <div>
          <!-- APPLICANT HIGHLIGHT / PROJECT HIGHLIGHT -->
          <div class="flex items-center gap-4 mb-4">
            <ProfileAvatar 
              :url="isRecruiter ? item.avatar : item.recruiter?.avatar" 
              :name="isRecruiter ? item.name : item.recruiter?.name" 
              size="md"
              :shape="isRecruiter ? 'circle' : 'rounded'"
            />
            <div class="flex-1">
              <p class="text-lg font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">
                {{ isRecruiter ? item.name : item.title }}
              </p>
              <p class="text-sm font-medium text-gray-500">
                {{ isRecruiter ? item.qualifications : item.recruiter?.company }}
              </p>
            </div>
            <!-- MATCH PERCENTAGE -->
            <div v-if="item.match_score !== undefined" class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold text-sm border border-blue-100">
              {{ item.match_score }}% Match
            </div>
          </div>

          <!-- DESCRIPTION PREVIEW -->
          <p class="text-gray-600 text-sm line-clamp-2 mb-4">
            {{ isRecruiter ? item.experience : item.description }}
          </p>

          <!-- SKILLS PREVIEW -->
          <div class="flex flex-wrap gap-1.5 mb-4 max-h-16 overflow-hidden">
             <span
                v-for="(skill, j) in item.skills?.slice(0, 3) || []"
                :key="j"
                class="px-2 py-0.5 text-[0.65rem] bg-gray-100 text-gray-600 rounded-md font-medium uppercase"
              >
                {{ skill }}
              </span>
              <span v-if="item.skills?.length > 3" class="px-2 py-0.5 text-[0.65rem] bg-gray-50 text-gray-400 rounded-md font-medium uppercase">
                +{{ item.skills.length - 3 }} more
              </span>
          </div>
        </div>

        <div class="mt-2 text-right">
          <span class="text-sm font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
            View Details →
          </span>
        </div>
      </div>
    </div>

    <!-- EMPTY STATE -->
    <div v-if="!loading && items.length === 0" class="text-center py-20 w-full">
      <p class="text-gray-400 text-lg">No matches found at the moment.</p>
    </div>

    <!-- DETAILS MODAL -->
    <transition name="modal">
      <div v-if="showModal && currentItem" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeDetails"></div>
        
        <!-- Modal Content (Existing Card Structure) -->
        <div class="relative bg-white border border-gray-100 rounded-[2rem] p-8 md:p-10 shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
          
          <!-- Close Button -->
          <button @click="closeDetails" class="absolute top-6 right-6 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 transition-colors">
            ✕
          </button>

          <!-- ROLE-BASED CONTENT -->
          <div v-if="!isRecruiter">
            <!-- JOB SEEKER VIEW -->
            <div class="flex items-center gap-5 mb-10 border-b border-gray-100 pb-8">
               <ProfileAvatar 
                :url="currentItem.recruiter?.avatar" 
                :name="currentItem.recruiter?.name" 
                size="lg" shape="rounded"
              />
              <div class="flex-1">
                <p class="text-xl font-bold text-gray-900 leading-tight">{{ currentItem.recruiter?.name }}</p>
                <p class="text-sm font-medium text-blue-600">{{ currentItem.recruiter?.company }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ currentItem.recruiter?.details }}</p>
              </div>
               <!-- REVIEWS -->
              <div v-if="currentItem.recruiter?.reviews?.length" class="flex flex-col items-end">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Recruiter Rating</p>
                <div class="flex gap-1 text-teal-500">
                  ★ {{ (currentItem.recruiter.reviews.reduce((a, b) => a + b.rating, 0) / currentItem.recruiter.reviews.length).toFixed(1) }}
                </div>
                <p class="text-xs text-gray-400 mt-1">({{ currentItem.recruiter.reviews.length }} reviews)</p>
              </div>
            </div>

            <div class="space-y-6 mb-10">
              <div>
                <h3 class="text-3xl font-black text-gray-900 mbb-3">{{ currentItem.title }}</h3>
                <p class="text-gray-600 leading-relaxed text-lg mt-4">{{ currentItem.description }}</p>
              </div>
              
              <SkillsSection :skills="currentItem.skills" />

            </div>

             <div class="flex gap-4 pt-6 border-t border-gray-100">
              <button class="flex-1 h-14 bg-gray-900 text-white rounded-2xl font-bold text-base hover:bg-black transition-colors" @click="sendApplication">
                Apply Now
              </button>
            </div>
          </div>

          <div v-else>
            <!-- RECRUITER VIEW -->
            <div class="flex items-center gap-5 mb-10 border-b border-gray-100 pb-8">
              <ProfileAvatar 
                :url="currentItem.avatar" 
                :name="currentItem.name" 
                size="lg" shape="circle"
              />
              <div class="flex-1">
                <p class="text-2xl font-bold text-gray-900 leading-tight">{{ currentItem.name }}</p>
                <p class="text-sm font-medium text-blue-600">Applicant • {{ currentItem.match_score }}% Match Score</p>
              </div>
              
              <!-- REVIEWS -->
              <div v-if="currentItem.reviews?.length" class="flex flex-col items-end">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Applicant Rating</p>
                <div class="flex gap-1 text-orange-500">
                  ★ {{ (currentItem.reviews.reduce((a, b) => a + b.rating, 0) / currentItem.reviews.length).toFixed(1) }}
                </div>
                <p class="text-xs text-gray-400 mt-1">({{ currentItem.reviews.length }} reviews)</p>
              </div>
            </div>

            <div class="space-y-8 mb-10">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <SkillsSection :skills="currentItem.skills" />
                
                <div class="space-y-4">
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Experience</p>
                  <p class="text-gray-700 font-medium">{{ currentItem.experience }}</p>
                </div>
              </div>

              <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Qualifications</p>
                <p class="text-gray-800 text-lg leading-snug font-medium">{{ currentItem.qualifications }}</p>
              </div>

              <!-- REVIEWS LIST -->
              <div v-if="currentItem.reviews?.length" class="space-y-4 pt-6 border-t border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Feedback</p>
                <div class="space-y-4">
                  <div v-for="(review, rIdx) in currentItem.reviews" :key="rIdx" class="bg-gray-50 p-4 rounded-xl">
                    <div class="flex justify-between items-center mb-2">
                       <span class="font-bold text-sm text-gray-900">{{ review.reviewer_name }}</span>
                       <span class="text-orange-500 text-sm font-bold">★ {{ review.rating }}</span>
                    </div>
                    <p class="text-sm text-gray-600 italic">"{{ review.comment }}"</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-gray-100">
              <button class="w-full h-14 bg-blue-600 text-white rounded-2xl font-bold text-lg hover:bg-blue-700 transition-colors">
                Contact Applicant
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import ProfileAvatar from '../components/ProfileAvatar.vue'

// Inline component to prevent duplication of Skills toggle logic
const SkillsSection = {
  props: ['skills'],
  setup(props) {
    const isExpanded = ref(false)
    return { isExpanded }
  },
  template: `
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Core Skills</p>
        <button v-if="skills && skills.length > 0" @click="isExpanded = !isExpanded" 
                class="text-xs font-bold px-2 py-1 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition">
          {{ isExpanded ? '− Collapse' : '+ Expand All' }}
        </button>
      </div>
      
      <div v-if="isExpanded" class="flex flex-wrap gap-2 transition-all">
        <span v-for="(skill, i) in skills || []" :key="i"
              class="px-3 py-1 text-sm bg-blue-50 text-blue-700 border border-blue-100 rounded-lg font-semibold transform transition-all animate-fade-in">
          {{ skill }}
        </span>
      </div>
      <div v-else class="text-gray-400 text-sm font-medium italic">
        Skills hidden by default. Click expand.
      </div>
    </div>
  `
}

const auth = useAuthStore()
const isRecruiter = computed(() => auth.role.value === 'recruiter')

const items = ref([])
const loading = ref(true)
const showModal = ref(false)
const currentItem = ref(null)

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

function openDetails(item) {
  currentItem.value = item
  showModal.value = true
}

function closeDetails() {
  showModal.value = false
  setTimeout(() => currentItem.value = null, 300) // wait for animation
}

async function sendApplication() {
  if (!currentItem.value) return

  try {
    const data = await auth.apiFetch('/v1/application/apply', {
      method: 'POST',
      body: JSON.stringify({
        project_id: currentItem.value.id
      }),
    })

    if (data.success) {
      alert('Application submitted!')
    } else {
      alert(data.message || 'You already applied or there was an error.')
    }
  } catch (error) {
    console.error(error)
    alert(error.message || 'Something went wrong')
  }
}

onMounted(() => {
  fetchMatches()
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
}
.modal-enter-from .relative {
  opacity: 0;
  transform: translateY(20px) scale(0.98);
}
.modal-leave-to .relative {
  opacity: 0;
  transform: scale(0.98);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>