import { reactive, computed } from 'vue'

const BASE_URL = 'http://127.0.0.1:8000/api'

// ─── Persisted state ─────────────────────────────────────────────────────────
// ─── Persisted state ─────────────────────────────────────────────────────────
function loadStoredAuth() {
    try {
        const raw = localStorage.getItem('lm_auth')
        if (raw) return JSON.parse(raw)
    } catch {
        /* ignore corrupt data */
    }
    return { user: null }
}

const stored = loadStoredAuth()

const state = reactive({
    user:  stored.user,
})

function persist() {
    localStorage.setItem('lm_auth', JSON.stringify({ user: state.user }))
}

// ─── Computed helpers ─────────────────────────────────────────────────────────
const isAuthenticated = computed(() => !!state.user)
const role            = computed(() => state.user?.role ?? 'guest')

// ─── API helper ───────────────────────────────────────────────────────────────
async function apiFetch(endpoint, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        ...(options.headers ?? {}),
    }
    const res = await fetch(`${BASE_URL}${endpoint}`, { 
        ...options, 
        headers, 
        credentials: 'include' 
    })

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
    if (data.user) {
        state.user  = data.user
        persist()
    }
    return data
}

async function register(payload) {
    const data = await apiFetch('/v1/register', {
        method: 'POST',
        body: JSON.stringify(payload),
    })
    if (data.user) {
        state.user  = data.user
        persist()
    }
    return data
}

/* OTP AUTHENTICATION (Commented for future use)
async function verifyOtp(email, otp) {
    const data = await apiFetch('/v1/otp/verify', {
        method: 'POST',
        body: JSON.stringify({ email, otp }),
    })
    if (data.user) {
        state.user = data.user
        persist()
    }
    return data
}
*/

async function logout() {
    try {
        await apiFetch('/v1/logout', { method: 'POST' })
    } catch { /* best-effort */ }
    state.user  = null
    persist()
}

async function fetchMe() {
    try {
        const data = await apiFetch('/v1/me')
        state.user = data
        persist()
        return data
    } catch {
        state.user  = null
        persist()
        return null
    }
}

async function uploadCv(file) {
    const formData = new FormData()
    formData.append('cv', file)

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const res = await fetch(`${BASE_URL}/v1/cv/upload`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        },
        credentials: 'include',
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
        apiFetch,
        // verifyOtp, // Future OTP verification logic
    }
}
