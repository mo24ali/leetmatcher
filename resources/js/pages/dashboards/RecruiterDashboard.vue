    <template>
      <div class="dashboard-page">
        <section class="dash-hero">
          <div class="dash-hero-inner">
            <div>
              <p class="dash-welcome">Recruiter Portal</p>
              <h1 class="dash-title">{{ auth.state.user?.name }}</h1>
              <p class="dash-subtitle">Manage your job postings and discover top talent.</p>
            </div>
            <div class="hero-badge recruiter">Recruiter</div>
          </div>
        </section>

        <div class="dash-content">
          <section class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon-box"><span>&#9646;</span></div>
              <div class="stat-body">
                <p class="stat-label">Active Listings</p>
                <p class="stat-number">{{ stats.activeListings }}</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon-box"><span>&#128101;</span></div>
              <div class="stat-body">
                <p class="stat-label">Total Applicants</p>
                <p class="stat-number">{{ stats.totalApplicants }}</p>
              </div>
            </div>
            <div class="stat-card positive">
              <div class="stat-icon-box success"><span>&#10003;</span></div>
              <div class="stat-body">
                <p class="stat-label">Interviews Scheduled</p>
                <p class="stat-number">{{ stats.interviews }}</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon-box"><span>&#9670;</span></div>
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
                <h2 class="dash-card-title">{{ editingId ? 'Edit Job Posting' : 'Post a New Job' }}</h2>
                <p class="dash-card-subtitle">
                  {{ editingId ? 'Update this listing details' : 'Create a listing to attract top candidates' }}
                </p>
              </div>

              <div v-if="postSuccess" class="success-banner">Job posted successfully!</div>
              <div v-if="postError" class="error-banner">{{ postError }}</div>

              <form @submit.prevent="handleSubmit" class="post-form">
                <div class="form-group">
                  <label class="field-label">Job Title</label>
                  <input v-model="jobForm.title" type="text" class="form-input" placeholder="e.g. Senior Frontend Engineer" required />
                </div>
                <div class="form-group">
                  <label class="field-label">Description</label>
                  <textarea v-model="jobForm.description" class="form-input form-textarea" placeholder="Describe the role, requirements, and expectations…" required></textarea>
                </div>

                <!-- Skills Management Section -->
                <div class="form-group">
                  <label class="field-label">Required Skills</label>
                  <div class="skills-input-wrapper">
                    <div class="skills-tags">
                      <span v-for="(skill, idx) in jobForm.skills" :key="idx" class="skill-tag">
                        {{ skill }}
                        <button type="button" class="remove-skill" @click="removeSkillItem(idx)">&times;</button>
                      </span>
                    </div>
                    <div class="autocomplete-container">
                      <input 
                        v-model="skillQuery" 
                        type="text" 
                        class="form-input skill-input" 
                        placeholder="Type a skill (e.g. Python) and press Enter…"
                        @input="handleSkillSearch"
                        @keydown.enter.prevent="addSkillFromInput"
                      />
                      <div v-if="skillSuggestions.length > 0" class="suggestions-list">
                        <div 
                          v-for="s in skillSuggestions" 
                          :key="s" 
                          class="suggestion-item"
                          @click="addSkillItem(s)"
                        >
                          {{ s }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="form-hint">Press Enter to add, or select from suggestions.</p>
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
                <div class="form-row">
                  <button type="submit" class="submit-btn" :disabled="posting">
                    <span v-if="posting" class="btn-spinner"></span>
                    <span>{{ posting ? 'Saving…' : (editingId ? 'Update Listing' : 'Post Job') }}</span>
                  </button>
                  <button v-if="editingId" type="button" @click="cancelEdit" class="listing-btn cancel-btn">Cancel</button>
                </div>
              </form>
            </section>

            <!-- My Listings -->
            <section class="dash-card" id="listings">
              <div class="dash-card-header">
                <h2 class="dash-card-title">My Listings</h2>
                <p class="dash-card-subtitle">Jobs you've posted on LeetMatcher</p>
              </div>

              <div class="listing-list">
                <div v-for="listing in listings" :key="listing.id" class="listing-card">
                  <div class="listing-top">
                    <div>
                      <p class="listing-title">{{ listing.title }}</p>
                      <p class="listing-meta">Posted {{ listing.posted }} &middot; Deadline {{ listing.deadlineDisplay }}</p>
                    </div>
                    <div class="listing-right">
                      <span class="listing-status" :class="listing.status">{{ listing.status }}</span>
                      <span class="listing-count">{{ listing.applications_count }} applicants</span>
                    </div>
                  </div>
                  <div class="listing-actions">
                    <button class="listing-btn" @click="viewApplicants(listing)">View Applicants</button>
                    <button class="listing-btn" @click="startEdit(listing)">Edit</button>
                    <button v-if="listing.status !== 'closed'" class="listing-btn" @click="closeListing(listing)">Close</button>
                    <button class="listing-btn danger" @click="deleteListing(listing)">Delete</button>
                  </div>
                </div>
                <p v-if="listings.length === 0" class="empty-hint">No listings yet. Post your first job above.</p>
              </div>
            </section>
          </div>

          <!-- Applicants panel — shown when a listing is selected -->
          <section v-if="selectedListing" class="dash-card applicants-panel" id="applicants">
            <div class="dash-card-header panel-header">
              <h2 class="dash-card-title">Applicants for "{{ selectedListing.title }}"</h2>
              <button class="close-panel" @click="selectedListing = null">&times; Close</button>
            </div>
            
            <div v-if="loadingApplicants" class="loading-state">Loading applicants...</div>
            <table v-else-if="applicants.length > 0" class="applicants-table">
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
                    <button class="listing-btn small" @click="reviewApplicant(app)">Review</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="empty-hint">No applicants yet for this listing.</p>

          </section>
        </div>

        <!-- Application review popup -->
            <div  
            
                v-if="reviewingApplicant"
                id="application-review-pannel" 
                  class="review-pannel-overlay"
                  @click.self="closeReviewPanel">
                <div class="review-pannel">
                  <button class="close-btn"
                          @click="closeReviewPanel">
                    &times;
                  </button>
                  <div class="review-header">
                      <ProfileAvatar 
                        :url="reviewingApplicant.picture" 
                        :name="reviewingApplicant.name" 
                        size="xl"
                      />
                      <h2 class="mt-2 text-xl font-bold text-gray-900">{{ reviewingApplicant.name }}</h2>
                      <p class="text-sm text-gray-500">{{ reviewingApplicant.description }}</p>
                      <div class="flex flex-col gap-1 mt-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">
                        <span><strong>Email:</strong> {{ reviewingApplicant.email }}</span>
                        <span><strong>Phone:</strong> {{ reviewingApplicant.phone }}</span>
                      </div>
                  </div>
                  <div class="reviewing-body">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Technical Skills</h3>
                    <ul class="skills-list">
                      <li v-for="skill in reviewingApplicant.skills" :key="skill">
                          {{ skill }}
                      </li>
                    </ul>
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mt-4 mb-2">Matching Precision</h3>
                    <p class="text-xl font-black text-green-600 bg-green-50 px-4 py-2 rounded-2xl inline-block">
                      {{ reviewingApplicant.matchPercent }}
                    </p>
                  </div>
                  <div class="review-actions" v-if="!schedulingInterview">
                    <button id="accept" @click="schedulingInterview = true" class="action-btn accept-btn">
                        Schedule Interview
                    </button>
                    <button id="reject" @click="rejectApplication(reviewingApplicant)" class="action-btn reject-btn">
                        Not a fit
                    </button>
                  </div>
                  
                  <div v-if="schedulingInterview" class="mt-5 p-6 border border-blue-100 rounded-3xl bg-blue-50/30 animate-in slide-in-from-bottom-2">
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900 mb-4">Set Interview Time</h3>
                    <div class="flex flex-col gap-4">
                      <div>
                        <label class="block text-[0.65rem] font-black uppercase tracking-widest text-slate-400 mb-2">Preferred Date & Time</label>
                        <input type="datetime-local" v-model="interviewForm.scheduled_at" class="w-full border border-slate-200 rounded-xl text-sm px-4 py-3 bg-white focus:outline-none focus:border-blue-500 shadow-sm transition-all" />
                      </div>
                      <div class="p-4 bg-white/60 rounded-2xl border border-blue-100/50 flex items-center gap-3">
                         <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                           <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 10l5-5v14l-5-5v-4z"/><rect x="2" y="3" width="11" height="18" rx="2" ry="2"/></svg>
                         </div>
                         <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-tight">Internal Video Room</span>
                            <span class="text-[0.6rem] text-slate-400 font-bold uppercase tracking-widest">Auto-generated link</span>
                         </div>
                      </div>
                      <div class="flex gap-2 mt-2">
                        <button @click="createInterview(reviewingApplicant)" class="flex-1 bg-slate-900 text-white px-6 py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">Confirm Booking</button>
                        <button @click="schedulingInterview = false" class="bg-white text-slate-400 px-4 py-3.5 rounded-2xl text-xs font-bold hover:text-slate-600 transition">Cancel</button>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
      </div>
    </template>

<script setup>
import { reactive, ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import ProfileAvatar from '../../components/ProfileAvatar.vue'

const auth = useAuthStore()
const loading = ref(true)

const stats = reactive({ activeListings: 0, totalApplicants: 0, interviews: 0, filled: 0 })
const jobForm = reactive({ title: '', description: '', deadline: '', status: 'open', skills: [] })
const skillQuery = ref('')
const skillSuggestions = ref([])
const posting     = ref(false)
const postSuccess = ref(false)
const postError   = ref('')
const listings    = ref([])
const selectedListing = ref(null)
const applicants  = ref([])
const loadingApplicants = ref(false)
const editingId = ref(null)

let pollInterval = null

async function loadRecruiterData(quiet = false) {  
  if (!quiet) loading.value = true
  try {
    const [sData, lData] = await Promise.all([
      auth.apiFetch('/v1/recruiter/stats'),
      auth.apiFetch('/v1/recruiter/listings')
    ])
    
    if (sData?.stats) {
      stats.activeListings = sData.stats.total_projects || 0
      stats.totalApplicants = sData.stats.total_applications || 0
      stats.interviews = sData.stats.scheduled_interviews || 0
      stats.filled = 0 
    }
    
    if (Array.isArray(lData)) {
      listings.value = lData.map(l => ({
        ...l,
        posted: l.created_at ? new Date(l.created_at).toLocaleDateString() : 'N/A',
        deadlineDisplay: l.deadline ? new Date(l.deadline).toLocaleDateString() : 'N/A',
      }))
    } else {
      listings.value = []
    }
  } catch (err) {
    console.error('Failed to load recruiter data:', err)
  } finally {
    loading.value = false
  }
}


onMounted(() => {
  loadRecruiterData()
  pollInterval = setInterval(() => loadRecruiterData(true), 30000)
})

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval)
})

