<template>
  <div class="min-h-screen bg-[#f8fafc] font-sans pb-24 text-slate-900">

    <!-- ── Hero / Page Header ───────────────────────────────────── -->
    <div class="bg-white border-b border-slate-200/60 pb-10 pt-12 relative overflow-hidden">
      <!-- Decorative background glow -->
      <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
      <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>

      <div class="max-w-6xl mx-auto px-6 relative">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div>
            <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">
              <router-link to="/dashboard/applicant" class="hover:text-slate-600 transition-colors">Settings</router-link>
              <span>/</span>
              <span class="text-slate-900">Profile</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Account Settings</h1>
            <p class="text-slate-500 mt-2 text-lg font-medium">Manage your professional identity and security.</p>
          </div>

          <!-- Quick Actions / Status -->
          <div class="flex items-center gap-4">
             <div class="bg-white rounded-2xl border border-slate-200 p-1 flex items-center shadow-sm">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap"
                  :class="activeTab === tab.id
                    ? 'bg-slate-900 text-white shadow-lg shadow-slate-200 scale-105'
                    : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                  @click="activeTab = tab.id"
                >
                  {{ tab.label }}
                </button>
             </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 mt-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <div class="lg:col-span-4 space-y-6">

          <!-- User Identity Card -->
          <section class="bg-white rounded-3xl border border-slate-200/60 p-8 shadow-sm relative group overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
               <button @click="startEditInfo" class="text-slate-400 hover:text-slate-900">
                 <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
               </button>
            </div>

            <div class="flex flex-col items-center text-center">
              <!-- Animated Avatar Circle -->
              <div class="relative mb-6">
                <div class="absolute -inset-2 bg-gradient-to-tr from-blue-500 to-indigo-600 rounded-full blur-sm opacity-20 group-hover:opacity-40 transition-opacity animate-pulse"></div>
                <div class="relative group">
                   <ProfileAvatar 
                     :url="avatarPreview" 
                     :name="auth.state.user?.name" 
                     size="xxl"
                     border
                     class="shadow-2xl transition-transform duration-500 group-hover:scale-105"
                   />

                   <!-- Overlay upload trigger -->
                   <label class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
                     <svg class="w-6 h-6 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                     <span class="text-[0.65rem] font-black uppercase tracking-widest">Update</span>
                     <input type="file" accept="image/*" class="hidden" @change="handleAvatarSelect" />
                   </label>
                 </div>

                <!-- Status indicator -->
                <div class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-4 border-white rounded-full shadow-md"></div>
              </div>

              <h2 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-1">{{ auth.state.user?.name }}</h2>
              <p class="text-slate-400 font-medium text-sm mb-4">{{ auth.state.user?.email }}</p>

              <div class="flex items-center gap-2 mb-6">
                <span
                  class="px-3 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-widest shadow-sm"
                  :class="roleBadgeClass"
                >
                  {{ auth.role.value }}
                </span>
                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                <span class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Member since 2024</span>
              </div>

              <div v-if="pendingAvatar" class="flex flex-col gap-2 w-full">
                <p class="text-[0.65rem] font-bold text-blue-600 mb-1">New photo selected!</p>
                <div class="flex gap-2">
                  <button
                    class="flex-1 bg-blue-600 text-white text-xs font-black py-2.5 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100 disabled:opacity-50"
                    :disabled="savingAvatar"
                    @click="saveAvatar"
                  >
                    {{ savingAvatar ? 'Uploading…' : 'Save Changes' }}
                  </button>
                  <button @click="cancelAvatar" class="px-3 py-2.5 rounded-xl bg-slate-50 text-slate-400 hover:text-slate-600 border border-slate-100 transition text-xs">
                    Cancel
                  </button>
                </div>
              </div>
            </div>

            <!-- Bio section -->
            <div class="mt-8 pt-8 border-t border-slate-100">
              <div class="flex justify-between items-center mb-4 font-bold text-[0.65rem] uppercase tracking-widest text-slate-400">
                <span>About Me</span>
              </div>
              <p class="text-sm text-slate-600 leading-relaxed italic" v-if="profileData.bio">
                "{{ profileData.bio }}"
              </p>
              <p class="text-sm text-slate-300 leading-relaxed italic" v-else>
                No bio provided yet. Click the edit icon to share your story.
              </p>
            </div>
          </section>

          <!-- Profile Strength -->
          <section class="bg-white rounded-3xl border border-slate-200/60 p-8 shadow-sm">
            <div class="flex justify-between items-center mb-6">
               <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Profile Strength</h3>
               <span class="text-xl font-black text-blue-600">{{ completenessScore }}%</span>
            </div>

            <!-- Modern Progress Bar -->
            <div class="relative h-4 bg-slate-100 rounded-full overflow-hidden mb-6 group">
              <div
                class="absolute inset-y-0 left-0 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 transition-all duration-1000 ease-out"
                :style="{ width: completenessScore + '%' }"
              >
                <!-- Animated stripes -->
                <div class="absolute inset-0 opacity-20 bg-[linear-gradient(45deg,rgba(255,255,255,.2)_25%,transparent_25%,transparent_50%,rgba(255,255,255,.2)_50%,rgba(255,255,255,.2)_75%,transparent_75%,transparent)] bg-[length:24px_24px] animate-[slide_1s_linear_infinite]"></div>
              </div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed font-medium">
              {{ completenessHint }}
            </p>
          </section>

        </div>

        <!-- ── Right Column: Dynamic Content ──────────────────────── -->
        <div class="lg:col-span-8 flex flex-col gap-8">

          <!-- ═══════════════════════════════════════════ OVERVIEW ══ -->
          <div v-if="activeTab === 'overview'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 space-y-8">

            <!-- Information Cards -->
            <section class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
              <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                 <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                   <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                   Personal Information
                 </h3>
                 <button v-if="!editingInfo" @click="startEditInfo" class="text-[0.7rem] font-extrabold text-blue-600 hover:text-blue-700 uppercase tracking-widest">Edit</button>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-1">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Full Name</label>
                    <div v-if="editingInfo">
                       <input v-model="infoDraft.name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-blue-500 focus:bg-white transition-all outline-none" />
                    </div>
                    <p v-else class="text-base font-bold text-slate-900">{{ auth.state.user?.name || '—' }}</p>
                  </div>

                  <div class="space-y-1">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Email Address</label>
                    <p class="text-base font-bold text-slate-900">{{ auth.state.user?.email || '—' }}</p>
                  </div>

                  <div class="md:col-span-2 space-y-1">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Biography</label>
                    <div v-if="editingInfo">
                       <textarea v-model="infoDraft.bio" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-blue-500 focus:bg-white transition-all outline-none resize-none"></textarea>
                    </div>
                    <p v-else class="text-base font-medium text-slate-600 leading-relaxed">{{ profileData.bio || 'Your professional storyline goes here…' }}</p>
                  </div>
                </div>

                <!-- Edit Actions -->
                <div v-if="editingInfo" class="mt-8 flex items-center gap-3 animate-in slide-in-from-top-1 transition-all">
                  <button
                    @click="saveInfo"
                    class="bg-blue-600 text-white text-xs font-black px-6 py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-100 disabled:opacity-50"
                    :disabled="savingInfo"
                  >
                    {{ savingInfo ? 'Saving…' : 'Save Changes' }}
                  </button>
                  <button @click="cancelEditInfo" class="text-xs font-bold text-slate-400 hover:text-slate-600 px-4">Cancel</button>
                </div>

                <p v-if="infoError" class="mt-4 text-[0.7rem] font-bold text-red-500">{{ infoError }}</p>
              </div>
            </section>
          </div>

          <!-- ══════════════════════════════════════════════ SKILLS ══ -->
          <div v-if="activeTab === 'skills'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 space-y-8">
            <section class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
               <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                 <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                   <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3 1.912 5.886h6.19l-5.007 3.638L16.907 18.41 12 14.773 7.093 18.41l1.812-5.886-5.007-3.638h6.19L12 3z"/></svg>
                   Professional Core Skills
                 </h3>
               </div>

               <div class="p-8">
                 <div class="flex flex-wrap gap-3 mb-10">
                   <div
                    v-for="skill in skills"
                    :key="skill.id"
                    class="group flex items-center gap-3 bg-slate-50 hover:bg-white hover:border-slate-300 border border-transparent px-4 py-2.5 rounded-2xl transition-all duration-200"
                   >
                     <div class="flex flex-col">
                        <span class="text-[0.85rem] font-bold text-slate-800">{{ skill.name }}</span>
                        <span class="text-[0.6rem] font-black uppercase text-indigo-400 tracking-widest">{{ skill.proficiency }}</span>
                     </div>
                     <button
                      @click="deleteSkill(skill)"
                      class="w-6 h-6 rounded-full flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                     >&times;</button>
                   </div>
                   <div v-if="!skills.length" class="w-full text-center py-10 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 text-sm font-medium">
                      Identify and add your top skills to increase your match score.
                   </div>
                 </div>

                 <!-- Manual Skill Form -->
                 <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-100">
                    <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-6">Add Skill Manually</p>

                    <div class="flex flex-col sm:flex-row gap-4">
                      <div class="flex-1 relative group">
                        <input
                          v-model="newSkill.name"
                          placeholder="e.g. TypeScript"
                          class="w-full pl-4 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm transition-all focus:border-indigo-500 outline-none"
                          @keydown.enter.prevent="submitAddSkill"
                        />
                      </div>

                      <select v-model="newSkill.proficiency" class="bg-white border border-slate-200 rounded-2xl px-4 py-3.5 text-xs font-bold text-slate-600 outline-none focus:border-indigo-500 transition-all">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                      </select>

                      <button
                        @click="submitAddSkill"
                        class="bg-indigo-600 text-white text-xs font-black px-8 py-3.5 rounded-2xl hover:bg-slate-900 transition-all duration-300 shadow-xl shadow-indigo-100 disabled:opacity-50"
                        :disabled="!newSkill.name.trim() || addingSkill"
                      >
                        {{ addingSkill ? '...' : 'Add Skill' }}
                      </button>
                    </div>
                    <p v-if="skillError" class="mt-3 text-[0.7rem] font-bold text-red-500">{{ skillError }}</p>
                 </div>
               </div>
            </section>
          </div>

          <!-- ══════════════════════════════════════════════════ CV ══ -->
          <div v-if="activeTab === 'cv'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 space-y-8">
             <section class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
               <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                 <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                   <svg class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                   Extracted CV Metadata
                 </h3>
                 <router-link to="/dashboard/applicant" class="text-[0.7rem] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest">Update CV</router-link>
               </div>

               <div class="p-8">
                  <div v-if="!hasCvData" class="py-20 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                       <svg class="w-10 h-10 text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                    </div>
                    <p class="text-slate-900 font-bold mb-1">No CV data detected</p>
                    <p class="text-xs text-slate-400 mb-8 max-w-xs">Upload your resume to automatically fill your professional profile with data points.</p>
                    <router-link to="/dashboard/applicant" class="bg-slate-900 text-white text-[0.7rem] font-black px-8 py-3 rounded-xl hover:bg-slate-800 transition">Upload Resume</router-link>
                  </div>

                  <template v-else>
                    <!-- Visual Metadata blocks -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-12 mb-10">
                       <div v-if="cvData.name" class="space-y-1">
                          <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Full Name</label>
                          <p class="text-base font-bold text-slate-900">{{ cvData.name }}</p>
                       </div>
                       <div v-if="cvData.email" class="space-y-1">
                          <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Contact Email</label>
                          <p class="text-base font-bold text-slate-900">{{ cvData.email }}</p>
                       </div>
                       <div v-if="cvData.phone" class="space-y-1">
                          <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Phone</label>
                          <p class="text-base font-bold text-slate-900">{{ cvData.phone }}</p>
                       </div>
                       <div v-if="cvData.location" class="space-y-1">
                          <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Location</label>
                          <p class="text-base font-bold text-slate-900">{{ cvData.location }}</p>
                       </div>
                    </div>

                    <div v-if="cvData.experience?.length" class="pt-8 border-t border-slate-50">
                       <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-6">Work Experience Highlights</p>
                       <ul class="space-y-3">
                          <li v-for="item in cvData.experience" :key="item" class="flex items-start gap-4 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 group hover:bg-white hover:border-slate-300 transition-colors">
                             <span class="w-1.5 h-1.5 bg-slate-300 rounded-full mt-2 shrink-0 group-hover:bg-blue-500"></span>
                             <p class="text-sm font-medium text-slate-700">{{ item }}</p>
                          </li>
                       </ul>
                    </div>
                  </template>
               </div>
             </section>
          </div>

          <!-- ══════════════════════════════════════════════ PASSWORD ══ -->
          <div v-if="activeTab === 'password'" class="animate-in fade-in slide-in-from-bottom-2 duration-500 max-w-xl">
             <section class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
               <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                 <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                   <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3L15.5 7.5z"/></svg>
                   Security Settings
                 </h3>
               </div>

               <div class="p-8">
                 <form @submit.prevent="submitPasswordChange" class="space-y-6">
                    <div class="space-y-2">
                      <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Current Password</label>
                      <input
                        v-model="pwForm.current"
                        type="password"
                        placeholder="••••••••"
                        class="w-full h-12 px-4 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-red-500 focus:bg-white transition-all text-sm"
                        :class="{'border-red-300': pwErrors.current}"
                      />
                      <p v-if="pwErrors.current" class="text-[0.7rem] font-bold text-red-500">{{ pwErrors.current }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">New Password</label>
                        <input
                          v-model="pwForm.password"
                          type="password"
                          placeholder="••••••••"
                          class="w-full h-12 px-4 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-slate-900 focus:bg-white transition-all text-sm"
                          :class="{'border-red-300': pwErrors.password}"
                        />
                         <p v-if="pwErrors.password" class="text-[0.7rem] font-bold text-red-500">{{ pwErrors.password }}</p>
                      </div>
                      <div class="space-y-2">
                        <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Confirm Password</label>
                        <input
                          v-model="pwForm.confirm"
                          type="password"
                          placeholder="••••••••"
                          class="w-full h-12 px-4 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-slate-900 focus:bg-white transition-all text-sm"
                           :class="{'border-red-300': pwErrors.confirm}"
                        />
                         <p v-if="pwErrors.confirm" class="text-[0.7rem] font-bold text-red-500">{{ pwErrors.confirm }}</p>
                      </div>
                    </div>

                    <div v-if="pwSuccess" class="p-4 bg-green-50 rounded-2xl border border-green-200 text-green-700 text-sm font-bold flex items-center gap-3">
                       <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                       Password updated effectively.
                    </div>

                    <button
                      type="submit"
                      class="bg-slate-900 text-white text-xs font-black px-10 py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl shadow-slate-100 disabled:opacity-50"
                      :disabled="changingPw"
                    >
                      {{ changingPw ? 'Processing…' : 'Update Security' }}
                    </button>
                 </form>
               </div>
             </section>
          </div>

        </div>
      </div>
    </div>

    <!-- Enhanced Global Toast -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform translate-y-10 opacity-0 scale-95"
      enter-to-class="transform translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="toast"
        class="fixed bottom-10 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-slate-900 text-white
               px-8 py-4 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] z-[100] border border-white/10 backdrop-blur-md"
      >
        <div class="w-4 h-4 rounded-full bg-blue-500 shadow-[0_0_10px_#3b82f6] animate-pulse"></div>
        <span class="text-sm font-black tracking-wide">{{ toast }}</span>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import ProfileAvatar from '../components/ProfileAvatar.vue'

