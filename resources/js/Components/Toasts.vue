<script setup>
import { useToast } from "@/composables/useToast";
const { toasts, remove } = useToast();
</script>

<template>
  <div class="fixed top-4 right-4 space-y-2 z-50">
    <div
      v-for="t in toasts"
      :key="t.id"
      class="px-4 py-2 rounded shadow text-white"
      :class="t.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'"
    >
      <div class="flex items-center gap-3">
        <span>{{ t.text }}</span>
        <button class="text-white/80" @click="remove(t.id)">✕</button>
      </div>
    </div>
  </div>
</template>
