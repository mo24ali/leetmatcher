<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { dashboardRouteForRole } from '../router/index.js'

const router = useRouter()
const auth   = useAuthStore()

const form = reactive({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
    role:                  'applicant',
})

const error   = ref('')
const loading = ref(false)

async function register() {
    error.value   = ''
    loading.value = true
    try {
        const res = await auth.register({ ...form })
        if (res.user) {
            router.push(dashboardRouteForRole(res.user.role))
        }
    } catch (e) {
        if (e.errors && Object.keys(e.errors).length) {
            const firstKey = Object.keys(e.errors)[0]
            error.value = e.errors[firstKey][0]
        } else {
            error.value = e.message || 'Registration failed. Please try again.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6 font-sans">
    <div class="max-w-[480px] w-full bg-white rounded-3xl border border-gray-200 p-8 sm:p-12 shadow-sm">
      
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Join as Applicant</h2>
        <p class="text-sm text-gray-500 mt-1">Start your journey to the top tech companies</p>
      </div>

      <div
        v-if="error"
        class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-sm text-red-800 animate-in fade-in slide-in-from-left-2"
        role="alert"
      >
        {{ error }}
      </div>

      <form @submit.prevent="register" class="flex flex-col gap-5">
        <div>
          <label for="name" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
          <input
            id="name"
            type="text"
            placeholder="John Doe"
            v-model="form.name"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900
                   outline-none focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-200"
          />
        </div>

        <div>
          <label for="email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email address</label>
          <input
            id="email"
            type="email"
            placeholder="john@example.com"
            v-model="form.email"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900
                   outline-none focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-200"
          />
        </div>

        <div>
          <label for="password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
          <input
            id="password"
            type="password"
            placeholder="••••••••"
            v-model="form.password"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900
                   outline-none focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-200"
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Confirm Password</label>
          <input
            id="password_confirmation"
            type="password"
            placeholder="••••••••"
            v-model="form.password_confirmation"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900
                   outline-none focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-200"
          />
        </div>

        <button
          type="submit"
          class="w-full h-12 bg-gray-900 text-white rounded-xl font-bold tracking-widest uppercase text-sm
                 hover:bg-gray-800 transition-all duration-200 flex items-center justify-center gap-3 disabled:opacity-50
                 shadow-lg hover:shadow-xl active:scale-95 mt-2"
          :disabled="loading"
        >
          <div v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          <span>{{ loading ? 'Creating account…' : 'Register' }}</span>
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
          Already have an account?
          <router-link to="/login" class="text-gray-900 font-bold hover:underline">Sign In</router-link>
        </p>
      </form>
    </div>
  </div>
</template>