const auth = useAuthStore()

const tabs = [
  { id: 'overview', label: 'Overview'  },
  { id: 'skills',   label: 'Skills'    },
  { id: 'cv',       label: 'CV Parser' },
  { id: 'password', label: 'Security'  },
]
const activeTab = ref('overview')

const profileData = reactive({ bio: '', cv_score: 0 })
const skills      = ref([])
const cvData      = reactive({
  name: '', email: '', phone: '', location: '',
  summary: '', experience: [], education: [], certifications: [],
})

const initials = computed(() => {
  const name = auth.state.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?'
})

const hasCvData = computed(() =>
  cvData.name || cvData.email || cvData.experience.length || cvData.education.length
)

const roleBadgeClass = computed(() => ({
  applicant: 'bg-blue-100 text-blue-700',
  recruiter: 'bg-emerald-100 text-emerald-700',
  admin:     'bg-amber-100 text-amber-700',
}[auth.role.value] ?? 'bg-slate-100 text-slate-600'))

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
  if (s === 100) return 'Perfect! Your profile is a beacon for potential matches.'
  if (s >= 70)   return 'You are almost there. Adding your professional bio will seal the deal.'
  return 'A stronger profile leads to 4x higher matching frequency. Start by adding key skills.'
})

const avatarPreview  = ref(null)
const pendingAvatar  = ref(null)
const savingAvatar   = ref(false)

