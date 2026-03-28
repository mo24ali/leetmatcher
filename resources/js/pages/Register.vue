<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6 font-sans">
    <div class="max-w-[500px] w-full bg-white rounded-3xl border border-gray-200 p-8 sm:p-12 shadow-sm">
      
      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-2 italic">Join as</h1>
        <p class="text-sm text-gray-500">Pick your path to continue</p>
      </div>
      
      <div class="space-y-4">
        <!-- Applicant link -->
        <router-link 
          to="/register-student" 
          class="group flex items-center justify-between p-6 bg-white border border-gray-200 rounded-2xl
                 hover:border-blue-500 hover:bg-blue-50/20 transition-all duration-300 no-underline shadow hover:shadow-lg active:scale-95"
        >
          <div class="flex-1 min-w-0">
            <span class="block text-[0.85rem] font-bold text-gray-900 group-hover:text-blue-600 mb-1">Are you an applicant?</span>
            <span class="text-xs text-gray-500 group-hover:text-gray-700">Find your next opportunity</span>
          </div>
          <span class="text-xl group-hover:translate-x-1.5 transition-transform duration-300 text-gray-400 group-hover:text-blue-600 pr-2">→</span>
        </router-link>

        <!-- Recruiter link -->
        <router-link 
          to="/register-recruiter" 
          class="group flex items-center justify-between p-6 bg-white border border-gray-200 rounded-2xl
                 hover:border-green-500 hover:bg-green-50/10 transition-all duration-300 no-underline shadow hover:shadow-lg active:scale-95"
        >
          <div class="flex-1 min-w-0">
            <span class="block text-[0.85rem] font-bold text-gray-900 group-hover:text-green-600 mb-1">Are you a recruiter?</span>
            <span class="text-xs text-gray-500 group-hover:text-gray-700">Hire the best talent</span>
          </div>
          <span class="text-xl group-hover:translate-x-1.5 transition-transform duration-300 text-gray-400 group-hover:text-green-600 pr-2">→</span>
        </router-link>
      </div>

      <!-- Subtle separator -->
      <div class="my-10 h-px bg-gray-100 w-full relative">
        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
          or
        </span>
      </div>

      <!-- Footer text -->
      <p class="text-center text-sm text-gray-500">
        Already have an account? 
        <router-link to="/login" class="text-gray-900 font-bold hover:underline">Sign in</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
// Minimal script
</script>