import { reactive, computed } from 'vue'

const BASE_URL = typeof window !== 'undefined' ? `${window.location.origin}/api` : 'http://127.0.0.1:8000/api'

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
    user: stored.user,
})

function persist() {
    localStorage.setItem('lm_auth', JSON.stringify({ user: state.user }))
}

const isAuthenticated = computed(() => !!state.user)
const role            = computed(() => state.user?.role ?? 'guest')

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
        credentials: 'include',
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

// ─── Auth ─────────────────────────────────────────────────────────────────────

async function login(email, password) {
    const data = await apiFetch('/v1/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    })
    if (data.user) {
        state.user = data.user
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
        state.user = data.user
        persist()
    }
    return data
}

async function logout() {
    try {
        await apiFetch('/v1/logout', { method: 'POST' })
    } catch { /* best-effort */ }
    state.user = null
    persist()
}

async function fetchMe() {
    try {
        const data = await apiFetch('/v1/me')
        state.user = data
        persist()
        return data
    } catch {
        state.user = null
        persist()
        return null
    }
}

// ─── CV Upload ────────────────────────────────────────────────────────────────

async function uploadCv(file) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const formData = new FormData()
    formData.append('cv', file)

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

// ─── Profile ──────────────────────────────────────────────────────────────────

async function fetchProfile() {
    return await apiFetch('/v1/profile')
}

async function updateProfile(payload) {
    const data = await apiFetch('/v1/profile', {
        method: 'PATCH',
        body: JSON.stringify(payload),
    })
    if (data.user) {
        state.user = { ...state.user, ...data.user }
        persist()
    }
    return data
}

async function changePassword(currentPassword, password, passwordConfirmation) {
    return await apiFetch('/v1/profile/password', {
        method: 'POST',
        body: JSON.stringify({
            current_password:      currentPassword,
            password:              password,
            password_confirmation: passwordConfirmation,
        }),
    })
}

async function uploadAvatar(file) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const formData = new FormData()
    formData.append('avatar', file)

    const res = await fetch(`${BASE_URL}/v1/profile/avatar`, {
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

// ─── Skills ───────────────────────────────────────────────────────────────────

async function fetchSkills() {
    return await apiFetch('/v1/profile/skills')
}

async function addSkill(name, proficiency = 'intermediate') {
    return await apiFetch('/v1/profile/skills', {
        method: 'POST',
        body: JSON.stringify({ name, proficiency }),
    })
}

async function removeSkill(skillId) {
    return await apiFetch(`/v1/profile/skills/${skillId}`, { method: 'DELETE' })
}

// ─── Export ───────────────────────────────────────────────────────────────────

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
        fetchProfile,
        updateProfile,
        changePassword,
        uploadAvatar,
        fetchSkills,
        addSkill,
        removeSkill,
        apiFetch,
    }
}
