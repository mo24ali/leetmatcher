<template>
  <div class="dashboard-page">
    <!-- Hero -->
    <section class="dash-hero">
      <div class="dash-hero-inner">
        <div>
          <p class="dash-welcome">Recruiter Portal</p>
          <h1 class="dash-title">{{ auth.state.user?.name }} 👋</h1>
          <p class="dash-subtitle">Manage your job postings and discover top talent.</p>
        </div>
        <div class="hero-badge recruiter">Recruiter</div>
      </div>
    </section>

    <div class="dash-content">
      <!-- Stats row -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📌</div>
          <div class="stat-body">
            <p class="stat-label">Active Listings</p>
            <p class="stat-number">{{ stats.activeListings }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-body">
            <p class="stat-label">Total Applicants</p>
            <p class="stat-number">{{ stats.totalApplicants }}</p>
          </div>
        </div>
        <div class="stat-card positive">
          <div class="stat-icon">✅</div>
          <div class="stat-body">
            <p class="stat-label">Interviews Scheduled</p>
            <p class="stat-number">{{ stats.interviews }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🎯</div>
          <div class="stat-body">
            <p class="stat-label">Positions Filled</p>
            <p class="stat-number">{{ stats.filled }}</p>
          </div>
        </div>
      </section>

      <div class="dash-two-col">
        <!-- Post a Job form -->
        <section class="dash-card" id="post">
          <div class="dash-card-header">
            <h2 class="dash-card-title">📝 Post a New Job</h2>
            <p class="dash-card-subtitle">Create a listing to attract top candidates</p>
          </div>

          <div v-if="postSuccess" class="success-banner">
            ✅ Job posted successfully!
          </div>
          <div v-if="postError" class="error-banner">{{ postError }}</div>

          <form @submit.prevent="postJob" class="post-form">
            <div class="form-group">
              <label class="field-label">Job Title</label>
              <input v-model="jobForm.title" type="text" class="form-input" placeholder="e.g. Senior Frontend Engineer" required />
            </div>
            <div class="form-group">
              <label class="field-label">Description</label>
              <textarea v-model="jobForm.description" class="form-input form-textarea" placeholder="Describe the role, requirements, and expectations…" required></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="field-label">Deadline</label>
                <input v-model="jobForm.deadline" type="date" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="field-label">Status</label>
                <select v-model="jobForm.status" class="form-input">
                  <option value="open">Open</option>
                  <option value="closed">Closed</option>
                </select>
              </div>
            </div>
            <button type="submit" class="upload-btn" :disabled="posting">
              <span v-if="posting" class="btn-spinner"></span>
              <span>{{ posting ? 'Posting…' : 'Post Job' }}</span>
            </button>
          </form>
        </section>

        <!-- My Listings -->
        <section class="dash-card" id="listings">
          <div class="dash-card-header">
            <h2 class="dash-card-title">📋 My Listings</h2>
            <p class="dash-card-subtitle">Jobs you've posted on LeetMatcher</p>
          </div>

          <div class="listing-list">
            <div v-for="listing in listings" :key="listing.id" class="listing-card">
              <div class="listing-top">
                <div>
                  <p class="listing-title">{{ listing.title }}</p>
                  <p class="listing-meta">Posted {{ listing.posted }} · Deadline {{ listing.deadline }}</p>
                </div>
                <div class="listing-right">
                  <span class="listing-status" :class="listing.status">{{ listing.status }}</span>
                  <span class="listing-count">{{ listing.applicants }} applicants</span>
                </div>
              </div>
              <div class="listing-actions">
                <button class="listing-btn" id="applicants" @click="viewApplicants(listing)">View Applicants →</button>
                <button class="listing-btn danger" @click="closeListing(listing)">Close</button>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Applicants panel -->
      <section v-if="selectedListing" class="dash-card applicants-panel" id="applicants">
        <div class="dash-card-header">
          <h2 class="dash-card-title">👤 Applicants for "{{ selectedListing.title }}"</h2>
          <button class="close-panel" @click="selectedListing = null">✕ Close</button>
        </div>
        <table class="applicants-table">
          <thead>
            <tr>
              <th>Name</th><th>Email</th><th>Applied</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="app in applicants" :key="app.id">
              <td>{{ app.name }}</td>
              <td>{{ app.email }}</td>
              <td>{{ app.applied }}</td>
              <td><span class="app-status" :class="app.status">{{ app.status }}</span></td>
              <td>
                <button class="listing-btn small">Review</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/authStore'

const auth = useAuthStore()
const loading = ref(true)

// Recruiter statistics (initialized to zero)
// These will be populated by an API call to a recruiter-specific stats endpoint
const stats = reactive({ activeListings: 0, totalApplicants: 0, interviews: 0, filled: 0 })

// ─── Job form ─────────────────────────────────────────────────────────────────
const jobForm = reactive({ title: '', description: '', deadline: '', status: 'open' })
const posting     = ref(false)
const postSuccess = ref(false)
const postError   = ref('')

async function postJob() {
  postSuccess.value = false
  postError.value   = ''
  posting.value     = true
  
  try {
    // API call to create a new job listing
    const res = await auth.apiFetch('/v1/projects', {
      method: 'POST',
      body: JSON.stringify({
        title: jobForm.title,
        description: jobForm.description,
        deadline: jobForm.deadline,
        status: jobForm.status
      })
    })
    
    // Add the new listing to the local list
    listings.value.unshift(res)
    
    // Reset form
    jobForm.title = ''; jobForm.description = ''; jobForm.deadline = ''; jobForm.status = 'open'
    postSuccess.value = true
    setTimeout(() => postSuccess.value = false, 3000)
  } catch (err) {
    postError.value = err.message || 'Failed to post job. Please try again.'
  } finally {
    posting.value = false
  }
}

// ─── Listings ─────────────────────────────────────────────────────────────────
// List of jobs posted by this recruiter
const listings = ref([])

async function closeListing(listing) {
  try {
    // API call to update listing status
    // await auth.apiFetch(`/v1/projects/${listing.id}`, {
    //   method: 'PATCH',
    //   body: JSON.stringify({ status: 'closed' })
    // })
    listing.status = 'closed'
  } catch (err) {
    console.error('Failed to close listing:', err)
  }
}

// ─── Applicants panel ─────────────────────────────────────────────────────────
const selectedListing = ref(null)
const applicants = ref([]) // Populated when a listing is selected

/**
 * Fetch recruiter dashboard overview data
 */
async function loadRecruiterData() {
  loading.value = true
  try {
    // const [sData, lData] = await Promise.all([
    //   auth.apiFetch('/v1/recruiter/stats'),
    //   auth.apiFetch('/v1/recruiter/listings')
    // ])
    // stats.activeListings = sData.active_count
    // listings.value = lData
    
    console.log('Recruiter data fetching logic ready for backend implementation.')
  } catch (err) {
    console.error('Failed to load recruiter data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadRecruiterData()
})

async function viewApplicants(listing) {
  selectedListing.value = listing
  applicants.value = []
  try {
    // Fetch applicants for the specific listing
    // applicants.value = await auth.apiFetch(`/v1/projects/${listing.id}/applications`)
  } catch (err) {
    console.error('Failed to load applicants:', err)
  }
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
.hero-badge.recruiter { background: #f0fdf4; color: #15803d; }

.dash-content { max-width: 1200px; margin: 0 auto; padding: 2rem; }

.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 2rem; }
@media(max-width:900px){ .stats-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px){ .stats-grid { grid-template-columns: 1fr; } }

.stat-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 14px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; }
.stat-card.positive { border-left: 3px solid #22c55e; }
.stat-icon   { font-size: 1.75rem; }
.stat-label  { font-size: 0.8rem; color: var(--gray-500); margin: 0 0 0.25rem; text-transform: uppercase; letter-spacing: 0.04em; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: var(--gray-900); margin: 0; line-height: 1; }

.dash-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
@media(max-width:900px){ .dash-two-col { grid-template-columns: 1fr; } }

.dash-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: 16px; padding: 1.75rem; }
.dash-card-header { margin-bottom: 1.5rem; }
.dash-card-title  { font-size: 1.1rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.3rem; }
.dash-card-subtitle { font-size: 0.85rem; color: var(--gray-500); margin: 0; }

/* ── Post form ── */
.post-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width:640px){ .form-row { grid-template-columns: 1fr; } }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-textarea { resize: vertical; min-height: 100px; }

.success-banner { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.9rem; margin-bottom: 1rem; }
.error-banner   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;  border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.9rem; margin-bottom: 1rem; }

.upload-btn {
  width: 100%; padding: 0.9rem; background: var(--gray-900); color: var(--white);
  border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 500; cursor: pointer;
  transition: background 0.2s; font-family: var(--font-base);
  display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.upload-btn:hover:not(:disabled) { background: var(--gray-800); }
.upload-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Listings ── */
.listing-list { display: flex; flex-direction: column; gap: 0.75rem; }
.listing-card { border: 1px solid var(--gray-200); border-radius: 10px; padding: 1rem 1.25rem; }
.listing-top  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; }
.listing-title { font-size: 0.95rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.2rem; }
.listing-meta  { font-size: 0.8rem; color: var(--gray-500); margin: 0; }
.listing-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem; }
.listing-status { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 99px; }
.listing-status.open   { background: #dcfce7; color: #16a34a; }
.listing-status.closed { background: #f3f4f6; color: #6b7280; }
.listing-count { font-size: 0.8rem; color: var(--gray-500); }
.listing-actions { display: flex; gap: 0.5rem; }
.listing-btn {
  font-size: 0.8rem; font-weight: 500; background: none; border: 1px solid var(--gray-300);
  border-radius: 6px; padding: 0.3rem 0.7rem; cursor: pointer; transition: all 0.15s;
  font-family: var(--font-base); color: var(--gray-700);
}
.listing-btn:hover   { background: var(--gray-900); color: var(--white); border-color: var(--gray-900); }
.listing-btn.danger  { border-color: #fca5a5; color: #b91c1c; }
.listing-btn.danger:hover { background: #ef4444; color: var(--white); border-color: #ef4444; }
.listing-btn.small { padding: 0.2rem 0.5rem; font-size: 0.75rem; }

/* ── Applicants panel ── */
.applicants-panel { margin-top: 0; }
.close-panel { background: none; border: none; cursor: pointer; color: var(--gray-500); font-size: 0.9rem; }
.close-panel:hover { color: var(--gray-900); }

.applicants-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.applicants-table th { text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--gray-500); padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--gray-200); }
.applicants-table td { padding: 0.75rem; border-bottom: 1px solid var(--gray-100); color: var(--gray-800); }
.applicants-table tr:last-child td { border-bottom: none; }

.app-status { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 99px; }
.app-status.pending  { background: #fef9c3; color: #a16207; }
.app-status.accepted { background: #dcfce7; color: #16a34a; }
.app-status.rejected { background: #fee2e2; color: #b91c1c; }

.btn-spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.35); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; flex-shrink: 0; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
