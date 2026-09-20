<template>
  <header class="h-12 border-b border-linear-border flex items-center justify-between px-6 bg-linear-surface/20 backdrop-blur-sm sticky top-0 z-10">
    <!-- Breadcrumb Indicator -->
    <div class="text-xs font-semibold text-zinc-400 tracking-wider uppercase flex items-center space-x-2">
      <span>workspace</span>
      <span class="text-zinc-600">/</span>
      <span class="text-zinc-200">projects</span>
    </div>

    <!-- External Links / Quick Status Tags Slot -->
    <div class="flex items-center space-x-3 text-zinc-400 text-xs font-medium">
      <a href="https://github.com" target="_blank" class="hover:text-zinc-100 transition-colors">GitHub</a>
      <span class="text-zinc-700">•</span>
      <a href="https://linkedin.com" target="_blank" class="hover:text-zinc-100 transition-colors">LinkedIn</a>
      <span class="px-2 py-0.5 bg-linear-border text-zinc-300 rounded border border-white/5 font-mono text-[10px] ml-2">
        EST. 2026
      </span>
    </div>
  </header>
</template>

<script setup>
</script>
