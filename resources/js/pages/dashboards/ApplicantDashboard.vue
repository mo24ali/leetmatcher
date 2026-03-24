<template>
  <div class="dashboard-page">
    <!-- Hero greeting -->
    <section class="dash-hero">
      <div class="dash-hero-inner">
        <div>
          <p class="dash-welcome">Welcome back,</p>
          <h1 class="dash-title">{{ auth.state.user?.name }} 👋</h1>
          <p class="dash-subtitle">Here's an overview of your job search activity.</p>
        </div>
        <div class="hero-badge applicant">Applicant</div>
      </div>
    </section>

    <div class="dash-content">
      <!-- Stats row -->
      <section class="stats-grid" id="apps">
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-body">
            <p class="stat-label">Applications Sent</p>
            <p class="stat-number">{{ stats.applied }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⏳</div>
          <div class="stat-body">
            <p class="stat-label">Pending Review</p>
            <p class="stat-number">{{ stats.pending }}</p>
          </div>
        </div>
        <div class="stat-card positive">
          <div class="stat-icon">✅</div>
          <div class="stat-body">
            <p class="stat-label">Accepted</p>
            <p class="stat-number">{{ stats.accepted }}</p>
          </div>
        </div>
        <div class="stat-card negative">
          <div class="stat-icon">❌</div>
          <div class="stat-body">
            <p class="stat-label">Rejected</p>
            <p class="stat-number">{{ stats.rejected }}</p>
          </div>
        </div>
      </section>

      <div class="dash-two-col">
        <!-- CV Upload section -->
        <section class="dash-card cv-section" id="cv-upload">
          <div class="dash-card-header">
            <h2 class="dash-card-title">📄 CV Upload & Parser</h2>
            <p class="dash-card-subtitle">Upload your resume to auto-extract your profile information</p>
          </div>

          <div
            class="drop-zone"
            :class="{ 'drop-active': dragging, 'has-file': selectedFile }"
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="handleDrop"
            @click="$refs.fileInput.click()"
          >
            <input
              ref="fileInput"
              type="file"
              accept=".pdf,.doc,.docx"
              style="display:none"
              @change="handleFileSelect"
            />
            <div v-if="!selectedFile" class="drop-placeholder">
              <div class="drop-icon">📁</div>
              <p class="drop-text">Drop your CV here or <span class="drop-link">browse</span></p>
              <p class="drop-hint">PDF, DOC, DOCX · Max 5 MB</p>
            </div>
            <div v-else class="drop-selected">
              <div class="file-icon">📎</div>
              <div class="file-meta">
                <p class="file-name">{{ selectedFile.name }}</p>
                <p class="file-size">{{ formatBytes(selectedFile.size) }}</p>
              </div>
              <button class="file-remove" @click.stop="clearFile" title="Remove file">✕</button>
            </div>
          </div>

          <div v-if="uploadError" class="upload-error">{{ uploadError }}</div>

          <button
            class="upload-btn"
            :disabled="!selectedFile || uploading"
            @click="uploadCv"
          >
            <span v-if="uploading" class="btn-spinner"></span>
            <span>{{ uploading ? 'Parsing your CV…' : 'Upload & Parse CV' }}</span>
          </button>

          <!-- Parsed results -->
          <transition name="fade-slide">
            <div v-if="parsed" class="parsed-results">
              <h3 class="parsed-title">✨ Parsed Information</h3>

              <div class="parsed-grid">
                <div v-if="parsed.name" class="parsed-field">
                  <label>Name</label>
                  <span>{{ parsed.name }}</span>
                </div>
                <div v-if="parsed.email" class="parsed-field">
                  <label>Email</label>
                  <span>{{ parsed.email }}</span>
                </div>
                <div v-if="parsed.phone" class="parsed-field">
                  <label>Phone</label>
                  <span>{{ parsed.phone }}</span>
                </div>
              </div>

              <div v-if="parsed.skills?.length" class="parsed-section">
                <h4>Skills</h4>
                <div class="skill-tags">
                  <span v-for="skill in parsed.skills" :key="skill" class="skill-tag">{{ skill }}</span>
                </div>
              </div>

              <div v-if="parsed.education?.length" class="parsed-section">
                <h4>Education</h4>
                <ul class="parsed-list">
                  <li v-for="item in parsed.education" :key="item">{{ item }}</li>
                </ul>
              </div>

              <div v-if="parsed.experience?.length" class="parsed-section">
                <h4>Experience</h4>
                <ul class="parsed-list">
                  <li v-for="item in parsed.experience" :key="item">{{ item }}</li>
                </ul>
              </div>
            </div>
          </transition>
        </section>

        <!-- Browse Jobs panel (placeholder) -->
        <section class="dash-card browse-section" id="browse">
          <div class="dash-card-header">
            <h2 class="dash-card-title">🔍 Browse Jobs</h2>
            <p class="dash-card-subtitle">Open positions matching your profile</p>
          </div>
          <div class="job-list">
            <div v-for="job in recommendedJobs" :key="job.id" class="job-card">
              <div class="job-card-top">
                <div>
                  <p class="job-title">{{ job.title }}</p>
                  <p class="job-company">{{ job.company }}</p>
                </div>
                <span class="job-badge" :class="job.type === 'Remote' ? 'remote' : 'onsite'">{{ job.type }}</span>
              </div>
              <div class="job-card-footer">
                <span class="job-skills">{{ job.skills.join(' · ') }}</span>
                <button class="job-apply-btn">Apply →</button>
              </div>
            </div>
            <!-- Empty state for no jobs found -->
            <p v-if="!loading && recommendedJobs.length === 0" class="empty-hint">No jobs matching your profile yet. Try uploading a CV!</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '../../stores/authStore'

const auth = useAuthStore()
const loading = ref(true)

// Applicant statistics (initialized to zero)
// These will be populated by an API call to a dashboard stats endpoint
const stats = reactive({ applied: 0, pending: 0, accepted: 0, rejected: 0 })

// Recommended job listings for the applicant
// These will be populated by the matching algorithm via API
const recommendedJobs = ref([])

/**
 * Fetch all applicant-specific dashboard data
 */
async function loadApplicantData() {
  loading.value = true
  try {
    // Endpoints to be implemented in ApplicantController or similar
    // const [sData, jData] = await Promise.all([
    //   auth.apiFetch('/v1/applicant/stats'),
    //   auth.apiFetch('/v1/applicant/recommended-jobs')
    // ])
    
    // Assign fetched data to reactive refs
    // stats.applied = sData.applied_count
    // recommendedJobs.value = jData
    
    console.log('Applicant data fetching logic ready for backend implementation.')
  } catch (err) {
    console.error('Failed to load applicant dashboard data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadApplicantData()
})

// ─── CV Upload ────────────────────────────────────────────────────────────────
const fileInput   = ref(null)
const selectedFile = ref(null)
const dragging    = ref(false)
const uploading   = ref(false)
const uploadError = ref('')
const parsed      = ref(null)

function handleDrop(e) {
  dragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) validateAndSet(file)
}

function handleFileSelect(e) {
  const file = e.target.files[0]
  if (file) validateAndSet(file)
}

function validateAndSet(file) {
  uploadError.value = ''
  const allowed = ['application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  if (!allowed.includes(file.type) && !file.name.match(/\.(pdf|doc|docx)$/i)) {
    uploadError.value = 'Only PDF, DOC, and DOCX files are accepted.'
    return
  }
  if (file.size > 5 * 1024 * 1024) {
    uploadError.value = 'File exceeds the 5 MB limit.'
    return
  }
  selectedFile.value = file
  parsed.value = null
}

function clearFile() {
  selectedFile.value = null
  parsed.value = null
  uploadError.value = ''
  if (fileInput.value) fileInput.value.value = ''
}

async function uploadCv() {
  if (!selectedFile.value) return
  uploading.value   = true
  uploadError.value = ''
  try {
    const res = await auth.uploadCv(selectedFile.value)
    parsed.value = res.parsed
  } catch (e) {
    uploadError.value = e.message || 'Upload failed. Please try again.'
  } finally {
    uploading.value = false
  }
}

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}
</script>

