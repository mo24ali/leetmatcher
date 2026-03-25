<template>
  <div class="dashboard-page">
    <section class="dash-hero">
      <div class="dash-hero-inner">
        <div>
          <p class="dash-welcome">Admin Control Panel</p>
          <h1 class="dash-title">{{ auth.state.user?.name }}</h1>
          <p class="dash-subtitle">Platform overview and management tools.</p>
        </div>
        <div class="hero-badge admin">Admin</div>
      </div>
    </section>

    <div class="dash-content">
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon-box"><span>&#128101;</span></div>
          <div class="stat-body">
            <p class="stat-label">Total Users</p>
            <p class="stat-number">{{ stats.users }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-box"><span>&#9646;</span></div>
          <div class="stat-body">
            <p class="stat-label">Total Jobs</p>
            <p class="stat-number">{{ stats.jobs }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon-box"><span>&#9998;</span></div>
          <div class="stat-body">
            <p class="stat-label">Applications</p>
            <p class="stat-number">{{ stats.applications }}</p>
          </div>
        </div>
        <div class="stat-card positive">
          <div class="stat-icon-box success"><span>&#8593;</span></div>
          <div class="stat-body">
            <p class="stat-label">Monthly Growth</p>
            <p class="stat-number">+{{ stats.growth }}%</p>
          </div>
        </div>
      </section>

      <div class="dash-two-col">
        <!-- User Management -->
        <section class="dash-card" id="users">
          <div class="dash-card-header">
            <h2 class="dash-card-title">User Management</h2>
            <p class="dash-card-subtitle">Platform user breakdown by role</p>
          </div>

          <div class="role-breakdown">
            <div v-for="r in roleBreakdown" :key="r.role" class="role-row">
              <div class="role-info">
                <span class="role-dot" :class="r.role"></span>
                <span class="role-name">{{ r.label }}</span>
              </div>
              <div class="role-bar-wrap">
                <div class="role-bar">
                  <div class="role-bar-fill" :class="r.role" :style="{ width: r.pct + '%' }"></div>
                </div>
                <span class="role-count">{{ r.count }}</span>
              </div>
            </div>
          </div>

          <div class="user-list">
            <table class="admin-table">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Action</th></tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td><span class="role-chip" :class="user.role">{{ user.role }}</span></td>
                  <td>{{ user.joined }}</td>
                  <td>
                    <button class="admin-btn danger" @click="removeUser(user)">Remove</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Recent activity -->
        <section class="dash-card">
          <div class="dash-card-header">
            <h2 class="dash-card-title">Recent Activity</h2>
            <p class="dash-card-subtitle">Latest platform events</p>
          </div>
          <div class="activity-feed">
            <div v-for="event in activity" :key="event.id" class="activity-item">
              <div class="activity-dot" :class="event.type"></div>
              <div class="activity-body">
                <p class="activity-text">{{ event.text }}</p>
                <p class="activity-time">{{ event.time }}</p>
              </div>
            </div>
            <p v-if="activity.length === 0" class="empty-hint">No recent activity to display.</p>
          </div>
        </section>
      </div>

      <!-- All job listings -->
      <section class="dash-card" id="listings">
        <div class="dash-card-header">
          <h2 class="dash-card-title">Manage Job Listings</h2>
          <p class="dash-card-subtitle">All jobs posted across the platform</p>
        </div>
        <table class="admin-table">
          <thead>
            <tr><th>Title</th><th>Recruiter</th><th>Applicants</th><th>Status</th><th>Deadline</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr v-for="job in allJobs" :key="job.id">
              <td>{{ job.title }}</td>
              <td>{{ job.recruiter }}</td>
              <td>{{ job.applicants }}</td>
              <td><span class="listing-status" :class="job.status">{{ job.status }}</span></td>
              <td>{{ job.deadline }}</td>
              <td>
                <button class="admin-btn danger" @click="removeJob(job)">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Site settings -->
      <section class="dash-card settings-card" id="settings">
        <div class="dash-card-header">
          <h2 class="dash-card-title">Site Settings</h2>
          <p class="dash-card-subtitle">Platform-level configuration</p>
        </div>
        <div class="settings-grid">
          <div v-for="setting in settings" :key="setting.key" class="setting-row">
            <div class="setting-info">
              <p class="setting-label">{{ setting.label }}</p>
              <p class="setting-desc">{{ setting.desc }}</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" v-model="setting.enabled" />
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/authStore'

const auth = useAuthStore()
const loading = ref(true)

const stats = reactive({ users: 0, jobs: 0, applications: 0, growth: 0 })

const roleBreakdown = ref([
  { role: 'applicant', label: 'Applicants', count: 0, pct: 0 },
  { role: 'recruiter', label: 'Recruiters', count: 0, pct: 0 },
  { role: 'admin',     label: 'Admins',     count: 0, pct: 0 },
])

const users    = ref([])
const allJobs  = ref([])
const activity = ref([])

const settings = reactive([
  { key: 'registration', label: 'Open Registration',   desc: 'Allow new users to register on the platform',  enabled: false },
  { key: 'email_notif',  label: 'Email Notifications', desc: 'Send email alerts for applications and updates', enabled: false },
  { key: 'maintenance',  label: 'Maintenance Mode',    desc: 'Take the platform offline for maintenance',      enabled: false },
  { key: 'cv_parsing',   label: 'CV Parser',           desc: 'Enable automatic CV parsing on upload',          enabled: false },
])

async function loadDashboardData() {
  loading.value = true
  try {
    // const [sData, uData, jData, aData] = await Promise.all([
    //   auth.apiFetch('/v1/admin/stats'),
    //   auth.apiFetch('/v1/admin/users'),
    //   auth.apiFetch('/v1/admin/projects'),
    //   auth.apiFetch('/v1/admin/activity')
    // ])
    // Object.assign(stats, sData)
    // users.value    = uData
    // allJobs.value  = jData
    // activity.value = aData
  } catch (err) {
    console.error('Failed to load admin dashboard data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDashboardData()
})

async function removeUser(user) {
  // await auth.apiFetch(`/v1/admin/users/${user.id}`, { method: 'DELETE' })
  users.value = users.value.filter(u => u.id !== user.id)
}

async function removeJob(job) {
  // await auth.apiFetch(`/v1/admin/projects/${job.id}`, { method: 'DELETE' })
  allJobs.value = allJobs.value.filter(j => j.id !== job.id)
}
</script>

<style scoped>
.dashboard-page { min-height: 100vh; background: var(--gray-50); font-family: var(--font-base); }

.dash-hero { background: var(--white); border-bottom: 1px solid var(--gray-200); padding: 2.5rem 0; }
.dash-hero-inner {
  max-width: 1200px; margin: 0 auto; padding: 0 2rem;
  display: flex; justify-content: space-between; align-items: center;
}
.dash-welcome  { font-size: 0.85rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 0.2rem; }
.dash-title    { font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; color: var(--gray-900); margin: 0 0 0.4rem; }
.dash-subtitle { font-size: 0.95rem; color: var(--gray-500); margin: 0; }

.hero-badge { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.4rem 1rem; border-radius: 99px; }
.hero-badge.admin { background: #fef3c7; color: #b45309; }

.dash-content { max-width: 1200px; margin: 0 auto; padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; }

.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; }
@media(max-width:900px){ .stats-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px){ .stats-grid { grid-template-columns: 1fr; } }

.stat-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 14px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; transition: box-shadow 0.2s; }
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.stat-card.positive { border-left: 3px solid #22c55e; }
.stat-icon-box { width: 40px; height: 40px; background: var(--gray-100); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--gray-500); font-size: 1rem; }
.stat-icon-box.success { background: #dcfce7; color: #16a34a; }
.stat-label  { font-size: 0.8rem; color: var(--gray-500); margin: 0 0 0.25rem; text-transform: uppercase; letter-spacing: 0.04em; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: var(--gray-900); margin: 0; line-height: 1; }

.dash-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
@media(max-width:900px){ .dash-two-col { grid-template-columns: 1fr; } }

.dash-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 16px; padding: 1.75rem; }
.dash-card-header { margin-bottom: 1.5rem; }
.dash-card-title  { font-size: 1.1rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.3rem; }
.dash-card-subtitle { font-size: 0.85rem; color: var(--gray-500); margin: 0; }

.role-breakdown { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
.role-row { display: flex; align-items: center; gap: 1rem; }
.role-info { display: flex; align-items: center; gap: 0.5rem; min-width: 90px; }
.role-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.role-dot.applicant { background: #0ea5e9; }
.role-dot.recruiter  { background: #22c55e; }
.role-dot.admin      { background: #f59e0b; }
.role-name { font-size: 0.85rem; color: var(--gray-700); font-weight: 500; }
.role-bar-wrap { flex: 1; display: flex; align-items: center; gap: 0.75rem; }
.role-bar { flex: 1; height: 8px; background: var(--gray-100); border-radius: 4px; overflow: hidden; }
.role-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
.role-bar-fill.applicant { background: #0ea5e9; }
.role-bar-fill.recruiter  { background: #22c55e; }
.role-bar-fill.admin      { background: #f59e0b; }
.role-count { font-size: 0.8rem; color: var(--gray-500); min-width: 35px; text-align: right; }

.admin-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.admin-table th { text-align: left; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--gray-500); padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--gray-200); }
.admin-table td { padding: 0.75rem; border-bottom: 1px solid var(--gray-100); color: var(--gray-800); }
.admin-table tr:last-child td { border-bottom: none; }

.role-chip { font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 99px; text-transform: uppercase; letter-spacing: 0.04em; }
.role-chip.applicant { background: #e0f2fe; color: #0369a1; }
.role-chip.recruiter  { background: #dcfce7; color: #15803d; }
.role-chip.admin      { background: #fef3c7; color: #b45309; }

.admin-btn { font-size: 0.78rem; font-weight: 500; background: none; border: 1px solid var(--gray-300); border-radius: 6px; padding: 0.25rem 0.6rem; cursor: pointer; font-family: var(--font-base); color: var(--gray-700); transition: all 0.15s; }
.admin-btn.danger    { border-color: #fca5a5; color: #b91c1c; }
.admin-btn.danger:hover { background: #ef4444; color: var(--white); border-color: #ef4444; }

.listing-status { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 99px; }
.listing-status.open   { background: #dcfce7; color: #16a34a; }
.listing-status.closed { background: #f3f4f6; color: #6b7280; }

.empty-hint { font-size: 0.875rem; color: var(--gray-400); text-align: center; padding: 2rem 0; margin: 0; }

.activity-feed { display: flex; flex-direction: column; gap: 1rem; }
.activity-item { display: flex; gap: 0.75rem; align-items: flex-start; }
.activity-dot  { width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; flex-shrink: 0; }
.activity-dot.user   { background: #0ea5e9; }
.activity-dot.job    { background: #22c55e; }
.activity-dot.apply  { background: #a855f7; }
.activity-dot.system { background: #f59e0b; }
.activity-text { font-size: 0.875rem; color: var(--gray-800); margin: 0 0 0.15rem; }
.activity-time { font-size: 0.75rem; color: var(--gray-500); margin: 0; }

.settings-grid { display: flex; flex-direction: column; gap: 0; }
.setting-row  { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--gray-100); }
.setting-row:last-child { border-bottom: none; }
.setting-label { font-size: 0.9rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.2rem; }
.setting-desc  { font-size: 0.8rem; color: var(--gray-500); margin: 0; }

.toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
  position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
  background: var(--gray-300); border-radius: 24px; transition: 0.2s;
}
.toggle-slider::before {
  content: ''; position: absolute; height: 18px; width: 18px;
  left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.2s;
}
input:checked + .toggle-slider { background: var(--gray-900); }
input:checked + .toggle-slider::before { transform: translateX(20px); }
</style>
