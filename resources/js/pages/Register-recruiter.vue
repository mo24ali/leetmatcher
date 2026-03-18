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
    role:                  'recruiter',
})

const error   = ref('')
const loading = ref(false)

async function register() {
    error.value   = ''
    loading.value = true
    try {
        const user = await auth.register({ ...form })
        router.push(dashboardRouteForRole(user.role))
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
    <div class="page-centered">
        <div class="register-card">
            <div class="header">
                <h2 class="cta-title">Create account</h2>
                <p class="cta-subtitle">Join as a recruiter</p>
            </div>

            <div v-if="error" class="form-error-banner" role="alert">
                {{ error }}
            </div>

            <form @submit.prevent="register" class="register-form">
                <div class="form-group">
                    <label for="name" class="field-label">Full name</label>
                    <input id="name" type="text" placeholder="John Doe" v-model="form.name" class="form-input" required />
                </div>

                <div class="form-group">
                    <label for="email" class="field-label">Email address</label>
                    <input id="email" type="email" placeholder="john@company.com" v-model="form.email" class="form-input" required />
                </div>

                <div class="form-group">
                    <label for="password" class="field-label">Password</label>
                    <input id="password" type="password" placeholder="••••••••" v-model="form.password" class="form-input" required />
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="field-label">Confirm password</label>
                    <input id="password_confirmation" type="password" placeholder="••••••••" v-model="form.password_confirmation" class="form-input" required />
                </div>

                <input type="hidden" v-model="form.role" />

                <button type="submit" class="submit-btn" :disabled="loading">
                    <span v-if="loading" class="btn-spinner"></span>
                    <span>{{ loading ? 'Creating account…' : 'Register' }}</span>
                </button>

                <p class="footer-text">
                    Already have an account?
                    <router-link to="/login" class="login-link">Sign in</router-link>
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