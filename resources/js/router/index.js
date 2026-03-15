import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import About from '../pages/About.vue'
import Welcome from '../pages/Welcome.vue'
import { compile } from 'vue'
import Register from '../pages/Register.vue'
import RegisterStudent from '../pages/Register-student.vue'
import RegisterRecruiter from '../pages/Register-recruiter.vue'
import Login from '../pages/Login.vue'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/about',
        name: 'About',
        component: About
    },
    {
        path: '/welcome',
        name: 'Welcome',
        component: Welcome
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/register-student',
        name: 'Register-student',
        component: RegisterStudent
    },
    {
        path: '/register-recruiter',
        name: 'Register-recruiter',
        component: RegisterRecruiter
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
