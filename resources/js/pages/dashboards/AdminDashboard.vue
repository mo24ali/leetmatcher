<template>
  <div class="dashboard-page">
    <!-- Navigation Sidebar/Overlay could be added, but following current style -->
    
    <section class="dash-hero">
      <div class="dash-hero-inner">
        <div>
          <p class="dash-welcome">Admin Control Panel</p>
          <h1 class="dash-title">{{ auth.state.user?.name }}</h1>
          <p class="dash-subtitle">Real-time platform oversight and moderation.</p>
        </div>
        <div class="hero-badge admin">System Administrator</div>
      </div>
    </section>

    <div class="dash-content">
      <!-- Top Stats Section -->
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
            <p class="stat-label">Growth (MoM)</p>
            <p class="stat-number">+{{ stats.growth }}%</p>
          </div>
        </div>
      </section>

      <div class="dash-two-col">
        <!-- Module 1: User Management & Moderation -->
        <section class="dash-card" id="users">
          <div class="dash-card-header flex-header">
            <div>
              <h2 class="dash-card-title">User Management</h2>
              <p class="dash-card-subtitle">Moderation and account operations</p>
            </div>
          </div>

          <div class="user-list">
            <table class="admin-table">
              <thead>
                <tr><th>User</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>
                    <div class="user-cell">
                      <span class="u-name">{{ user.name }}</span>
                      <span class="u-email">{{ user.email }}</span>
                    </div>
                  </td>
                  <td><span class="role-chip" :class="user.role">{{ user.role }}</span></td>
                  <td>
                    <span v-if="user.warnings > 0" class="warn-badge">{{ user.warnings }} Warnings</span>
                    <span v-else class="status-ok">Active</span>
                  </td>
                  <td>{{ user.joined }}</td>
                  <td>
                    <div class="action-btns">
                      <button @click="openModeration(user)" class="admin-btn">Warn</button>
                      <button @click="confirmDeleteUser(user)" class="admin-btn danger">Delete</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Module 2: Statistics & Analytics (Role Breakdown) -->
        <section class="dash-card">
           <div class="dash-card-header">
            <h2 class="dash-card-title">Platform Demographics</h2>
            <p class="dash-card-subtitle">User distribution by role</p>
          </div>
          <div class="role-breakdown">
            <div style="height: 250px; display: flex; justify-content: center; align-items: center;">
              <Pie v-if="chartData.datasets && chartData.datasets.length" :data="chartData" :options="chartOptions" />
              <p v-else class="empty-hint">Loading Demographics...</p>
            </div>
          </div>
        </section>
      </div>

      <!-- Module 3: System Logs Monitoring -->
      <section class="dash-card" id="logs">
        <div class="dash-card-header flex-header">
          <div>
            <h2 class="dash-card-title">System Audit Logs</h2>
            <p class="dash-card-subtitle">Complete traceability of platform operations</p>
          </div>
          <div class="header-actions">
            <input v-model="logSearch" placeholder="Filter logs..." class="admin-input" />
          </div>
        </div>
        <div class="log-viewer">
          <table class="admin-table sticky-header">
            <thead>
              <tr><th>Admin</th><th>Event</th><th>Severity</th><th>Timestamp</th><th>Details</th></tr>
            </thead>
            <tbody>
              <tr v-for="log in filteredLogs" :key="log.id" :class="'severity-' + log.severity">
                <td class="font-bold">{{ log.admin }}</td>
                <td><span class="log-event">{{ log.event }}</span></td>
                <td><span class="sev-chip" :class="log.severity">{{ log.severity }}</span></td>
                <td class="text-gray-500">{{ log.time }}</td>
                <td>
                   <button @click="viewLogDetails(log)" class="text-btn">View JSON</button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="filteredLogs.length === 0" class="empty-hint">No logs matching criteria.</p>
        </div>
      </section>

      <!-- Module 4: Job Listings Overwatch -->
      <section class="dash-card" id="listings">
        <div class="dash-card-header">
          <h2 class="dash-card-title">Job Postings Overwatch</h2>
          <p class="dash-card-subtitle">Platform-wide listing management</p>
        </div>
        <table class="admin-table">
          <thead>
            <tr><th>Title</th><th>Recruiter</th><th>Reach</th><th>Status</th><th>Deadline</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr v-for="job in allJobs" :key="job.id">
              <td><span class="font-bold">{{ job.title }}</span></td>
              <td>{{ job.recruiter }}</td>
              <td>{{ job.applicants }} candidates</td>
              <td><span class="listing-status" :class="job.status">{{ job.status }}</span></td>
              <td>{{ job.deadline }}</td>
              <td>
                <button class="admin-btn danger" @click="confirmDeleteJob(job)">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Module 5: Blog Moderation -->
      <section class="dash-card" id="blogs">
        <div class="dash-card-header flex-header">
          <div>
            <h2 class="dash-card-title">Blog Moderation</h2>
            <p class="dash-card-subtitle">Manage community posts and visibility</p>
          </div>
        </div>
        <div class="blog-list">
          <table class="admin-table">
            <thead>
              <tr><th>Post Title</th><th>Author</th><th>Status</th><th>Visibility</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="post in blogPosts" :key="post.id">
                <td><span class="font-bold">{{ post.title }}</span></td>
                <td>{{ post.author?.name }} ({{ post.author?.role }})</td>
                <td><span :class="['role-chip', post.moderation_status]">{{ post.moderation_status }}</span></td>
                <td><span :class="['listing-status', post.visibility]">{{ post.visibility }}</span></td>
                <td>{{ new Date(post.created_at).toLocaleDateString() }}</td>
                <td>
                  <div class="action-btns">
                    <button @click="openBlogModeration(post)" class="admin-btn">Moderate</button>
                    <button @click="deleteBlogPost(post.id)" class="admin-btn danger">Delete</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-if="blogPosts.length === 0" class="empty-hint">No blog posts found.</p>
        </div>
      </section>
    </div>

    <!-- Modals -->
    <transition name="fade">
      <div v-if="modals.moderation" class="modal-overlay" @click.self="modals.moderation = false">
        <div class="admin-modal">
          <h3>Modulate User: {{ selectedUser?.name }}</h3>
          <p class="modal-hint">Review history and issue system warnings or flags.</p>
          
          <div class="user-history" v-if="selectedUser?.history && selectedUser.history.length > 0">
            <h4 class="history-title">Warning History</h4>
            <ul class="history-list">
              <li v-for="(warn, idx) in selectedUser.history" :key="idx" class="history-item">
                <span class="warn-type">{{ warn.type.toUpperCase() }}</span> - 
                <span class="warn-level">Lvl {{ warn.level }}</span>
                <p class="warn-reason">{{ warn.reason }}</p>
                <span class="warn-date">{{ warn.date }}</span>
              </li>
            </ul>
          </div>
          <div v-else class="empty-hint text-sm mb-4">No prior infractions.</div>

          
          <div class="modal-form">
            <div class="form-group">
              <label>Warning Type</label>
              <select v-model="forms.moderation.type">
                <option value="behavior">Behavioral Concern</option>
                <option value="spam">Spam / Excessive Posting</option>
                <option value="violations">Terms Violation</option>
                <option value="fake">Fake Profile</option>
              </select>
            </div>
            <div class="form-group">
              <label>Escalation Level</label>
              <input type="number" v-model="forms.moderation.level" min="1" max="5" />
            </div>
            <div class="form-group">
              <label>Reason / Details</label>
              <textarea v-model="forms.moderation.reason" placeholder="Details for audit logs..."></textarea>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="admin-btn" @click="modals.moderation = false">Cancel</button>
            <button class="admin-btn danger" @click="submitModeration">Issue Warning</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Blog Moderation Modal -->
    <transition name="fade">
      <div v-if="modals.blog" class="modal-overlay" @click.self="modals.blog = false">
        <div class="admin-modal">
          <h3>Moderate Post: {{ selectedPost?.title }}</h3>
          <p class="modal-hint">Review and adjust post status or visibility.</p>
          
          <div class="modal-form">
            <div class="form-group">
              <label>Moderation Status</label>
              <select v-model="forms.blog.moderation_status">
                <option value="approved">Approved</option>
                <option value="pending">Pending Review</option>
                <option value="rejected">Rejected / Hidden</option>
              </select>
            </div>
            <div class="form-group">
              <label>Visibility</label>
              <select v-model="forms.blog.visibility">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="draft">Draft</option>
              </select>
            </div>
            <div class="form-group">
              <label>Reason for Action</label>
              <textarea v-model="forms.blog.reason" placeholder="Explain the moderation decision..."></textarea>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="admin-btn" @click="modals.blog = false">Cancel</button>
            <button class="admin-btn success-btn" @click="submitBlogModeration">Apply Changes</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Generic Confirm & Detail Modals can follow similar pattern -->
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed, watch } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

