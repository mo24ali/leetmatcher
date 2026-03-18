import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

// ─── Page imports ─────────────────────────────────────────────────────────────
import Home              from '../pages/Home.vue'
import About             from '../pages/About.vue'
import Welcome           from '../pages/Welcome.vue'
import Register          from '../pages/Register.vue'
import RegisterStudent   from '../pages/Register-student.vue'
import RegisterRecruiter from '../pages/Register-recruiter.vue'
import Login             from '../pages/Login.vue'

// Dashboards (lazy-loaded)
const ApplicantDashboard = () => import('../pages/dashboards/ApplicantDashboard.vue')
const RecruiterDashboard = () => import('../pages/dashboards/RecruiterDashboard.vue')
const AdminDashboard     = () => import('../pages/dashboards/AdminDashboard.vue')
const Forbidden          = () => import('../pages/Forbidden.vue')

// ─── Route definitions ────────────────────────────────────────────────────────
const routes = [
    // Public
    { path: '/',        name: 'Home',              component: Home },
    { path: '/about',   name: 'About',             component: About },
    { path: '/welcome', name: 'Welcome',           component: Welcome },
    { path: '/register',            name: 'Register',          component: Register },
    { path: '/register-student',    name: 'Register-student',  component: RegisterStudent },
    { path: '/register-recruiter',  name: 'Register-recruiter',component: RegisterRecruiter },
    { path: '/login',   name: 'Login',             component: Login },
    { path: '/403',     name: 'Forbidden',         component: Forbidden },

    // Protected — role-gated dashboards
    {
        path: '/dashboard/applicant',
        name: 'ApplicantDashboard',
        component: ApplicantDashboard,
        meta: { requiresAuth: true, role: 'applicant' },
    },
    {
        path: '/dashboard/recruiter',
        name: 'RecruiterDashboard',
        component: RecruiterDashboard,
        meta: { requiresAuth: true, role: 'recruiter' },
    },
    {
        path: '/dashboard/admin',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: { requiresAuth: true, role: 'admin' },
    },

    // Catch-all
    { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// ─── Global navigation guard ──────────────────────────────────────────────────
router.beforeEach((to, _from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth) {
        if (!auth.isAuthenticated.value) {
            // Not logged in — send to login
            return next({ name: 'Login', query: { redirect: to.fullPath } })
        }

        if (to.meta.role && auth.role.value !== to.meta.role) {
            // Wrong role — send to 403
            return next({ name: 'Forbidden' })
        }
    }

    next()
})

export default router

// ─── Helper: redirect to the correct dashboard for the current user ───────────
export function dashboardRouteForRole(role) {
    const map = {
        applicant: '/dashboard/applicant',
        recruiter: '/dashboard/recruiter',
        admin:     '/dashboard/admin',
    }
    return map[role] ?? '/'
}
