<template>
  <header class="minimal-header">
    <div class="container">
      <nav class="nav-container">
        <!-- Logo -->
        <router-link to="/" class="logo logo-link">
          <span class="logo-leet">LEET</span>
          <span class="logo-matcher">MATCHER</span>
        </router-link>

        <!-- Role-based navigation links -->
        <ul class="nav-links">
          <li v-for="link in filteredLinks" :key="link.path">
            <router-link
              :to="link.path"
              class="nav-link"
              :class="{ active: isActive(link.path) }"
            >
              {{ link.label }}
            </router-link>
          </li>
        </ul>

        <!-- Right side actions -->
        <div class="header-actions">
          <router-link v-if="auth.isAuthenticated.value" to="/profile" class="user-badge-link">
            <div class="user-badge">
              <span class="user-name">{{ auth.state.user?.name }}</span>
              <span class="role-chip" :class="auth.role.value">{{ auth.role.value }}</span>
            </div>
          </router-link>

          <button
            v-if="auth.isAuthenticated.value"
            class="logout-btn"
            @click="handleLogout"
            :disabled="loggingOut"
          >
            {{ loggingOut ? 'Signing out…' : 'Logout' }}
          </button>

          <template v-else>
            <router-link to="/login"    class="nav-link">Login</router-link>
            <router-link to="/register" class="nav-btn-register">Register</router-link>
          </template>
        </div>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import { dashboardRouteForRole } from '../router/index.js'

const auth   = useAuthStore()
const route  = useRoute()
const router = useRouter()

const loggingOut = ref(false)

// ─── Role → navigation link definitions ──────────────────────────────────────
const navLinks = {
  guest: [
    { path: '/',       label: 'Home'  },
    { path: '/about',  label: 'About' },
  ],
  applicant: [
    { path: '/dashboard/applicant',        label: 'Dashboard'    },
    { path: '/profile',                    label: 'Profile'      },
    { path: '/dashboard/applicant#browse', label: 'Browse Jobs'  },
  ],
  recruiter: [
    { path: '/dashboard/recruiter',            label: 'Dashboard'       },
    { path: '/profile',                        label: 'Profile'         },
    { path: '/dashboard/recruiter#post',       label: 'Post a Job'      },
  ],
  admin: [
    { path: '/dashboard/admin',             label: 'Dashboard'       },
    { path: '/profile',                     label: 'Profile'         },
    { path: '/dashboard/admin#users',       label: 'Manage Users'    },
  ],
}

const filteredLinks = computed(() => navLinks[auth.role.value] ?? navLinks.guest)

const isActive = (path) => route.path === path.split('#')[0]

async function handleLogout() {
  loggingOut.value = true
  await auth.logout()
  loggingOut.value = false
  router.push('/login')
}
</script>

<style scoped>
.logo-link {
  text-decoration: none;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-badge-link {
  text-decoration: none;
  transition: opacity 0.2s ease;
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
}
.user-badge-link:hover {
  background: var(--gray-50);
}

.user-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-name {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--gray-700);
}

.role-chip {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.15rem 0.5rem;
  border-radius: 99px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.role-chip.applicant { background: #e0f2fe; color: #0369a1; }
.role-chip.recruiter  { background: #f0fdf4; color: #15803d; }
.role-chip.admin      { background: #fef3c7; color: #b45309; }
.role-chip.guest      { background: var(--gray-100); color: var(--gray-600); }

.logout-btn {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--gray-600);
  background: none;
  border: 1px solid var(--gray-200);
  border-radius: 6px;
  padding: 0.4rem 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: var(--font-base);
}
.logout-btn:hover:not(:disabled) {
  border-color: var(--gray-400);
  color: var(--gray-900);
}
.logout-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.nav-btn-register {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--white);
  background: var(--gray-900);
  border-radius: 6px;
  padding: 0.4rem 0.9rem;
  text-decoration: none;
  transition: background 0.2s ease;
}
.nav-btn-register:hover {
  background: var(--gray-800);
}

@media (max-width: 768px) {
  .user-name { display: none; }
  .header-actions { gap: 0.5rem; }
}
</style>