async function handleSubmit() {
  if (editingId.value) {
    await updateJob()
  } else {
    await postJob()
  }
}

async function postJob() {
  postSuccess.value = false
  postError.value   = ''
  posting.value     = true
  try {
    await auth.apiFetch('/v1/projects', {
      method: 'POST',
      body: JSON.stringify({
        title:       jobForm.title,
        description: jobForm.description,
        deadline:    jobForm.deadline,
        status:      jobForm.status,
        skills:      jobForm.skills,
      }),
    })
    
    clearForm()
    postSuccess.value = true
    setTimeout(() => postSuccess.value = false, 3000)
    await loadRecruiterData(true)
  } catch (err) {
    postError.value = err.message || 'Failed to post job.'
  } finally {
    posting.value = false
  }
}

async function updateJob() {
  postSuccess.value = false
  postError.value   = ''
  posting.value     = true
  try {
    await auth.apiFetch(`/v1/projects/${editingId.value}`, {
      method: 'PATCH',
      body: JSON.stringify({
        title:       jobForm.title,
        description: jobForm.description,
        deadline:    jobForm.deadline,
        status:      jobForm.status,
        skills:      jobForm.skills,
      }),
    })
    
    clearForm()
    postSuccess.value = true
    setTimeout(() => postSuccess.value = false, 3000)
    await loadRecruiterData(true)
  } catch (err) {
    postError.value = err.message || 'Failed to update job.'
  } finally {
    posting.value = false
  }
}

