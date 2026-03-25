<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { dashboardRouteForRole } from '../router/index.js'

const router = useRouter()
const route  = useRoute()
const auth   = useAuthStore()

const form = reactive({
    email:    '',
    password: '',
})

const error   = ref('')
const loading = ref(false)

async function login() {
    error.value   = ''
    loading.value = true
    try {
        const res = await auth.login(form.email, form.password)
        if (res.user) {
            const redirect = route.query.redirect || dashboardRouteForRole(res.user.role)
            router.push(redirect)
        }
    } catch (e) {
        error.value = e.message || 'Login failed. Please check your credentials.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6 font-sans">
    <div class="max-w-[420px] w-full bg-white rounded-3xl border border-gray-200 p-8 sm:p-10 shadow-sm">
      
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Welcome Back</h2>
        <p class="text-sm text-gray-500 mt-1">Access your account to continue matching</p>
      </div>

      <div
        v-if="error"
        class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-sm text-red-800 animate-in fade-in slide-in-from-left-2"
        role="alert"
      >
        {{ error }}
      </div>

      <form @submit.prevent="login" class="flex flex-col gap-5">
        <div>
          <label for="email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
          <input
            id="email"
            type="email"
            placeholder="john@example.com"
            v-model="form.email"
            required
            autocomplete="email"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900
                   outline-none focus:bg-white focus:border-gray-900 focus:ring-4 focus:ring-gray-100 transition-all duration-200"
          />
        </div>

        <div class="relative">
          <label for="password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
          <input
            id="password"
            type="password"
            placeholder="••••••••"
            v-model="form.password"
            required
            autocomplete="current-password"
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
          <span>{{ loading ? 'Signing in…' : 'Sign In' }}</span>
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
          Don't have an account?
          <router-link to="/register" class="text-gray-900 font-bold hover:underline">Register</router-link>
        </p>
      </form>
    </div>
  </div>
</template>