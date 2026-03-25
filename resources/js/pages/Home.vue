<template>
  <div class="leet-splash">
    <!-- Clean minimal background -->
    <div class="minimal-bg"></div>
    
    <!-- Main split layout -->
    <div class="split-layout">
      <!-- Left side - Logo & Branding -->
      <div class="brand-side">
        <div class="brand-content">
          <!-- Minimalist logo - typography only -->
          <h1 class="logo">
            <span class="logo-leet">LEET</span>
            <span class="logo-matcher">MATCHER</span>
          </h1>
          
          <!-- Minimal tagline -->
          <p class="tagline">
            Match. Compete. Conquer.
          </p>
          
          <!-- Subtle divider -->
          <div class="divider-accent"></div>
          
          <!-- Clean description -->
          <p class="description">
            The platform where CS students<br>
            connect with top tech companies
          </p>
        </div>
      </div>

      <!-- Right side - Clean CTA buttons -->
      <div class="cta-side">
        <div class="cta-content">
          <h2 class="cta-title">Get started</h2>
          <p class="cta-subtitle">Access your account or create one</p>

          <!-- Login Button - Outlined -->
          <router-link to="/login" class="btn btn-outline">
            LOGIN
          </router-link>

          <!-- Register Button - Filled -->
          <router-link to="/register" class="btn btn-primary">
            REGISTER
          </router-link>

          <!-- Simple footer note -->
          <p class="footer-note">
            © 2026 LeetMatcher · All rights reserved
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>