function startEdit(listing) {
  editingId.value = listing.id
  jobForm.title = listing.title
  jobForm.description = listing.description
  jobForm.deadline = new Date(listing.deadline).toISOString().split('T')[0]
  jobForm.status = listing.status
  jobForm.skills = listing.skills ? listing.skills.map(s => s.name) : []
  
  document.getElementById('post').scrollIntoView({ behavior: 'smooth' })
}

function cancelEdit() {
  clearForm()
}

function clearForm() {
  editingId.value = null
  jobForm.title = ''
  jobForm.description = ''
  jobForm.deadline = ''
  jobForm.status = 'open'
  jobForm.skills = []
  skillQuery.value = ''
  skillSuggestions.value = []
}


async function handleSkillSearch() {
  if (skillQuery.value.length < 2) {
    skillSuggestions.value = []
    return
  }
  try {
    skillSuggestions.value = await auth.apiFetch(`/v1/skills/search?query=${encodeURIComponent(skillQuery.value)}`)
  } catch (err) {
    console.error('Skill search failed:', err)
  }
}

function addSkillFromInput() {
  const val = skillQuery.value.trim()
  if (val) addSkillItem(val)
}

function addSkillItem(name) {
  const normalized = name.trim()
  if (normalized && !jobForm.skills.includes(normalized)) {
    jobForm.skills.push(normalized)
  }
  skillQuery.value = ''
  skillSuggestions.value = []
}

