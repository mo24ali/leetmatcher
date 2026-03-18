<template>
  <div class="forbidden-page">
    <div class="forbidden-card">
      <div class="forbidden-icon">🔒</div>
      <h1 class="forbidden-title">403</h1>
      <p class="forbidden-subtitle">Access Denied</p>
      <p class="forbidden-message">
        You don't have permission to view this page.
        This area is restricted to a different user role.
      </p>
      <div class="forbidden-actions">
        <router-link to="/" class="btn btn-outline">Go Home</router-link>
        <button class="btn btn-primary" @click="goToDashboard">My Dashboard</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { dashboardRouteForRole } from '../router/index.js'

const router = useRouter()
const auth   = useAuthStore()

function goToDashboard() {
  router.push(dashboardRouteForRole(auth.role.value))
}
</script>

<style scoped>
.forbidden-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-50);
  font-family: var(--font-base);
  padding: 2rem;
}

.forbidden-card {
  max-width: 440px;
  width: 100%;
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 20px;
  padding: 3rem 2.5rem;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0,0,0,0.05);
}

.forbidden-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.forbidden-title {
  font-size: 4rem;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -0.04em;
  line-height: 1;
  margin: 0 0 0.25rem;
}

.forbidden-subtitle {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--gray-700);
  margin: 0 0 1rem;
}

.forbidden-message {
  font-size: 0.95rem;
  color: var(--gray-500);
  line-height: 1.6;
  margin-bottom: 2rem;
}

.forbidden-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  flex: 1;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-family: var(--font-base);
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.btn-outline {
  border: 1.5px solid var(--gray-300);
  background: transparent;
  color: var(--gray-700);
}
.btn-outline:hover { border-color: var(--gray-600); color: var(--gray-900); }
.btn-primary {
  border: 1.5px solid var(--gray-900);
  background: var(--gray-900);
  color: var(--white);
}
.btn-primary:hover { background: var(--gray-800); }
</style>
