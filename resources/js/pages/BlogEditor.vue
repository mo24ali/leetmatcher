<template>
  <div class="editor-page">
    <div class="editor-container">
      <div class="editor-header">
        <button class="back-btn" @click="router.push('/blogs')">&larr; Back to Blogs</button>
        <h1 class="page-title">{{ isEditing ? 'Edit Post' : 'Create New Post' }}</h1>
      </div>

      <div class="editor-card">
        <form @submit.prevent="savePost">
          <div class="form-group">
            <label>Title</label>
            <input type="text" v-model="form.title" placeholder="A catchy title..." required class="title-input"/>
          </div>

          <div class="form-row">
            <div class="form-group half">
              <label>Visibility</label>
              <select v-model="form.visibility" class="std-input">
                <option value="public">Public - visible to everyone</option>
                <option value="draft">Draft - only visible to you</option>
                <option value="private">Private - unlisted</option>
              </select>
            </div>
            
            <div class="form-group half">
              <label>Tags (comma separated)</label>
              <input type="text" v-model="tagsInput" placeholder="career, interview, tip" class="std-input"/>
            </div>
          </div>

          <div class="form-group flex-1">
            <label>Content (Markdown supported natively using standard text)</label>
            <textarea v-model="form.body" placeholder="Write your thoughts here..." required class="body-input"></textarea>
          </div>

          <div class="form-actions">
            <div class="status-indicator">
               <span v-if="saving" class="saving-text">Saving...</span>
            </div>
            <div class="action-btns">
              <button type="button" class="btn-cancel" @click="router.push('/blogs')">Cancel</button>
              <button type="submit" class="btn-save" :disabled="saving">
                {{ isEditing ? 'Update Post' : 'Publish Post' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const isEditing = ref(false)
const postId = route.params.id
const saving = ref(false)

const tagsInput = ref('')
const form = reactive({
  title: '',
  body: '',
  visibility: 'public'
})

onMounted(async () => {
  if (postId && postId !== 'create') {
    isEditing.value = true
    try {
      const res = await auth.apiFetch(`/v1/blog-posts/${postId}`)
      form.title = res.title
      form.body = res.body
      form.visibility = res.visibility || 'public'
      if (res.tags && res.tags.length) {
        tagsInput.value = res.tags.join(', ')
      }
    } catch (err) {
      alert('Failed to load post')
      router.push('/blogs')
    }
  }
})

const savePost = async () => {
  saving.value = true
  const payload = {
    ...form,
    tags: tagsInput.value.split(',').map(t => t.trim()).filter(Boolean)
  }

  try {
    if (isEditing.value) {
      await auth.apiFetch(`/v1/blog-posts/${postId}`, {
        method: 'PUT',
        body: JSON.stringify(payload)
      })
    } else {
      await auth.apiFetch(`/v1/blog-posts`, {
        method: 'POST',
        body: JSON.stringify(payload)
      })
    }
    router.push('/blogs')
  } catch (err) {
    console.error("Save error:", err)
    alert('Failed to save post: ' + (err.message || JSON.stringify(err)))
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.editor-page { min-height: 100vh; background: #eef2f6; padding: 3rem 1.5rem; font-family: 'Inter', sans-serif; display: flex; justify-content: center; }
.editor-container { width: 100%; max-width: 900px; display: flex; flex-direction: column; }

.editor-header { margin-bottom: 2rem; }
.back-btn { background: none; border: none; color: #64748b; font-weight: 700; cursor: pointer; padding: 0.5rem 0; margin-bottom: 1rem; display: inline-block; transition: 0.2s; }
.back-btn:hover { color: #0ea5e9; }
.page-title { font-size: 2.5rem; font-weight: 900; color: #0f172a; margin: 0; letter-spacing: -0.03em; }

.editor-card { background: white; border-radius: 1.5rem; padding: 2.5rem; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; flex-grow: 1; display: flex; flex-direction: column; }
form { display: flex; flex-direction: column; height: 100%; gap: 1.5rem; }

.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.flex-1 { flex-grow: 1; }
.form-row { display: flex; gap: 1.5rem; }
.half { flex: 1; }

label { font-size: 0.8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }

input, select, textarea { 
  background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.75rem; 
  font-family: inherit; color: #1e293b; padding: 1rem; transition: all 0.2s;
}
input:focus, select:focus, textarea:focus { outline: none; border-color: #0ea5e9; background: white; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1); }

.title-input { font-size: 1.5rem; font-weight: 700; padding: 1.25rem 1rem; border-radius: 1rem; }
.std-input { font-size: 1rem; }
.body-input { flex-grow: 1; min-height: 400px; resize: vertical; line-height: 1.6; font-size: 1.05rem; border-radius: 1rem; }

.form-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; margin-top: auto; }
.saving-text { color: #0ea5e9; font-weight: 700; font-size: 0.9rem; }
.action-btns { display: flex; gap: 1rem; }

.btn-cancel { padding: 0.75rem 1.5rem; background: white; border: 1px solid #cbd5e1; border-radius: 0.75rem; font-weight: 700; color: #64748b; cursor: pointer; transition: 0.2s; }
.btn-cancel:hover { background: #f1f5f9; color: #1e293b; }

.btn-save { padding: 0.75rem 2rem; background: #0ea5e9; border: none; border-radius: 0.75rem; font-weight: 800; color: white; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(14,165,233,0.3); }
.btn-save:hover:not(:disabled) { background: #0284c7; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(14,165,233,0.4); }
.btn-save:disabled { opacity: 0.7; cursor: wait; }
</style>
