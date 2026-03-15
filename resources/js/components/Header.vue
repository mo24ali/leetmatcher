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
</style>