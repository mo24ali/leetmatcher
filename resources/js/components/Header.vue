<template>
    <header class="bg-blue-600 text-white py-4 mb-6">
        <div class="container mx-auto">
            <nav class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">LeetMatcher</h1>
                <ul class="flex gap-6">
                    <li>
                        <router-link 
                            to="/" 
                            class="hover:text-blue-200 transition"
                            :class="{ 'text-blue-200': isActive('/') }"
                        >
                            Home
                        </router-link>
                    </li>
                    <li>
                        <router-link 
                            to="/about" 
                            class="hover:text-blue-200 transition"
                            :class="{ 'text-blue-200': isActive('/about') }"
                        >
                            About
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
