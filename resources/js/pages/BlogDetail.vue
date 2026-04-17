<template>
  <div class="blog-detail-page">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
    </div>
    <div class="detail-container" v-else-if="post">
      <button class="back-btn" @click="router.push('/blogs')">&larr; Back to Blogs</button>
      
      <div class="post-header">
        <h1 class="post-title">{{ post.title }}</h1>
        <div class="post-meta">
          <div class="author-info">
             <div class="author-avatar" :style="{ backgroundImage: `url(${post.author?.avatar_url || '/default-avatar.png'})` }"></div>
             <div>
               <p class="author-name">{{ post.author?.name || 'Unknown' }}</p>
               <p class="author-role">{{ post.author?.role }}</p>
             </div>
          </div>
          <div class="post-date">
            <span class="date-icon">&#128197;</span> {{ formatDate(post.created_at) }}
            <span v-if="post.created_at !== post.updated_at" class="edited-badge">(Edited)</span>
          </div>
        </div>
      </div>

      <div class="post-body">
         <!-- In a real app we would use markdown parsing like marked.js -->
         <p v-for="(paragraph, index) in parseBody(post.body)" :key="index" class="body-paragraph">
           {{ paragraph }}
         </p>
      </div>

      <div class="post-tags" v-if="post.tags && post.tags.length">
         <span class="tag" v-for="tag in post.tags" :key="tag">#{{ tag }}</span>
      </div>
      
      <div class="owner-panel" v-if="isOwner">
         <h3>Owner Actions</h3>
         <div class="panel-actions">
           <button class="btn edit" @click="router.push(`/blogs/${post.id}/edit`)">Edit Post</button>
           <div class="stats">
              <span class="visibility-badge" :class="post.visibility">Visibility: {{ post.visibility }}</span>
              <span v-if="post.moderation_status" class="mod-badge">Status: {{ post.moderation_status }}</span>
           </div>
         </div>
      </div>
    </div>
    <div v-else class="not-found">
      Post not found or unavailable.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const loading = ref(true)
const post = ref(null)

onMounted(async () => {
  try {
    const res = await auth.apiFetch(`/v1/blog-posts/${route.params.id}`)
    post.value = res
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

const isOwner = computed(() => {
  return auth.isAuthenticated.value && auth.state.user?.id === post.value?.author_id
})

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

const parseBody = (body) => {
  if (!body) return []
  return body.split('\n').filter(p => p.trim() !== '')
}
</script>

<style scoped>
.blog-detail-page { min-height: 100vh; background: #fff; font-family: 'Inter', sans-serif; color: #1e293b; padding: 4rem 1.5rem; display: flex; justify-content: center; }
.detail-container { max-width: 800px; width: 100%; }

.loading-state { display: flex; justify-content: center; align-items: center; height: 50vh; }
.spinner { width: 40px; height: 40px; border: 3px solid #f1f5f9; border-top-color: #0ea5e9; border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { 100% { transform: rotate(360deg); } }

.back-btn { background: none; border: none; font-size: 0.9rem; font-weight: 700; color: #64748b; cursor: pointer; padding: 0; margin-bottom: 2rem; transition: 0.2s; }
.back-btn:hover { color: #0ea5e9; transform: translateX(-5px); }

.post-header { margin-bottom: 3rem; }
.post-title { font-size: 3rem; font-weight: 900; letter-spacing: -0.04em; line-height: 1.1; margin-bottom: 2rem; color: #0f172a; }

.post-meta { display: flex; justify-content: space-between; align-items: center; padding-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; }
.author-info { display: flex; align-items: center; gap: 1rem; }
.author-avatar { width: 3.5rem; height: 3.5rem; border-radius: 50%; background-position: center; background-size: cover; background-color: #e2e8f0; }
.author-name { font-weight: 800; font-size: 1.05rem; margin: 0; color: #0f172a; }
.author-role { font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin: 0.2rem 0 0; }

.post-date { font-size: 0.9rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
.edited-badge { font-size: 0.75rem; color: #94a3b8; }

.post-body { font-size: 1.15rem; line-height: 1.8; color: #334155; margin-bottom: 3rem; }
.body-paragraph { margin-bottom: 1.5rem; }

.post-tags { display: flex; gap: 0.5rem; margin-bottom: 3rem; }
.tag { font-size: 0.85rem; font-weight: 700; color: #0ea5e9; background: #e0f2fe; padding: 0.4rem 0.8rem; border-radius: 0.5rem; }

.owner-panel { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin-top: 3rem; }
.owner-panel h3 { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #64748b; margin: 0 0 1rem; }
.panel-actions { display: flex; justify-content: space-between; align-items: center; }
.btn.edit { background: white; border: 1px solid #cbd5e1; padding: 0.5rem 1.5rem; border-radius: 0.5rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
.btn.edit:hover { background: #f1f5f9; border-color: #94a3b8; }

.stats { display: flex; gap: 1rem; }
.visibility-badge { font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.7rem; border-radius: 999px; text-transform: uppercase; background: #e2e8f0; color: #475569; }
.visibility-badge.public { background: #dcfce7; color: #16a34a; }
.mod-badge { font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.7rem; border-radius: 999px; text-transform: uppercase; background: #fef3c7; color: #b45309; }
</style>
