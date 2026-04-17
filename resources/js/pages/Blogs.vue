<template>
  <div class="blog-page">
    <section class="blog-hero">
      <div class="hero-content">
        <h1 class="hero-title">Platform Insights</h1>
        <p class="hero-subtitle">Discover stories, experiences, and technical insights from recruiters and applicants.</p>
        <button class="create-post-btn" @click="router.push('/blogs/create')">
          <span class="btn-icon">&#10010;</span> Write a Post
        </button>
      </div>
    </section>

    <div class="blog-content">
      <div class="blog-filters">
        <button class="filter-btn" :class="{ active: viewMode === 'all' }" @click="setViewMode('all')">Community Feed</button>
        <button class="filter-btn" :class="{ active: viewMode === 'mine' }" @click="setViewMode('mine')">My Posts</button>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading posts...</p>
      </div>

      <div v-else-if="posts.length === 0" class="empty-state">
        <div class="empty-icon">&#128214;</div>
        <h3>No posts found</h3>
        <p>Be the first to share your experience on LeetMatcher.</p>
        <button class="create-post-btn secondary" @click="router.push('/blogs/create')">Start Writing</button>
      </div>

      <div class="blog-grid" v-else>
         <div class="blog-card" v-for="post in posts" :key="post.id">
            <div class="card-header">
              <div class="author-info">
                <div class="author-avatar" :style="{ backgroundImage: `url(${post.author?.avatar_url || '/default-avatar.png'})` }"></div>
                <div>
                  <p class="author-name">{{ post.author?.name || 'Unknown User' }}</p>
                  <p class="author-role">{{ post.author?.role || 'Member' }} &bull; {{ formatDate(post.created_at) }}</p>
                </div>
              </div>
              <div class="post-status" v-if="viewMode === 'mine'">
                <span :class="['status-badge', post.visibility]">{{ post.visibility }}</span>
                <span v-if="post.moderation_status !== 'approved'" :class="['mod-badge', post.moderation_status]">{{ post.moderation_status }}</span>
              </div>
            </div>
            
            <h2 class="post-title">{{ post.title }}</h2>
            <p class="post-preview">{{ getPreview(post.body) }}</p>

            <div class="post-tags" v-if="post.tags && post.tags.length">
              <span class="tag" v-for="tag in post.tags" :key="tag">#{{ tag }}</span>
            </div>

            <div class="card-actions">
               <button class="read-more-btn" @click="viewPost(post)">Read Full Post &rarr;</button>
               <div class="owner-actions" v-if="viewMode === 'mine'">
                  <button class="icon-btn edit" title="Edit" @click="router.push(`/blogs/${post.id}/edit`)">&#9998;</button>
                  <button class="icon-btn delete" title="Delete" @click="deletePost(post.id)">&#128465;</button>
               </div>
            </div>
         </div>
      </div>
      
      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)">Prev</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const auth = useAuthStore()

const loading = ref(true)
const posts = ref([])
const viewMode = ref('all') // 'all' or 'mine'
const currentPage = ref(1)
const totalPages = ref(1)

const fetchPosts = async () => {
  loading.value = true
  try {
    const url = viewMode.value === 'mine' 
      ? `/v1/blog-posts?mine=1&page=${currentPage.value}`
      : `/v1/blog-posts?page=${currentPage.value}`;
    
    const res = await auth.apiFetch(url);
    posts.value = res.data;
    currentPage.value = res.current_page;
    totalPages.value = res.last_page;
  } catch (err) {
    console.error('Error fetching blog posts', err);
  } finally {
    loading.value = false;
  }
}

const setViewMode = (mode) => {
  if (viewMode.value !== mode) {
    viewMode.value = mode;
    currentPage.value = 1;
    fetchPosts();
  }
}

const changePage = (page) => {
  currentPage.value = page;
  fetchPosts();
}

const getPreview = (body) => {
  if (!body) return 'No content provided.';
  // Strip simple markdown or HTML tags if needed, returning plain text slice
  const plainText = body.replace(/<\/?[^>]+(>|$)/g, "");
  return plainText.length > 150 ? plainText.substring(0, 150) + '...' : plainText;
}

const formatDate = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const viewPost = (post) => {
  // Navigation to a single view page, could be built next or a modal
  router.push(`/blogs/${post.id}`);
}

const deletePost = async (id) => {
  if (!confirm("Are you sure you want to delete this post?")) return;
  try {
    await auth.apiFetch(`/v1/blog-posts/${id}`, { method: 'DELETE' });
    posts.value = posts.value.filter(p => p.id !== id);
  } catch (error) {
    alert("Failed to delete post");
  }
}

