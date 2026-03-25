<template>
  <div class="relative min-h-screen bg-white font-sans overflow-hidden">
    
    <!-- Restore the subtle grid background from the original design -->
    <div 
      class="fixed inset-0 pointer-events-none opacity-[0.15] z-0"
      style="background-image: linear-gradient(#e5e5e5 1px, transparent 1px), linear-gradient(90deg, #e5e5e5 1px, transparent 1px); background-size: 80px 80px;"
    ></div>
    
    <!-- Main split layout structure -->
    <div class="relative z-10 flex flex-col md:flex-row min-h-screen w-full">
      
      <!-- Left side - Logo & Branding (White background) -->
      <div class="flex-1 flex items-center justify-center p-12 md:p-16 border-b md:border-b-0 md:border-r border-gray-200 bg-white shadow-2xl md:shadow-none">
        <div class="max-w-md w-full animate-in fade-in slide-in-from-left-4 duration-700">
          
          <!-- Minimalist logo - typography only (Restored monochrome sizes) -->
          <h1 class="flex items-baseline gap-1 select-none mb-10 no-underline group cursor-default">
            <span class="text-[3.5rem] md:text-[4rem] font-bold tracking-tight text-gray-900 leading-none">LEET</span>
            <span class="text-[2.2rem] md:text-[2.6rem] font-normal tracking-tight text-gray-400 leading-none">MATCHER</span>
          </h1>
          
          <!-- Minimal tagline (Restored style) -->
          <p class="text-xl md:text-2xl font-normal text-gray-500 mb-8 uppercase tracking-[0.05em] leading-relaxed">
            Match. Compete. Conquer.
          </p>
          
          <!-- Subtle divider (Restored style) -->
          <div class="w-[60px] h-[2px] bg-gray-300 mb-10"></div>
          
          <!-- Clean description (Restored monochrome style) -->
          <p class="text-[1rem] leading-[1.8] text-gray-500 font-normal max-w-[360px]">
            The elite platform where CS students<br>
            connect with top tech companies through 
            data-driven, skill-based matches.
          </p>
        </div>
      </div>

      <!-- Right side - Clean CTA buttons (Gray-50 background) -->
      <div class="flex-1 flex items-center justify-center p-12 md:p-16 bg-gray-50/80 backdrop-blur-sm">
        <div class="max-w-[360px] w-full animate-in fade-in slide-in-from-right-4 duration-700">
          <h2 class="text-[2rem] font-bold text-gray-900 tracking-tight leading-none mb-2">Get started</h2>
          <p class="text-[1rem] text-gray-400 font-normal mb-12">Access your account or create one</p>

          <div class="flex flex-col gap-5">
            <!-- Login Button - Outlined monochrome -->
            <router-link 
              to="/login" 
              class="flex items-center justify-center h-14 w-full border border-gray-900 rounded-lg text-gray-900 font-bold text-sm tracking-widest uppercase hover:bg-gray-900 hover:text-white transition-all duration-300 active:scale-95"
            >
              LOGIN
            </router-link>

            <!-- Register Button - Filled monochrome -->
            <router-link 
              to="/register" 
              class="flex items-center justify-center h-14 w-full bg-gray-900 rounded-lg text-white font-bold text-sm tracking-widest uppercase hover:bg-gray-800 transition-all duration-300 shadow-xl shadow-gray-200 active:scale-95"
            >
              REGISTER
            </router-link>
          </div>

          <!-- Simple footer note -->
          <p class="mt-16 text-[0.85rem] text-gray-400/80 font-medium text-center md:text-left">
            © 2026 LeetMatcher · All rights reserved
          </p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
// Minimal logic
</script>