<script setup lang="ts">
const emit = defineEmits<{
    add: []
}>()
</script>

<template>
  <button
      type="button"
      class="group flex h-full w-full flex-col rounded-md border border-linear-border bg-linear-surface text-left shadow-[0_20px_70px_rgba(0,0,0,0.28)] transition-[border-color,box-shadow] duration-200 hover:border-linear-green hover:shadow-[0_0_16px_rgba(140,255,0,0.55),0_0_48px_rgba(140,255,0,0.35)]"
      @click="emit('add')"
  >
    <div class="flex aspect-[16/9] items-center justify-center rounded-t-md border-b border-linear-border bg-linear-base" aria-hidden="true">
      <span class="text-sm font-bold text-linear-muted transition-colors group-hover:text-linear-green">//</span>
    </div>
    <div class="space-y-3 p-6">
      <p class="invisible text-[11px] font-bold uppercase tracking-widest" aria-hidden="true">Client</p>
      <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-linear-text">
        <span class="text-linear-green">//</span>
        Add new
      </h2>
      <p class="invisible text-xs font-medium uppercase tracking-wide" aria-hidden="true">Tagline</p>
    </div>
  </button>
</template>
