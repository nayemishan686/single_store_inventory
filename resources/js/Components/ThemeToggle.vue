<script setup>
import { useTheme } from "@/composables/useTheme";
const { theme, toggle } = useTheme();
</script>

<template>
  <button
    @click="toggle"
    class="px-3 py-1.5 rounded-lg text-sm border border-slate-300/70 dark:border-white/15 bg-white hover:bg-slate-50 text-slate-700 dark:bg-white/10 dark:hover:bg-white/15 dark:text-slate-200 transition"
    title="Toggle theme"
  >
    <span v-if="theme === 'dark'">🌙 Dark</span>
    <span v-else>☀️ Light</span>
  </button>
</template>