function handleAvatarSelect(e) {
  const file = e.target.files[0]
  if (!file) return
  pendingAvatar.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

function cancelAvatar() {
  pendingAvatar.value = null
  avatarPreview.value = auth.state.user?.avatar_url ?? null
}

async function saveAvatar() {
  if (!pendingAvatar.value) return
  savingAvatar.value = true
  try {
    const res = await auth.uploadAvatar(pendingAvatar.value)
    avatarPreview.value = res.avatar_url
    pendingAvatar.value = null
    showToast('Photo uploaded.')
  } catch (err) {
    showToast('Upload failed.')
  } finally {
    savingAvatar.value = false
  }
}

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
}

async function saveInfo() {
  if (!infoDraft.name.trim()) return
  savingInfo.value = true
  try {
    await auth.updateProfile({ name: infoDraft.name.trim(), bio: infoDraft.bio.trim() })
    profileData.bio = infoDraft.bio.trim()
    editingInfo.value = false
    showToast('Identity updated.')
  } catch (err) {
    infoError.value = 'Sync failed.'
  } finally {
    savingInfo.value = false
  }
}

const newSkill   = reactive({ name: '', proficiency: 'intermediate' })
const addingSkill = ref(false)
const skillError  = ref('')

async function submitAddSkill() {
  if (!newSkill.name.trim()) return
  addingSkill.value = true
  try {
    const res = await auth.addSkill(newSkill.name, newSkill.proficiency)
    skills.value.push(res.skill)
    newSkill.name = ''
    showToast('Skill indexed.')
  } catch (err) {
    skillError.value = err.message
  } finally {
    addingSkill.value = false
  }
}