onMounted(() => {
  fetchPosts()
})
</script>

<style scoped>
.blog-page { min-height: 100vh; background: #eef2f6; font-family: 'Inter', sans-serif; color: #1e293b; }

.blog-hero {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  padding: 5rem 2rem;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.blog-hero::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.2), transparent 50%);
}

.hero-content { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
.hero-title { font-size: 3rem; font-weight: 900; margin-bottom: 1rem; letter-spacing: -0.05em; }
.hero-subtitle { font-size: 1.2rem; color: #94a3b8; margin-bottom: 2.5rem; line-height: 1.5; }

.create-post-btn {
  background: #0ea5e9; color: white; padding: 1rem 2rem; border-radius: 999px;
  font-weight: 800; font-size: 1rem; border: none; cursor: pointer; transition: all 0.3s;
  box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.5);
  display: inline-flex; align-items: center; gap: 0.5rem;
}
.create-post-btn:hover { background: #0284c7; transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(14, 165, 233, 0.6); }

.blog-content { max-width: 1200px; margin: -2rem auto 3rem; position: relative; z-index: 10; padding: 0 1.5rem; }

.blog-filters { display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center; }
.filter-btn {
  background: white; border: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; border-radius: 0.75rem;
  font-weight: 700; color: #64748b; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}
.filter-btn.active { background: #0ea5e9; color: white; border-color: #0ea5e9; }
.filter-btn:hover:not(.active) { background: #f8fafc; color: #1e293b; }

.blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem; }

.blog-card {
  background: white; border-radius: 1.5rem; padding: 1.5rem;
  box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); transition: transform 0.3s, box-shadow 0.3s;
  display: flex; flex-direction: column; height: 100%; border: 1px solid rgba(255,255,255,0.5);
  backdrop-filter: blur(10px);
}
.blog-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); }

.card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
.author-info { display: flex; align-items: center; gap: 1rem; }
.author-avatar { width: 3rem; height: 3rem; border-radius: 50%; background-size: cover; background-position: center; background-color: #e2e8f0; }
.author-name { font-weight: 800; font-size: 0.95rem; margin: 0; color: #0f172a; }
.author-role { font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0; text-transform: uppercase; font-weight: 700; }

.status-badge { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.25rem 0.6rem; border-radius: 999px; }
.status-badge.public { background: #dcfce7; color: #16a34a; }
.status-badge.draft { background: #fef3c7; color: #d97706; }
.status-badge.private { background: #f1f5f9; color: #475569; }

.mod-badge { margin-top: 5px; display: inline-block; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; }
.mod-badge.pending { background: #fef3c7; color: #d97706; }
.mod-badge.rejected { background: #fee2e2; color: #dc2626; }

.post-title { font-size: 1.5rem; font-weight: 800; line-height: 1.3; margin-bottom: 1rem; color: #0f172a; }
.post-preview { color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem; flex-grow: 1; }

.post-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.tag { font-size: 0.75rem; font-weight: 700; color: #0ea5e9; background: #e0f2fe; padding: 0.25rem 0.6rem; border-radius: 0.5rem; }

.card-actions { display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; }
.read-more-btn { background: none; border: none; color: #0ea5e9; font-weight: 800; font-size: 0.9rem; cursor: pointer; padding: 0; transition: 0.2s; }
.read-more-btn:hover { color: #0284c7; }

.owner-actions { display: flex; gap: 0.5rem; }
.icon-btn { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; width: 2.2rem; height: 2.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; color: #475569; }
.icon-btn.edit:hover { background: #e0f2fe; color: #0ea5e9; border-color: #bae6fd; }
.icon-btn.delete:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }

.loading-state { text-align: center; padding: 5rem 0; color: #64748b; }
.spinner { width: 40px; height: 40px; border: 3px solid #e2e8f0; border-top-color: #0ea5e9; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { 100% { transform: rotate(360deg); } }

.empty-state { text-align: center; padding: 5rem 2rem; background: white; border-radius: 1.5rem; border: 1px dashed #cbd5e1; }
.empty-icon { font-size: 4rem; margin-bottom: 1rem; }
.empty-state h3 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
.empty-state p { color: #64748b; margin-bottom: 1.5rem; }
.create-post-btn.secondary { background: #1e293b; box-shadow: none; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 3rem; }
.pagination button { padding: 0.5rem 1.25rem; font-weight: 700; border-radius: 0.5rem; border: 1px solid #cbd5e1; background: white; cursor: pointer; transition: 0.2s; }
.pagination button:not(:disabled):hover { background: #f8fafc; border-color: #94a3b8; }
.pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
.pagination span { font-weight: 700; color: #475569; font-size: 0.9rem; }
</style>
