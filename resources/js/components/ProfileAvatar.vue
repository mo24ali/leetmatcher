<template>
  <div 
    class="relative flex items-center justify-center overflow-hidden shrink-0 select-none bg-slate-200 text-white font-bold uppercase transition-all duration-300"
    :class="[
      sizeClasses,
      shape === 'circle' ? 'rounded-full' : 'rounded-2xl',
      border ? 'border-2 border-white/20' : ''
    ]"
    :style="!url || hasError ? placeholderStyle : {}"
  >
    <img 
      v-if="url && !hasError" 
      :src="url" 
      :alt="name || 'Profile picture'"
      class="w-full h-full object-cover"
      @error="handleError"
    />
    <span v-else class="tracking-tighter">
      {{ initials || '?' }}
    </span>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
  url: {
    type: String,
    default: null
  },
  name: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md' // xs, sm, md, lg, xl, xxl
  },
  shape: {
    type: String,
    default: 'circle' // circle, rounded
  },
  border: {
    type: Boolean,
    default: false
  }
});

const hasError = ref(false);

// Reset error when URL changes (essential for preview updates)
watch(() => props.url, (newVal) => {
  if (newVal) hasError.value = false;
});

const initials = computed(() => {
  if (!props.name) return '';
  const parts = props.name.trim().split(/\s+/);
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

const sizeClasses = computed(() => {
  const mapping = {
    'xs': 'w-6 h-6 text-[0.6rem]',
    'sm': 'w-8 h-8 text-[0.7rem]',
    'md': 'w-12 h-12 text-sm',
    'lg': 'w-16 h-16 text-base',
    'xl': 'w-24 h-24 text-xl',
    'xxl': 'w-32 h-32 text-2xl',
  };
  return mapping[props.size] || mapping.md;
});

const placeholderStyle = computed(() => {
  if (!props.name) return { backgroundColor: '#cbd5e1' }; // slate-300
  
  // Consistent color generation based on name
  const palettes = [
    'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', // indigo
    'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', // violet
    'linear-gradient(135deg, #ec4899 0%, #db2777 100%)', // pink
    'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)', // rose
    'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // amber
    'linear-gradient(135deg, #10b981 0%, #059669 100%)', // emerald
    'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)', // cyan
  ];
  
  let hash = 0;
  for (let i = 0; i < props.name.length; i++) {
    hash = props.name.charCodeAt(i) + ((hash << 5) - hash);
  }
  
  const bg = palettes[Math.abs(hash) % palettes.length];
  return { background: bg };
});

const handleError = (e) => {
  console.warn('Avatar failed to load:', props.url);
  hasError.value = true;
};
</script>

<style scoped>
/* Pure tailwind component */
</style>
