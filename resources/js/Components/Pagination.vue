<script setup>
const props = defineProps({
  meta: { type: Object, required: true }, // expects Laravel pagination meta
});
const emit = defineEmits(["go"]);
const toPage = (p) => {
  if (p && p !== props.meta.current_page) emit("go", p);
};
</script>

<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center gap-2 mt-4">
    <button
      class="px-3 py-1 rounded border"
      :disabled="meta.current_page === 1"
      @click="toPage(meta.current_page - 1)"
    >
      Prev
    </button>
    <span class="text-sm">Page {{ meta.current_page }} / {{ meta.last_page }}</span>
    <button
      class="px-3 py-1 rounded border"
      :disabled="meta.current_page === meta.last_page"
      @click="toPage(meta.current_page + 1)"
    >
      Next
    </button>
  </div>
</template>
