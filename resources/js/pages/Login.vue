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
        const user = await auth.login(form.email, form.password)
        const redirect = route.query.redirect || dashboardRouteForRole(user.role)
        router.push(redirect)
    } catch (e) {
        error.value = e.message || 'Login failed. Please try again.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="page-centered">
        <div class="register-card">
            <div class="header">
                <h2 class="cta-title">Welcome Back</h2>
                <p class="cta-subtitle">Enter your credentials to access your account</p>
            </div>

            <div v-if="error" class="form-error-banner" role="alert">
                {{ error }}
            </div>

            <form @submit.prevent="login" class="register-form">
                <div class="form-group">
                    <label for="email" class="field-label">Email address</label>
                    <input
                        id="email"
                        type="email"
                        placeholder="john@example.com"
                        v-model="form.email"
                        class="form-input"
                        required
                        autocomplete="email"
                    />
                </div>

                <div class="form-group">
                    <label for="password" class="field-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        placeholder="••••••••"
                        v-model="form.password"
                        class="form-input"
                        required
                        autocomplete="current-password"
                    />
                </div>

                <button type="submit" class="submit-btn" :disabled="loading">
                    <span v-if="loading" class="btn-spinner"></span>
                    <span>{{ loading ? 'Signing in…' : 'Sign In' }}</span>
                </button>

                <p class="footer-text">
                    Don't have an account?
                    <router-link to="/register" class="login-link">Register</router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<style scoped>
.form-error-banner {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 3px solid #ef4444;
    color: #b91c1c;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
}
.submit-btn { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>