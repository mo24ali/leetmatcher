<template>
  <header
    class="sticky top-0 z-50 transition-all duration-300 backdrop-blur-md"
    :class="[scrolled ? 'bg-white/90 border-b border-gray-200 shadow-sm' : 'bg-white/80']"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex justify-between items-center h-16 sm:h-20">
        <!-- Logo -->
        <router-link to="/" class="flex items-baseline gap-1 select-none no-underline group">
          <span class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 transition-colors group-hover:text-blue-600">LEET</span>
          <span class="text-lg sm:text-xl font-medium tracking-tight text-gray-400">MATCHER</span>
        </router-link>

        <!-- Centered Navigation links (Adaptive) -->
        <div :class="[
          mobileMenuOpen ? 'flex flex-col absolute top-full left-0 w-full bg-white/95 backdrop-blur-md shadow-xl p-4 border-b border-gray-200 z-50' : 'hidden', 
          'md:flex md:flex-row md:relative md:top-auto md:left-auto md:w-auto md:bg-transparent md:shadow-none md:border-none md:p-0 items-center gap-1'
        ]">
          <template v-for="link in filteredLinks" :key="link.path">
            <router-link
              :to="link.path"
              class="relative px-4 py-2 text-[0.925rem] font-medium transition-all duration-200"
              :class="[
                isActive(link.path)
                  ? 'text-gray-900 bg-gray-100 rounded-lg'
                  : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-lg'
              ]"
            >
              {{ link.label }}
            </router-link>
          </template>

          <!-- Applications Dropdown (Applicants only) -->
          <div v-if="auth.role.value === 'applicant'" class="relative" ref="appsMenuRef">
            <button
              class="flex items-center gap-1.5 px-4 py-2 text-[0.925rem] font-medium transition-all duration-200 rounded-lg"
              :class="[
                appsOpen
                  ? 'text-gray-900 bg-gray-100'
                  : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'
              ]"
              @click="toggleApps"
            >
              Applications
              <svg
                class="w-4 h-4 transition-transform duration-200"
                :class="{ 'rotate-180': appsOpen }"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              >
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </button>

            <!-- Dropdown content -->
            <transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="transform scale-95 opacity-0 -translate-y-2"
              enter-to-class="transform scale-100 opacity-100 translate-y-0"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="transform scale-100 opacity-100 translate-y-0"
              leave-to-class="transform scale-95 opacity-0 -translate-y-2"
            >
              <div
                v-if="appsOpen"
                class="absolute right-0 mt-2 w-full sm:w-[340px] max-w-full bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden"
              >
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                  <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Matched Jobs</p>
                  <p class="text-[0.7rem] text-gray-500 mt-0.5">Recommended based on your profile</p>
                </div>

                <div v-if="loadingJobs" class="flex items-center gap-3 px-5 py-6 text-sm text-gray-400">
                  <div class="w-4 h-4 border-2 border-gray-200 border-t-gray-600 rounded-full animate-spin"></div>
                  Searching matches…
                </div>

                <template v-else-if="matchedJobs.length">
                  <div class="max-h-[380px] overflow-y-auto">
                    <a
                      v-for="job in matchedJobs"
                      :key="job.id"
                      href="#"
                      class="flex flex-col gap-1 px-5 py-4 border-b border-gray-50 hover:bg-blue-50/30 transition-colors no-underline group"
                      @click.prevent="goToJob(job)"
                    >
                      <div class="flex justify-between items-start gap-2">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 truncate">
                          {{ job.title }}
                        </p>
                        <span class="text-[0.7rem] font-bold text-green-600 whitespace-nowrap">
                          {{ job.match }}% match
                        </span>
                      </div>
                      <p class="text-[0.75rem] text-gray-500">{{ job.company }}</p>
                    </a>
                    
                  </div>
                  <router-link
                    to="/jobs"
                    class="block text-center py-3 text-xs font-semibold text-gray-600 hover:bg-gray-50 border-t border-gray-100"
                    @click="appsOpen = false"
                  >
                    View all opportunities
                  </router-link>
                </template>

                <div v-else class="px-8 py-10 text-center">
                  <div class="mb-4 flex justify-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                      </svg>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600 font-medium">No matches yet</p>
                  <p class="text-xs text-gray-400 mt-1 mb-5">Complete your profile to unlock recommendations.</p>
                  <router-link
                    to="/profile"
                    class="inline-block text-[0.72rem] font-bold bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition"
                    @click="appsOpen = false"
                  >
                    Enhance Profile
                  </router-link>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Right Side: Profile / Login -->
        <div class="flex items-center gap-3 sm:gap-4 shrink-0">
          <template v-if="auth.isAuthenticated.value">
            <NotificationDropdown />
            <router-link
              to="/profile"
              class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors no-underline"
            >
              <ProfileAvatar 
                :url="auth.state.user?.avatar_url" 
                :name="auth.state.user?.name" 
                size="sm"
              />
              <div class="flex flex-col pr-1">
                <span class="text-xs font-bold text-gray-900 leading-tight truncate max-w-[120px]">
                  {{ auth.state.user?.name?.split(' ')[0] }}
                </span>
                <span class="text-[0.62rem] text-gray-500 font-bold uppercase tracking-widest leading-none mt-0.5">
                  {{ auth.role.value }}
                </span>
              </div>
            </router-link>

            <button
              class="text-[0.85rem] font-semibold text-gray-600 hover:text-red-500 px-2 py-1 transition-colors disabled:opacity-50"
              :disabled="loggingOut"
              @click="handleLogout"
            >
              {{ loggingOut ? '...' : 'Sign out' }}
            </button>
          </template>

          <template v-else>
            <router-link to="/login" class="text-[0.85rem] font-semibold text-gray-600 hover:text-gray-900 px-3 py-2 transition-colors">
              Log in
            </router-link>
            <router-link
              to="/register"
              class="bg-gray-900 text-white text-[0.85rem] font-semibold px-5 py-2 rounded-full hover:bg-gray-700 transition-all shadow-md hover:shadow-lg active:scale-95"
            >
              Get Started
            </router-link>
          </template>

          <!-- Mobile Menu Button (Hamburger) -->
          <button 
            class="md:hidden p-3 text-gray-600 transition-colors hover:bg-gray-100 rounded-lg"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <svg 
              class="w-6 h-6 transition-transform duration-200" 
              :class="{ 'rotate-90': mobileMenuOpen }" 
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            >
              <path v-if="!mobileMenuOpen" d="M3 12h18M3 6h18M3 18h18"/>
              <path v-else d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import ProfileAvatar from './ProfileAvatar.vue'