const auth = useAuthStore()
const loading = ref(true)
const logSearch = ref('')

const stats = reactive({ 
  users: 0, 
  jobs: 0, 
  applications: 0, 
  growth: 0,
  roleBreakdown: {} 
})

const users    = ref([])
const allJobs  = ref([])
const blogPosts = ref([])
const auditLogs = ref([])

const modals = reactive({ moderation: false, logs: false, confirm: false, blog: false })
const selectedUser = ref(null)
const selectedPost = ref(null)

const forms = reactive({
  moderation: { type: 'behavior', reason: '', level: 1 },
  blog: { moderation_status: 'approved', visibility: 'public', reason: '' }
})

async function loadDashboardData() {
  loading.value = true
  try {
     const [sData, uData, jData, lData, bData] = await Promise.all([
       auth.apiFetch('/v1/admin/stats'),
       auth.apiFetch('/v1/admin/users'),
       auth.apiFetch('/v1/admin/projects'),
       auth.apiFetch('/v1/admin/logs'),
       auth.apiFetch('/v1/admin/blog-posts')
     ])
     Object.assign(stats, sData)
     users.value    = uData
     allJobs.value  = jData
     auditLogs.value = lData
     blogPosts.value = bData.data || bData
  } catch (err) {
    console.error('Admin Load Error:', err)
  } finally {
    loading.value = false
  }
}

