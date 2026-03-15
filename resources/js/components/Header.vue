<template>
    <header class="minimal-header">
        <div class="container">
            <nav class="nav-container">
                <!-- Minimalist logo - typography only -->
                <div class="logo">
                    <span class="logo-leet">LEET</span>
                    <span class="logo-matcher">MATCHER</span>
                </div>
                
                <!-- Clean navigation links -->
                <ul class="nav-links">
                    <li>
                        <router-link 
                            to="/" 
                            class="nav-link"
                            :class="{ 'active': isActive('/') }"
                        >
                            Home
                        </router-link>
                    </li>
                    <li>
                        <router-link 
                            to="/about" 
                            class="nav-link"
                            :class="{ 'active': isActive('/about') }"
                        >
                            About
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            to="/welcome"
                            class="nav-link"
                            :class="{ 'active': isActive('/welcome')}" 
                        >
                            Welcome
                        </router-link>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const isActive = (path) => route.path === path
</script>

<style scoped>
/* Minimalist monochrome variables */
:root {
    --black: #000000;
    --white: #ffffff;
    --gray-50: #fafafa;
    --gray-100: #f5f5f5;
    --gray-200: #e5e5e5;
    --gray-300: #d4d4d4;
    --gray-400: #a3a3a3;
    --gray-500: #737373;
    --gray-600: #525252;
    --gray-700: #404040;
    --gray-800: #262626;
    --gray-900: #171717;
}

/* Header container */
.minimal-header {
    background-color: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.98);
}

/* Container for consistent width */
.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Navigation container */
.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Minimalist logo - pure typography */
.logo {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
    user-select: none;
}

.logo-leet {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--gray-900);
    line-height: 1.2;
}

.logo-matcher {
    font-size: 1.25rem;
    font-weight: 400;
    letter-spacing: -0.01em;
    color: var(--gray-500);
    line-height: 1.2;
}

/* Navigation links */
.nav-links {
    display: flex;
    gap: 2.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-link {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--gray-600);
    text-decoration: none;
    letter-spacing: -0.01em;
    transition: color 0.2s ease;
    position: relative;
    padding: 0.5rem 0;
}

.nav-link:hover {
    color: var(--gray-900);
}

/* Active link styling - subtle and minimal */
.nav-link.active {
    color: var(--gray-900);
    font-weight: 500;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background-color: var(--gray-900);
    border-radius: 1px;
}

/* Responsive design */
@media (max-width: 640px) {
    .container {
        padding: 0 1.5rem;
    }

    .nav-container {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .nav-links {
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .logo-leet {
        font-size: 1.25rem;
    }

    .logo-matcher {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .nav-links {
        gap: 1rem;
    }

    .nav-link {
        font-size: 0.85rem;
    }
}

/* Optional: Add a subtle shadow on scroll (add with JS if needed) */
.minimal-header.scrolled {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
</style>