async function deleteSkill(skill) {
  try {
    await auth.removeSkill(skill.id)
    skills.value = skills.value.filter(s => s.id !== skill.id)
    showToast('Skill removed.')
  } catch {}
}

const pwForm = reactive({ current: '', password: '', confirm: '' })
const pwErrors   = reactive({ current: '', password: '', confirm: '' })
const changingPw = ref(false)
const pwSuccess  = ref(false)

async function submitPasswordChange() {
  changingPw.value = true
  pwSuccess.value  = false
  Object.keys(pwErrors).forEach(k => pwErrors[k] = '')
  try {
    await auth.changePassword(pwForm.current, pwForm.password, pwForm.confirm)
    pwForm.current = ''; pwForm.password = ''; pwForm.confirm = ''
    pwSuccess.value = true
    showToast('Security updated.')
  } catch (err) {
    const e = err.errors || {}
    pwErrors.current  = e.current_password?.[0]
    pwErrors.password = e.password?.[0]
    if (!pwErrors.current && !pwErrors.password) pwErrors.current = err.message
  } finally {
    changingPw.value = false
  }
}

const toast = ref('')
function showToast(msg) {
  toast.value = msg
  setTimeout(() => toast.value = '', 4000)
}

onMounted(async () => {
  try {
    const data = await auth.fetchProfile()
    profileData.bio = data.profile?.bio ?? ''
    avatarPreview.value = data.user?.avatar_url ?? null
    skills.value = data.skills ?? []
    // Extract CV details if existing
    if (data.cv) {
       Object.assign(cvData, data.cv)
    }
  } catch {}
})
</script>

<style>
@keyframes slide {
  from { background-position: 0 0; }
  to { background-position: 24px 0; }
}
</style>