function removeSkillItem(index) {
  jobForm.skills.splice(index, 1)
}

async function closeListing(listing) {
  if (!confirm('Are you sure you want to close this listing?')) return
  try {
    await auth.apiFetch(`/v1/projects/${listing.id}`, { 
      method: 'PATCH', 
      body: JSON.stringify({ status: 'closed' }) 
    })
    await loadRecruiterData(true)
  } catch (err) {
    console.error('Failed to close listing:', err)
  }
} 

async function deleteListing(listing) {
  if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) return
  try {
    await auth.apiFetch(`/v1/projects/${listing.id}`, { method: 'DELETE' })
    await loadRecruiterData(true)
    if (selectedListing.value?.id === listing.id) selectedListing.value = null
  } catch (err) {
    console.error('Failed to delete listing:', err)
  }
}

async function viewApplicants(listing) {
  selectedListing.value = listing
  applicants.value = []
  loadingApplicants.value = true
  try {
    applicants.value = await auth.apiFetch(`/v1/projects/${listing.id}/applications`)
  } catch (err) {
    console.error('Failed to load applicants:', err)
  } finally {
    loadingApplicants.value = false
  }
}


const reviewingApplicant = ref(null)
const schedulingInterview = ref(false)
const interviewForm = reactive({
  scheduled_at: '',
  meeting_link: 'INTERNAL' // Default to internal
})

function reviewApplicant(app) {
  reviewingApplicant.value = app
  schedulingInterview.value = false
  interviewForm.scheduled_at = ''
}

function closeReviewPanel(){
  reviewingApplicant.value = null
  schedulingInterview.value = false
}

async function createInterview(app){
  if (!interviewForm.scheduled_at) {
    alert("Please provide the Interview Date & Time.")
    return
  }
  
  try {
    const formattedDate = new Date(interviewForm.scheduled_at).toISOString().slice(0, 19).replace('T', ' ');

    await auth.apiFetch(`/v1/interviews`, {
      method: 'POST',
      body: JSON.stringify({
        application_id : app.id,
        scheduled_at : formattedDate,
        meeting_link: 'INTERNAL',
        notes : '',
        score: 0
      })
    });

    await auth.apiFetch(`/v1/applications/${app.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ status: 'in_progress' })
    });

    app.status = 'in_progress';
    alert('Interview scheduled in internal room! Application is now In Progress.');
    closeReviewPanel();
    await loadRecruiterData(true);
  } catch(err) {
    console.error('Failed to schedule interview:', err);
    alert(err.message || 'Failed to schedule interview');
  }
}

async function rejectApplication(app) {
  if (!confirm('Are you sure you want to reject this applicant?')) return;
  try {
    await auth.apiFetch(`/v1/applications/${app.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ status: 'rejected' })
    });
    
    app.status = 'rejected';
    alert('Applicant rejected successfully.');
    closeReviewPanel();
    await loadRecruiterData(true);
  } catch (err) {
    console.error('Failed to reject applicant:', err);
    alert(err.message || 'Failed to reject applicant');
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
.panel-header { display: flex; justify-content: space-between; align-items: center; }
.dash-card-title  { font-size: 1.1rem; font-weight: 600; color: var(--gray-900); margin: 0 0 0.3rem; }
.dash-card-subtitle { font-size: 0.85rem; color: var(--gray-500); margin: 0; }

.post-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width:640px){ .form-row { grid-template-columns: 1fr; } }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.field-label { font-size: 0.8rem; font-weight: 500; color: var(--gray-700); }
.form-input { border: 1px solid var(--gray-300); border-radius: 8px; padding: 0.55rem 0.8rem; font-size: 0.9rem; font-family: var(--font-base); color: var(--gray-900); background: var(--white); outline: none; transition: border-color 0.15s; }
.form-input:focus { border-color: var(--gray-600); }
.form-textarea { resize: vertical; min-height: 100px; }

