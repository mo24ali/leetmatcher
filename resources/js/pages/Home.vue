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
          <div class="divider"></div>
          
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
// No script needed
</script>

<style scoped>
/* ---------- RESET & VARIABLES ---------- */
.leet-splash {
  --black: #000000;
  --white: #ffffff;
  --gray-100: #f5f5f5;
  --gray-200: #e5e5e5;
  --gray-300: #d4d4d4;
  --gray-400: #a3a3a3;
  --gray-500: #737373;
  --gray-600: #525252;
  --gray-700: #404040;
  --gray-800: #262626;
  --gray-900: #171717;
  
  min-height: 100vh;
  width: 100%;
  background-color: var(--white);
  color: var(--gray-900);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  position: relative;
}

/* Minimal background - very subtle grid */
.minimal-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: 
    linear-gradient(var(--gray-200) 1px, transparent 1px),
    linear-gradient(90deg, var(--gray-200) 1px, transparent 1px);
  background-size: 80px 80px;
  opacity: 0.3;
  z-index: 0;
}

/* ---------- SPLIT LAYOUT ---------- */
.split-layout {
  position: relative;
  z-index: 1;
  display: flex;
  min-height: 100vh;
  width: 100%;
}

/* Left side */
.brand-side {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  background-color: var(--white);
  border-right: 1px solid var(--gray-200);
}

.brand-content {
  max-width: 480px;
  padding: 2rem;
}

/* Minimalist logo - pure typography */
.logo {
  margin-bottom: 2rem;
  line-height: 1.1;
}

.logo-leet {
  display: block;
  font-size: 4rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.logo-matcher {
  display: block;
  font-size: 2.5rem;
  font-weight: 400;
  letter-spacing: -0.01em;
  color: var(--gray-600);
}

/* Clean tagline */
.tagline {
  font-size: 1.25rem;
  font-weight: 400;
  color: var(--gray-500);
  margin-bottom: 2rem;
  line-height: 1.6;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Subtle divider */
.divider {
  width: 60px;
  height: 2px;
  background-color: var(--gray-300);
  margin: 2rem 0;
}

/* Description */
.description {
  font-size: 1rem;
  line-height: 1.8;
  color: var(--gray-600);
  font-weight: 400;
  max-width: 360px;
}

/* ---------- RIGHT SIDE ---------- */
.cta-side {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  background-color: var(--gray-100);
}

.cta-content {
  max-width: 360px;
  width: 100%;
  padding: 2rem;
}

.cta-title {
  font-size: 2rem;
  font-weight: 600;
  letter-spacing: -0.02em;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.cta-subtitle {
  font-size: 1rem;
  color: var(--gray-500);
  margin-bottom: 3rem;
  font-weight: 400;
}

/* Button base styles - clean and minimal */
.btn {
  display: block;
  width: 100%;
  padding: 1rem 2rem;
  margin-bottom: 1rem;
  border-radius: 6px;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  font-size: 1rem;
  letter-spacing: 0.02em;
  text-decoration: none;
  text-align: center;
  transition: all 0.2s ease;
  cursor: pointer;
}

/* Outlined button */
.btn-outline {
  background: transparent;
  border: 1.5px solid var(--gray-900);
  color: var(--gray-900);
}

.btn-outline:hover {
  background: var(--gray-900);
  color: var(--white);
}

/* Primary button */
.btn-primary {
  background: var(--gray-900);
  border: 1.5px solid var(--gray-900);
  color: var(--white);
}

.btn-primary:hover {
  background: var(--gray-800);
  border-color: var(--gray-800);
}

/* Footer note */
.footer-note {
  margin-top: 3rem;
  font-size: 0.75rem;
  color: var(--gray-400);
  text-align: center;
  letter-spacing: 0.02em;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 768px) {
  .split-layout {
    flex-direction: column;
  }

  .brand-side {
    border-right: none;
    border-bottom: 1px solid var(--gray-200);
    padding: 3rem 1.5rem;
    min-height: auto;
  }

  .brand-content {
    text-align: center;
    padding: 1rem;
  }

  .logo-leet {
    font-size: 3rem;
  }

  .logo-matcher {
    font-size: 2rem;
  }

  .divider {
    margin-left: auto;
    margin-right: auto;
  }

  .description {
    margin-left: auto;
    margin-right: auto;
  }

  .cta-side {
    padding: 3rem 1.5rem;
  }

  .cta-content {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .logo-leet {
    font-size: 2.5rem;
  }

  .logo-matcher {
    font-size: 1.5rem;
  }

  .tagline {
    font-size: 1rem;
  }

  .cta-title {
    font-size: 1.75rem;
  }
}

/* Font imports */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
</style>