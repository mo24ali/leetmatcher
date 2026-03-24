<template>
  <div class="profile-page container py-12">
    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar-placeholder">
          {{ auth.state.user?.name?.charAt(0) || 'U' }}
        </div>
        <div class="header-info">
          <h1 class="cta-title">{{ auth.state.user?.name || 'User Profile' }}</h1>
          <span class="role-badge" :class="auth.role.value">{{ auth.role.value }}</span>
        </div>
      </div>

      <div class="profile-body">
        <div class="info-group">
          <label class="field-label">Email</label>
          <p class="field-value">{{ auth.state.user?.email || 'N/A' }}</p>
        </div>
        <div class="info-group">
          <label class="field-label">Account Type</label>
          <p class="field-value capitalize">{{ auth.role.value }}</p>
        </div>
        
        <div v-if="auth.role.value === 'applicant'" class="info-section">
          <h3 class="section-title">Resume</h3>
          <!-- Placeholder for resume status -->
          <p class="empty-hint">No resume uploaded yet.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/authStore'
const auth = useAuthStore()
</script>

<style scoped>
.profile-page { min-height: 80vh; display: flex; align-items: flex-start; justify-content: center; }
.profile-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 600px; }
.profile-header { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--gray-100); }
.avatar-placeholder { width: 80px; height: 80px; background: var(--gray-900); color: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; }
.role-badge { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 0.25rem 0.75rem; border-radius: 99px; }
.role-badge.applicant { background: #e0f2fe; color: #0369a1; }
.role-badge.recruiter  { background: #f0fdf4; color: #15803d; }
.role-badge.admin      { background: #fef3c7; color: #b45309; }
.info-group { margin-bottom: 1.5rem; }
.field-value { font-size: 1rem; color: var(--gray-900); font-weight: 500; margin-top: 0.25rem; }
.capitalize { text-transform: capitalize; }
.section-title { font-size: 1.1rem; font-weight: 600; margin: 2rem 0 1rem; }
</style>