.success-banner { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.9rem; margin-bottom: 1rem; }
.error-banner   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.9rem; margin-bottom: 1rem; }

.submit-btn {
  width: 100%; padding: 0.9rem; background: var(--gray-900); color: var(--white);
  border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 500; cursor: pointer;
  transition: background 0.2s; font-family: var(--font-base);
  display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.submit-btn:hover:not(:disabled) { background: var(--gray-800); }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.empty-hint { font-size: 0.875rem; color: var(--gray-400); text-align: center; padding: 2rem 0; margin: 0; }

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
.cancel-btn { flex: 0 0 auto; width: auto; height: 100%; align-self: stretch; display: flex; align-items: center; justify-content: center; }

.close-panel { background: none; border: none; cursor: pointer; color: var(--gray-500); font-size: 0.9rem; font-family: var(--font-base); }
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

/* Skills Section Styles */
.skills-input-wrapper { display: flex; flex-direction: column; gap: 0.75rem; border: 1px solid var(--gray-200); border-radius: 10px; padding: 0.8rem; background: var(--gray-50); }
.skills-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.skill-tag { background: var(--gray-900); color: var(--white); padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; display: flex; align-items: center; gap: 0.4rem; }
.remove-skill { background: none; border: none; color: rgba(255,255,255,0.6); cursor: pointer; font-size: 1rem; padding: 0; line-height: 1; }
.remove-skill:hover { color: #fff; }
.autocomplete-container { position: relative; }
.skill-input { width: 100%; border: 1px solid var(--gray-300); background: var(--white); }
.suggestions-list { position: absolute; top: 100%; left: 0; right: 0; background: var(--white); border: 1px solid var(--gray-200); border-radius:8px; z-index: 10; margin-top: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; }
.suggestion-item { padding: 0.6rem 1rem; cursor: pointer; font-size: 0.9rem; color: var(--gray-700); }
.suggestion-item:hover { background: var(--gray-50); color: var(--gray-900); }
.form-hint { font-size: 0.75rem; color: var(--gray-400); margin: 0.25rem 0 0; }

@keyframes spin { to { transform: rotate(360deg); } }

/* Application Review Popup Styles */
.review-pannel-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
.review-pannel { background: var(--white); border-radius: 16px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; position: relative; padding: 2.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; gap: 1.5rem; font-family: var(--font-base); }
.close-btn { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 2rem; cursor: pointer; color: var(--gray-500); transition: color 0.2s; line-height: 1; padding: 0.5rem; display: flex; align-items: center; justify-content: center; }
.close-btn:hover { color: var(--gray-900); }
.review-header { text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
.app-photo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid var(--gray-200); margin-bottom: 0.5rem; }
.review-header h2 { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin: 0; }
.review-header p { font-size: 0.95rem; color: var(--gray-600); margin: 0; line-height: 1.4; }
.review-header p strong { color: var(--gray-800); }
.reviewing-body { display: flex; flex-direction: column; gap: 1rem; border-top: 1px solid var(--gray-200); padding-top: 1.5rem; }
.reviewing-body h3 { font-size: 1.05rem; font-weight: 600; color: var(--gray-900); margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
.skills-list { display: flex; flex-wrap: wrap; gap: 0.5rem; list-style: none; padding: 0; margin: 0; }
.skills-list li { background: var(--gray-900); color: var(--white); padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; }
.reviewing-body p { font-size: 1.25rem; font-weight: 700; color: #16a34a; margin: 0; display: inline-block; background: #dcfce7; padding: 0.4rem 1rem; border-radius: 99px; align-self: flex-start; }

.review-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem; border-top: 1px solid var(--gray-200); padding-top: 1.5rem; }
.action-btn { padding: 0.8rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; font-size: 0.95rem; font-family: var(--font-base); display: flex; justify-content: center; align-items: center;}
.accept-btn { background: #16a34a; color: white; }
.accept-btn:hover { background: #15803d; }
.reject-btn { background: #ef4444; color: white; }
.reject-btn:hover { background: #b91c1c; }
</style>