const filteredLogs = computed(() => {
  if (!logSearch.value) return auditLogs.value
  const q = logSearch.value.toLowerCase()
  return auditLogs.value.filter(l => 
    l.admin.toLowerCase().includes(q) || 
    l.event.toLowerCase().includes(q)
  )
})

const chartData = ref({ labels: [], datasets: [] })
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } }
  }
}

onMounted(() => {
  loadDashboardData()
})

watch(() => stats.roleBreakdown, (newBreakdown) => {
  if (Object.keys(newBreakdown).length > 0) {
    const labels = Object.keys(newBreakdown)
    const data = Object.values(newBreakdown)
    chartData.value = {
      labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
      datasets: [{
        backgroundColor: ['#0ea5e9', '#22c55e', '#f59e0b'],
        borderWidth: 0,
        data: data
      }]
    }
  }
}, { deep: true })

// User Management Actions
function openModeration(user) {
  selectedUser.value = user
  forms.moderation = { type: 'behavior', reason: '', level: 1 }
  modals.moderation = true
}

async function submitModeration() {
  if (!forms.moderation.reason) return alert('Reason required')
  try {
     await auth.apiFetch(`/v1/admin/users/${selectedUser.value.id}/warn`, {
       method: 'POST',
       body: JSON.stringify(forms.moderation)
     })
     modals.moderation = false
     loadDashboardData() // Refresh
  } catch (err) {
    alert('Failed to apply moderation')
  }
}

async function confirmDeleteUser(user) {
  if (!confirm(`Are you sure you want to permanently delete user ${user.name}? This action is irreversible and will be logged.`)) return
  try {
    await auth.apiFetch(`/v1/admin/users/${user.id}`, { method: 'DELETE' })
    users.value = users.value.filter(u => u.id !== user.id)
  } catch (err) {
    alert('Delete failed')
  }
}

function viewLogDetails(log) {
    alert(JSON.stringify(log.metadata, null, 2))
}

// Blog Moderation Actions
function openBlogModeration(post) {
  selectedPost.value = post
  forms.blog = { 
    moderation_status: post.moderation_status, 
    visibility: post.visibility, 
    reason: '' 
  }
  modals.blog = true
}

async function submitBlogModeration() {
  try {
    await auth.apiFetch(`/v1/admin/blog-posts/${selectedPost.value.id}/moderate`, {
      method: 'PATCH',
      body: JSON.stringify(forms.blog)
    })
    modals.blog = false
    loadDashboardData()
  } catch (err) {
    alert('Failed to moderate blog post')
  }
}