import NotificationDropdown from './NotificationDropdown.vue'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const scrolled = ref(false)
const loggingOut = ref(false)
const appsOpen = ref(false)
const mobileMenuOpen = ref(false)
const appsMenuRef = ref(null)
const loadingJobs = ref(false)
const matchedJobs = ref([])

const tabs = computed(() => {
  const roles = {
    guest: [
      { path: '/', label: 'Home' },
      { path: '/blogs', label: 'Blog' },
      { path: '/about', label: 'About' },
    ],
    applicant: [
      { path: '/dashboard/applicant', label: 'Dashboard' },
      { path: '/jobs', label: 'Browse Jobs' },
      { path: '/blogs', label: 'Blog' },
      { path: '/profile', label: 'Profile' },
      { path: '/top-matches', label: 'Top Matches'},
      { path: '/interviews', label: 'Interviews'},
    ],
    recruiter: [
      { path: '/dashboard/recruiter', label: 'Dashboard' },
      { path: '/blogs', label: 'Blog' },
      { path: '/profile', label: 'Profile' },
      { path: '/top-matches', label: 'Top Matches'},
      { path: '/interviews', label: 'Interviews'},
    ],
    admin: [
      { path: '/dashboard/admin', label: 'Control Center' },
      { path: '/blogs', label: 'Moderation' },
      { path: '/profile', label: 'Profile' },
    ],
  }
  return roles[auth.role.value] ?? roles.guest
})

const filteredLinks = computed(() => tabs.value)

const initials = computed(() => {
  const name = auth.state.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?'
})

function isActive(path) {
  return route.path === path
}

async function handleLogout() {
  loggingOut.value = true
  try {
    await auth.logout()
    router.push('/login')
  } finally {
    loggingOut.value = false
  }
}

function handleScroll() {
  scrolled.value = window.scrollY > 20
}

function toggleApps() {
  appsOpen.value = !appsOpen.value
  if (appsOpen.value && matchedJobs.value.length === 0) {
    fetchMatches()
  }
}

async function fetchMatches() {
  loadingJobs.value = true
  try {
    const data = await auth.apiFetch('/v1/projects/recommended')
    matchedJobs.value = data
  } catch (err) {
    console.error('Failed to fetch matched jobs:', err)
  } finally {
    loadingJobs.value = false
  }
}

function goToJob(job) {
  appsOpen.value = false
  router.push('/jobs') // or job specific page if exists
}

function handleClickOutside(event) {
  if (appsMenuRef.value && !appsMenuRef.value.contains(event.target)) {
    appsOpen.value = false
  }
}

// Watch route path to close dropdown
watch(() => route.path, () => {
  appsOpen.value = false
  mobileMenuOpen.value = false
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  window.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('mousedown', handleClickOutside)
})
</script>