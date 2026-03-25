<template>
  <div class="min-h-screen bg-gray-50 font-sans pb-16">

    <!-- Page header bar -->
    <div class="bg-white border-b border-gray-200 py-6">
      <div class="max-w-5xl mx-auto px-6">
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">My Profile</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage your account, CV data, and security settings</p>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 pt-8 flex gap-7 items-start">

      <!-- ── Sidebar ─────────────────────────────────────────────── -->
      <aside class="hidden md:flex w-52 shrink-0 flex-col gap-1 sticky top-24">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="w-full text-left text-sm font-medium px-3 py-2.5 rounded-lg transition-colors"
          :class="activeTab === tab.id
            ? 'bg-gray-100 text-gray-900 font-semibold'
            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </aside>

      <!-- Mobile tab row -->
      <div class="flex md:hidden gap-1 flex-wrap mb-4 w-full">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="text-xs font-medium px-3 py-1.5 rounded-full border transition-colors"
          :class="activeTab === tab.id
            ? 'bg-gray-900 text-white border-gray-900'
            : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
          @click="activeTab = tab.id"
        >{{ tab.label }}</button>
      </div>

      <!-- ── Main content ────────────────────────────────────────── -->
      <main class="flex-1 min-w-0 flex flex-col gap-5">

        <!-- ═══════════════════════════════════════════ OVERVIEW ══ -->
        <template v-if="activeTab === 'overview'">

          <!-- Avatar + identity -->
          <section class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-5 pb-5 mb-5 border-b border-gray-100">
              <!-- Avatar -->
              <div class="relative group shrink-0">
                <div
                  v-if="!avatarPreview"
                  class="w-20 h-20 rounded-full bg-gray-900 text-white flex items-center justify-center
                         text-2xl font-bold select-none"
                >
                  {{ initials }}
                </div>
                <img
                  v-else
                  :src="avatarPreview"
                  class="w-20 h-20 rounded-full object-cover border border-gray-200"
                  alt="Profile avatar"
                />
                <label
                  class="absolute inset-0 rounded-full bg-black/40 text-white text-xs font-semibold
                         flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer
                         transition-opacity"
                >
                  Change
                  <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    @change="handleAvatarSelect"
                  />
                </label>
              </div>

              <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold text-gray-900 truncate">{{ auth.state.user?.name }}</h2>
                <p class="text-sm text-gray-500 truncate">{{ auth.state.user?.email }}</p>
                <span
                  class="inline-block mt-1.5 text-[0.65rem] font-bold uppercase tracking-wide
                         px-2.5 py-0.5 rounded-full"
                  :class="roleBadgeClass"
                >{{ auth.role.value }}</span>
              </div>

              <!-- Avatar upload button (visible when file selected) -->
              <div v-if="pendingAvatar" class="flex flex-col gap-2 shrink-0">
                <button
                  class="text-xs font-semibold bg-gray-900 text-white px-4 py-1.5 rounded-lg
                         hover:bg-gray-700 transition disabled:opacity-50"
                  :disabled="savingAvatar"
                  @click="saveAvatar"
                >
                  {{ savingAvatar ? 'Saving…' : 'Save Photo' }}
                </button>
                <button
                  class="text-xs text-gray-500 hover:text-gray-800"
                  @click="cancelAvatar"
                >Cancel</button>
              </div>
            </div>

            <!-- Avatar error -->
            <p v-if="avatarError" class="text-xs text-red-600 mb-4">{{ avatarError }}</p>

            <!-- Name / bio edit form -->
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-sm font-semibold text-gray-700">Account Details</h3>
              <button v-if="!editingInfo" class="text-xs text-gray-500 hover:text-gray-900 underline" @click="startEditInfo">Edit</button>
              <div v-else class="flex gap-2">
                <button
                  class="text-xs font-semibold bg-gray-900 text-white px-3 py-1 rounded-lg hover:bg-gray-700 transition disabled:opacity-50"
                  :disabled="savingInfo"
                  @click="saveInfo"
                >{{ savingInfo ? 'Saving…' : 'Save' }}</button>
                <button class="text-xs text-gray-500 hover:text-gray-800" @click="cancelEditInfo">Cancel</button>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <p class="text-[0.7rem] uppercase tracking-wider text-gray-400 font-semibold mb-1">Full Name</p>
                <input
                  v-if="editingInfo"
                  v-model="infoDraft.name"
                  type="text"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900
                         outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition"
                />
                <p v-else class="text-sm font-medium text-gray-900">{{ auth.state.user?.name }}</p>
              </div>
              <div>
                <p class="text-[0.7rem] uppercase tracking-wider text-gray-400 font-semibold mb-1">Email</p>
                <p class="text-sm font-medium text-gray-900">{{ auth.state.user?.email }}</p>
              </div>
              <div class="sm:col-span-2">
                <p class="text-[0.7rem] uppercase tracking-wider text-gray-400 font-semibold mb-1">Bio</p>
                <textarea
                  v-if="editingInfo"
                  v-model="infoDraft.bio"
                  rows="3"
                  placeholder="A short professional summary…"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900
                         outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition resize-none"
                ></textarea>
                <p v-else class="text-sm text-gray-700 leading-relaxed">{{ profileData.bio || '—' }}</p>
              </div>
            </div>

            <!-- Info error -->
            <p v-if="infoError" class="text-xs text-red-600 mt-3">{{ infoError }}</p>
          </section>

          <!-- Profile completeness -->
          <section class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Profile Completeness</h3>
            <div class="flex items-center gap-3 mb-2">
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gray-900 rounded-full transition-all duration-700"
                  :style="{ width: completenessScore + '%' }"
                ></div>
              </div>
              <span class="text-sm font-semibold text-gray-700 min-w-[38px] text-right">{{ completenessScore }}%</span>
            </div>
            <p class="text-xs text-gray-400">{{ completenessHint }}</p>
          </section>

        </template>

        <!-- ══════════════════════════════════════════════ SKILLS ══ -->
        <template v-if="activeTab === 'skills'">
          <section class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-5">
              <div>
                <h3 class="text-sm font-semibold text-gray-900">Skills</h3>
                <p class="text-xs text-gray-400 mt-0.5">From your CV or added manually</p>
              </div>
            </div>

            <!-- Existing skill tags -->
            <div v-if="skills.length" class="flex flex-wrap gap-2 mb-6">
              <div
                v-for="skill in skills"
                :key="skill.id"
                class="flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-medium
                       px-3 py-1 rounded-full group"
              >
                <span>{{ skill.name }}</span>
                <span v-if="skill.proficiency" class="text-blue-400 text-[0.65rem]">· {{ skill.proficiency }}</span>
                <button
                  class="text-blue-300 hover:text-red-500 transition ml-1 leading-none"
                  :title="'Remove ' + skill.name"
                  @click="deleteSkill(skill)"
                >&times;</button>
              </div>
            </div>
            <p v-else class="text-sm text-gray-400 mb-5">No skills yet. Upload a CV or add them manually below.</p>

            <!-- Manual add form -->
            <div class="border-t border-gray-100 pt-5">
              <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-3">Add a skill manually</p>
              <div class="flex flex-wrap gap-3">
                <input
                  v-model="newSkill.name"
                  type="text"
                  placeholder="e.g. Python"
                  class="flex-1 min-w-[140px] border border-gray-300 rounded-lg px-3 py-2 text-sm
                         text-gray-900 outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition"
                  @keydown.enter.prevent="submitAddSkill"
                />
                <select
                  v-model="newSkill.proficiency"
                  class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white
                         outline-none focus:border-gray-600 transition"
                >
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                  <option value="expert">Expert</option>
                </select>
                <button
                  class="bg-gray-900 text-white text-sm font-semibold px-5 py-2 rounded-lg
                         hover:bg-gray-700 transition disabled:opacity-50"
                  :disabled="!newSkill.name.trim() || addingSkill"
                  @click="submitAddSkill"
                >
                  {{ addingSkill ? 'Adding…' : 'Add' }}
                </button>
              </div>
              <p v-if="skillError" class="text-xs text-red-600 mt-2">{{ skillError }}</p>
            </div>
          </section>
        </template>

        <!-- ══════════════════════════════════════════════════ CV ══ -->
        <template v-if="activeTab === 'cv'">
          <section class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Resume / CV</h3>
            <p class="text-xs text-gray-400 mb-5">Upload from your applicant dashboard to populate this section</p>

            <div v-if="!hasCvData" class="text-center py-10">
              <p class="text-sm text-gray-400 mb-3">No CV data found.</p>
              <router-link
                to="/dashboard/applicant#cv-upload"
                class="text-xs font-semibold bg-gray-900 text-white px-4 py-2 rounded-lg
                       hover:bg-gray-700 transition inline-block"
              >
                Upload your CV
              </router-link>
            </div>

            <template v-else>
              <!-- Personal info -->
              <div class="mb-6">
                <p class="text-[0.68rem] uppercase tracking-widest text-gray-400 font-bold mb-3">Personal Info</p>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                  <div v-if="cvData.name">
                    <p class="text-[0.7rem] text-gray-400 mb-0.5">Name</p>
                    <p class="text-sm font-medium text-gray-900">{{ cvData.name }}</p>
                  </div>
                  <div v-if="cvData.email">
                    <p class="text-[0.7rem] text-gray-400 mb-0.5">Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ cvData.email }}</p>
                  </div>
                  <div v-if="cvData.phone">
                    <p class="text-[0.7rem] text-gray-400 mb-0.5">Phone</p>
                    <p class="text-sm font-medium text-gray-900">{{ cvData.phone }}</p>
                  </div>
                  <div v-if="cvData.location">
                    <p class="text-[0.7rem] text-gray-400 mb-0.5">Location</p>
                    <p class="text-sm font-medium text-gray-900">{{ cvData.location }}</p>
                  </div>
                </div>
              </div>

              <div v-if="cvData.summary" class="mb-6">
                <p class="text-[0.68rem] uppercase tracking-widest text-gray-400 font-bold mb-2">Summary</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ cvData.summary }}</p>
              </div>

              <div v-if="cvData.experience?.length" class="mb-6">
                <p class="text-[0.68rem] uppercase tracking-widest text-gray-400 font-bold mb-2">Experience</p>
                <ul class="space-y-1 list-disc list-inside">
                  <li v-for="item in cvData.experience" :key="item" class="text-sm text-gray-700">{{ item }}</li>
                </ul>
              </div>

              <div v-if="cvData.education?.length" class="mb-6">
                <p class="text-[0.68rem] uppercase tracking-widest text-gray-400 font-bold mb-2">Education</p>
                <ul class="space-y-1 list-disc list-inside">
                  <li v-for="item in cvData.education" :key="item" class="text-sm text-gray-700">{{ item }}</li>
                </ul>
              </div>

              <div v-if="cvData.certifications?.length">
                <p class="text-[0.68rem] uppercase tracking-widest text-gray-400 font-bold mb-2">Certifications</p>
                <ul class="space-y-1 list-disc list-inside">
                  <li v-for="cert in cvData.certifications" :key="cert" class="text-sm text-gray-700">{{ cert }}</li>
                </ul>
              </div>
            </template>
          </section>
        </template>

        <!-- ══════════════════════════════════════════════ PASSWORD ══ -->
        <template v-if="activeTab === 'password'">
          <section class="bg-white rounded-2xl border border-gray-200 p-6 max-w-md">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Change Password</h3>
            <p class="text-xs text-gray-400 mb-6">Choose a strong password of at least 8 characters</p>

            <form class="flex flex-col gap-4" @submit.prevent="submitPasswordChange">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Current Password</label>
                <input
                  v-model="pwForm.current"
                  type="password"
                  autocomplete="current-password"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900
                         outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition"
                  :class="{ 'border-red-400': pwErrors.current }"
                />
                <p v-if="pwErrors.current" class="text-xs text-red-600 mt-1">{{ pwErrors.current }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">New Password</label>
                <input
                  v-model="pwForm.password"
                  type="password"
                  autocomplete="new-password"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900
                         outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition"
                  :class="{ 'border-red-400': pwErrors.password }"
                />
                <p v-if="pwErrors.password" class="text-xs text-red-600 mt-1">{{ pwErrors.password }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Confirm New Password</label>
                <input
                  v-model="pwForm.confirm"
                  type="password"
                  autocomplete="new-password"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900
                         outline-none focus:border-gray-600 focus:ring-2 focus:ring-gray-100 transition"
                  :class="{ 'border-red-400': pwErrors.confirm }"
                />
                <p v-if="pwErrors.confirm" class="text-xs text-red-600 mt-1">{{ pwErrors.confirm }}</p>
              </div>

              <div
                v-if="pwSuccess"
                class="text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2.5"
              >
                Password changed successfully.
              </div>

              <button
                type="submit"
                class="bg-gray-900 text-white text-sm font-semibold px-5 py-2.5 rounded-lg
                       hover:bg-gray-700 transition disabled:opacity-50 self-start"
                :disabled="changingPw"
              >
                {{ changingPw ? 'Updating…' : 'Update Password' }}
              </button>
            </form>
          </section>
        </template>

      </main>
    </div>

    <!-- Global toast -->
    <transition name="toast">
      <div
        v-if="toast"
        class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm font-medium
               px-5 py-3 rounded-xl shadow-xl z-50"
      >
        {{ toast }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()

// ── Tabs ─────────────────────────────────────────────────────────────────────
const tabs = [
  { id: 'overview', label: 'Overview'  },
  { id: 'skills',   label: 'Skills'    },
  { id: 'cv',       label: 'CV Data'   },
  { id: 'password', label: 'Password'  },
]
const activeTab = ref('overview')

// ── Profile data (fetched from backend) ──────────────────────────────────────
const profileData = reactive({ bio: '', cv_score: 0 })
const skills      = ref([])
const cvData      = reactive({
  name: '', email: '', phone: '', location: '',
  summary: '', experience: [], education: [], certifications: [],
})

const hasCvData = computed(() =>
  cvData.name || cvData.email || cvData.experience.length || cvData.education.length
)

const initials = computed(() => {
  const name = auth.state.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || 'U'
})

const roleBadgeClass = computed(() => ({
  applicant: 'bg-blue-50 text-blue-700',
  recruiter: 'bg-green-50 text-green-700',
  admin:     'bg-amber-50 text-amber-700',
}[auth.role.value] ?? 'bg-gray-100 text-gray-600'))

/**
 * Completeness score weights:
 * name=10, email=10, bio=10, avatar=10, skills=20, experience=25, education=15
 */
const completenessScore = computed(() => {
  let s = 0
  if (auth.state.user?.name)       s += 10
  if (auth.state.user?.email)      s += 10
  if (profileData.bio)             s += 10
  if (avatarPreview.value)         s += 10
  if (skills.value.length)         s += 20
  if (cvData.experience.length)    s += 25
  if (cvData.education.length)     s += 15
  return Math.min(s, 100)
})

const completenessHint = computed(() => {
  const s = completenessScore.value
  if (s === 100) return 'Your profile is complete — you stand out to recruiters!'
  if (s >= 70)   return 'Great profile — a few more details will make it shine.'
  if (s >= 40)   return 'Keep going — upload your CV and add skills to improve visibility.'
  return 'Get started by uploading a CV or filling in your bio.'
})

// ── Avatar ────────────────────────────────────────────────────────────────────
const avatarPreview  = ref(null)
const pendingAvatar  = ref(null)
const savingAvatar   = ref(false)
const avatarError    = ref('')

function handleAvatarSelect(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    avatarError.value = 'Image must be under 2 MB.'
    return
  }
  avatarError.value = ''
  pendingAvatar.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

function cancelAvatar() {
  pendingAvatar.value = null
  avatarPreview.value = profileData.avatar_url ?? null
}

async function saveAvatar() {
  if (!pendingAvatar.value) return
  savingAvatar.value = true
  avatarError.value  = ''
  try {
    const res = await auth.uploadAvatar(pendingAvatar.value)
    avatarPreview.value = res.avatar_url
    pendingAvatar.value = null
    showToast('Profile photo updated.')
  } catch (err) {
    avatarError.value = err.message || 'Upload failed.'
  } finally {
    savingAvatar.value = false
  }
}

// ── Info (name / bio) edit ────────────────────────────────────────────────────
const editingInfo = ref(false)
const savingInfo  = ref(false)
const infoError   = ref('')
const infoDraft   = reactive({ name: '', bio: '' })

function startEditInfo() {
  infoDraft.name = auth.state.user?.name ?? ''
  infoDraft.bio  = profileData.bio ?? ''
  editingInfo.value = true
  infoError.value   = ''
}

function cancelEditInfo() {
  editingInfo.value = false
  infoError.value   = ''
}

async function saveInfo() {
  if (!infoDraft.name.trim()) {
    infoError.value = 'Name cannot be empty.'
    return
  }
  savingInfo.value = true
  infoError.value  = ''
  try {
    await auth.updateProfile({ name: infoDraft.name.trim(), bio: infoDraft.bio.trim() || null })
    profileData.bio   = infoDraft.bio.trim()
    editingInfo.value = false
    showToast('Profile updated.')
  } catch (err) {
    infoError.value = err.message || 'Failed to save changes.'
  } finally {
    savingInfo.value = false
  }
}

// ── Skills ────────────────────────────────────────────────────────────────────
const newSkill   = reactive({ name: '', proficiency: 'intermediate' })
const addingSkill = ref(false)
const skillError  = ref('')

async function submitAddSkill() {
  const name = newSkill.name.trim()
  if (!name) return
  addingSkill.value = true
  skillError.value  = ''
  try {
    const res = await auth.addSkill(name, newSkill.proficiency)
    skills.value.push(res.skill)
    newSkill.name = ''
    showToast('Skill added.')
  } catch (err) {
    skillError.value = err.errors?.name?.[0] ?? err.message ?? 'Failed to add skill.'
  } finally {
    addingSkill.value = false
  }
}

async function deleteSkill(skill) {
  try {
    await auth.removeSkill(skill.id)
    skills.value = skills.value.filter(s => s.id !== skill.id)
    showToast('Skill removed.')
  } catch (err) {
    showToast(err.message || 'Failed to remove skill.')
  }
}

// ── Password change ───────────────────────────────────────────────────────────
const pwForm = reactive({ current: '', password: '', confirm: '' })
const pwErrors   = reactive({ current: '', password: '', confirm: '' })
const changingPw = ref(false)
const pwSuccess  = ref(false)

function validatePwForm() {
  pwErrors.current  = ''
  pwErrors.password = ''
  pwErrors.confirm  = ''
  let ok = true
  if (!pwForm.current) { pwErrors.current  = 'Current password is required.'; ok = false }
  if (pwForm.password.length < 8) { pwErrors.password = 'Password must be at least 8 characters.'; ok = false }
  if (pwForm.password !== pwForm.confirm) { pwErrors.confirm = 'Passwords do not match.'; ok = false }
  return ok
}

async function submitPasswordChange() {
  if (!validatePwForm()) return
  changingPw.value = true
  pwSuccess.value  = false
  try {
    await auth.changePassword(pwForm.current, pwForm.password, pwForm.confirm)
    pwForm.current = ''; pwForm.password = ''; pwForm.confirm = ''
    pwSuccess.value = true
    showToast('Password changed.')
  } catch (err) {
    // Map backend field-level errors back to the form
    const errs = err.errors ?? {}
    pwErrors.current  = errs.current_password?.[0] ?? ''
    pwErrors.password = errs.password?.[0] ?? ''
    if (!pwErrors.current && !pwErrors.password) {
      pwErrors.current = err.message || 'Failed to change password.'
    }
  } finally {
    changingPw.value = false
  }
}

// ── Toast helper ──────────────────────────────────────────────────────────────
const toast = ref('')
function showToast(msg) {
  toast.value = msg
  setTimeout(() => toast.value = '', 3500)
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const data = await auth.fetchProfile()
    profileData.bio       = data.profile?.bio ?? ''
    profileData.cv_score  = data.profile?.cv_score ?? 0
    avatarPreview.value   = data.user?.avatar_url ?? null
    skills.value          = data.skills ?? []
  } catch {
    // Fallback: try the legacy skills endpoint
    try {
      const res = await auth.fetchSkills()
      skills.value = res.skills ?? []
    } catch { /* best-effort */ }
  }
})
</script>

<style scoped>
.toast-enter-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.toast-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.toast-enter-from   { opacity: 0; transform: translateY(8px); }
.toast-leave-to     { opacity: 0; transform: translateY(8px); }
</style>