async function deleteBlogPost(id) {
  if (!confirm('Are you sure you want to delete this blog post?')) return
  try {
    await auth.apiFetch(`/v1/admin/blog-posts/${id}`, { method: 'DELETE' })
    blogPosts.value = blogPosts.value.filter(p => p.id !== id)
  } catch (err) {
    alert('Failed to delete blog post')
  }
}
</script>

<style scoped>
.dashboard-page { min-height: 100vh; background: #f8fafc; color: #1e293b; font-family: 'Inter', sans-serif; }

/* Hero */
.dash-hero { background: white; border-bottom: 1px solid #e2e8f0; padding: 2.5rem 0; }
.dash-hero-inner { max-width: 1300px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
.dash-welcome { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.1em; margin-bottom: 0.25rem; }
.dash-title { font-size: 2.25rem; font-weight: 900; letter-spacing: -0.04em; margin: 0; }
.dash-subtitle { color: #94a3b8; font-size: 1rem; }
.hero-badge { background: #fef3c7; color: #b45309; padding: 0.4rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }

/* Layout */
.dash-content { max-width: 1300px; margin: 0 auto; padding: 2rem; display: flex; flex-direction: column; gap: 2rem; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
.stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; }
.stat-icon-box { width: 3.5rem; height: 3.5rem; background: #f1f5f9; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }

/* Table Overrides */
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th { text-align: left; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #64748b; padding: 1rem; border-bottom: 2px solid #f1f5f9; }
.admin-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

.user-cell { display: flex; flex-direction: column; }
.u-name { font-weight: 700; color: #0f172a; }
.u-email { font-size: 0.75rem; color: #64748b; }

.role-chip { padding: 0.25rem 0.6rem; border-radius: 0.5rem; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
.role-chip.applicant { background: #e0f2fe; color: #0369a1; }
.role-chip.recruiter { background: #dcfce7; color: #16a34a; }
.role-chip.admin { background: #fef3c7; color: #92400e; }

/* Buttons */
.admin-btn { padding: 0.5rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: white; font-weight: 700; font-size: 0.75rem; transition: 0.2s; cursor: pointer; }
.admin-btn:hover { background: #f8fafc; }
.admin-btn.danger { color: #dc2626; border-color: #fecaca; }
.admin-btn.danger:hover { background: #ef4444; color: white; border-color: #ef4444; }
.success-btn { background: #16a34a; color: white; border: none; font-weight: 700; padding: 0.5rem 1rem; border-radius: 0.75rem; cursor: pointer; transition: 0.2s; }
.success-btn:hover { background: #15803d; }

/* Log Viewer */
.log-viewer { max-height: 500px; overflow-y: auto; border-radius: 1rem; border: 1px solid #f1f5f9; }
.severity-warning { background: #fffbeb; }
.severity-error { background: #fef2f2; }
.sev-chip { padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.6rem; font-weight: 900; text-transform: uppercase; }
.sev-chip.info { background: #e0f2fe; color: #0369a1; }
.sev-chip.warning { background: #fef3c7; color: #92400e; }
.sev-chip.error { background: #fee2e2; color: #991b1b; }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; }
.admin-modal { background: white; border-radius: 2rem; width: 100%; max-width: 500px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
.modal-form { display: flex; flex-direction: column; gap: 1.5rem; margin: 2rem 0; }
.form-group label { display: block; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem; }
.form-group select, .form-group textarea, .form-group input { width: 100%; padding: 1rem; border-radius: 1rem; border: 1px solid #e2e8f0; font-family: inherit; }

/* History */
.user-history { margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0; }
.history-title { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin: 0 0 1rem; }
.history-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem; max-height: 150px; overflow-y: auto; }
.history-item { font-size: 0.8rem; }
.warn-type { font-weight: 800; color: #dc2626; }
.warn-level { background: #fee2e2; color: #991b1b; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 800; font-size: 0.65rem; }
.warn-reason { margin: 0.25rem 0 0; color: #475569; }
.warn-date { display: block; margin-top: 0.25rem; font-size: 0.7rem; color: #94a3b8; }

.status-ok { color: #16a34a; font-weight: 700; font-size: 0.75rem; }
.warn-badge { background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }

.dash-card { background: white; border: 1px solid #e2e8f0; border-radius: 1.5rem; padding: 2rem; }
.flex-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.admin-input { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; width: 250px; }

.dash-two-col { display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