<style scoped>
/* ── Layout ── */
.dashboard-page { min-height: 100vh; background: var(--gray-50); font-family: var(--font-base); }

.dash-hero {
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  padding: 2.5rem 0;
}
.dash-hero-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dash-welcome  { font-size: 0.85rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 0.2rem; }
.dash-title    { font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; color: var(--gray-900); margin: 0 0 0.4rem; }
.dash-subtitle { font-size: 0.95rem; color: var(--gray-500); margin: 0; }

.hero-badge {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 0.4rem 1rem;
  border-radius: 99px;
}
.hero-badge.applicant { background: #e0f2fe; color: #0369a1; }

.dash-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* ── Stats ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}
@media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: box-shadow 0.2s;
}
.stat-card:hover     { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.stat-card.positive  { border-left: 3px solid #22c55e; }
.stat-card.negative  { border-left: 3px solid #ef4444; }

.stat-icon   { font-size: 1.75rem; }
.stat-label  { font-size: 0.8rem; color: var(--gray-500); margin: 0 0 0.25rem; text-transform: uppercase; letter-spacing: 0.04em; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: var(--gray-900); margin: 0; line-height: 1; }

/* ── Two-column layout ── */
.dash-two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
@media (max-width: 900px) { .dash-two-col { grid-template-columns: 1fr; } }

.dash-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: 16px;
  padding: 1.75rem;
}

.dash-card-header { margin-bottom: 1.5rem; }
.dash-card-title  { font-size: 1.1rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.3rem; }
.dash-card-subtitle { font-size: 0.85rem; color: var(--gray-500); margin: 0; }

/* ── Drop zone ── */
.drop-zone {
  border: 2px dashed var(--gray-300);
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 1rem;
  background: var(--gray-50);
}
.drop-zone:hover, .drop-zone.drop-active { border-color: var(--gray-600); background: var(--gray-100); }
.drop-zone.has-file { border-style: solid; border-color: #22c55e; background: #f0fdf4; }

.drop-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
.drop-text { font-size: 0.95rem; color: var(--gray-600); margin: 0 0 0.25rem; }
.drop-link { color: var(--gray-900); font-weight: 600; text-decoration: underline; }
.drop-hint { font-size: 0.8rem; color: var(--gray-400); margin: 0; }

.drop-selected {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-align: left;
}
.file-icon { font-size: 2rem; }
.file-meta { flex: 1; }
.file-name { font-size: 0.9rem; font-weight: 500; color: var(--gray-900); margin: 0 0 0.2rem; word-break: break-all; }
.file-size { font-size: 0.8rem; color: var(--gray-500); margin: 0; }
.file-remove {
  background: none;
  border: none;
  color: var(--gray-400);
  font-size: 1rem;
  cursor: pointer;
  padding: 0.25rem;
  transition: color 0.15s;
}
.file-remove:hover { color: #ef4444; }

.upload-error {
  color: #b91c1c;
  font-size: 0.85rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 0.6rem 0.9rem;
  margin-bottom: 0.75rem;
}

.upload-btn {
  width: 100%;
  padding: 0.9rem;
  background: var(--gray-900);
  color: var(--white);
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
  font-family: var(--font-base);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.upload-btn:hover:not(:disabled) { background: var(--gray-800); }
.upload-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Parsed results ── */
.parsed-results {
  margin-top: 1.5rem;
  border: 1px solid #d1fae5;
  background: #f0fdf4;
  border-radius: 12px;
  padding: 1.25rem;
}

.parsed-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #166534;
  margin: 0 0 1rem;
}

.parsed-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.parsed-field label {
  display: block;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6b7280;
  margin-bottom: 0.15rem;
}
.parsed-field span {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--gray-900);
}

.parsed-section { margin-bottom: 0.75rem; }
.parsed-section h4 {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #6b7280;
  margin: 0 0 0.5rem;
  font-weight: 600;
}

.skill-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.skill-tag {
  font-size: 0.8rem;
  background: #d1fae5;
  color: #065f46;
  padding: 0.2rem 0.6rem;
  border-radius: 99px;
  font-weight: 500;
}

.parsed-list { margin: 0; padding-left: 1.25rem; }
.parsed-list li { font-size: 0.85rem; color: var(--gray-700); margin-bottom: 0.2rem; }

/* ── Job cards ── */
.job-list { display: flex; flex-direction: column; gap: 0.75rem; }
.job-card {
  border: 1px solid var(--gray-200);
  border-radius: 10px;
  padding: 1rem 1.25rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.job-card:hover { border-color: var(--gray-400); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

.job-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}
.job-title   { font-size: 0.95rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.2rem; }
.job-company { font-size: 0.82rem; color: var(--gray-500); margin: 0; }

.job-badge {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 99px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: nowrap;
}
.job-badge.remote { background: #e0f2fe; color: #0369a1; }
.job-badge.onsite { background: #f3f4f6; color: #374151; }

.job-card-footer { display: flex; justify-content: space-between; align-items: center; }
.job-skills { font-size: 0.78rem; color: var(--gray-500); }
.job-apply-btn {
  font-size: 0.8rem;
  font-weight: 600;
  background: none;
  border: 1px solid var(--gray-300);
  border-radius: 6px;
  padding: 0.3rem 0.7rem;
  cursor: pointer;
  transition: all 0.15s;
  font-family: var(--font-base);
  color: var(--gray-700);
}
.job-apply-btn:hover { background: var(--gray-900); color: var(--white); border-color: var(--gray-900); }

/* ── Transitions ── */
.btn-spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

.fade-slide-enter-active { transition: opacity 0.35s ease, transform 0.35s ease; }
.fade-slide-enter-from   { opacity: 0; transform: translateY(12px); }
</style>
