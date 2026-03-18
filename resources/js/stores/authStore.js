import { reactive, computed } from 'vue'

const BASE_URL = 'http://127.0.0.1:8000/api'

// ─── Persisted state ─────────────────────────────────────────────────────────
function loadStoredAuth() {
    try {
        const raw = localStorage.getItem('lm_auth')
        if (raw) return JSON.parse(raw)
    } catch {
        /* ignore corrupt data */
    }
    return { user: null, token: null }
}

const stored = loadStoredAuth()

const state = reactive({
    user:  stored.user,
    token: stored.token,
})

function persist() {
    localStorage.setItem('lm_auth', JSON.stringify({ user: state.user, token: state.token }))
}

// ─── Computed helpers ─────────────────────────────────────────────────────────
const isAuthenticated = computed(() => !!state.token && !!state.user)
const role            = computed(() => state.user?.role ?? 'guest')

// ─── API helper ───────────────────────────────────────────────────────────────
async function apiFetch(endpoint, options = {}) {
    const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        ...(state.token ? { Authorization: `Bearer ${state.token}` } : {}),
        ...(options.headers ?? {}),
    }
    const res = await fetch(`${BASE_URL}${endpoint}`, { ...options, headers })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
        const err = new Error(data.message ?? `HTTP ${res.status}`)
        err.errors = data.errors ?? {}
        err.status = res.status
        throw err
    }
    return data
}

// ─── Actions ──────────────────────────────────────────────────────────────────
async function login(email, password) {
    const data = await apiFetch('/v1/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    })
    state.user  = data.user
    state.token = data.token
    persist()
    return data.user
}

async function register(payload) {
    const data = await apiFetch('/v1/register', {
        method: 'POST',
        body: JSON.stringify(payload),
    })
    state.user  = data.user
    state.token = data.token
    persist()
    return data.user
}

async function logout() {
    try {
        await apiFetch('/v1/logout', { method: 'POST' })
    } catch { /* best-effort */ }
    state.user  = null
    state.token = null
    persist()
}

async function fetchMe() {
    if (!state.token) return null
    try {
        const data = await apiFetch('/v1/me')
        state.user = data
        persist()
        return data
    } catch {
        // token probably expired
        state.user  = null
        state.token = null
        persist()
        return null
    }
}

async function uploadCv(file) {
    const formData = new FormData()
    formData.append('cv', file)

    const res = await fetch(`${BASE_URL}/v1/cv/upload`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${state.token}`,
        },
        body: formData,
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
        const err = new Error(data.message ?? `HTTP ${res.status}`)
        err.errors = data.errors ?? {}
        throw err
    }
    return data
}

// ─── Public API ───────────────────────────────────────────────────────────────
export function useAuthStore() {
    return {
        state,
        isAuthenticated,
        role,
        login,
        register,
        logout,
        fetchMe,
        uploadCv,
    }
}
