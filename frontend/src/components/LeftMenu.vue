<template>
  <aside class="w-64 border-r border-linear-border bg-linear-surface/40 backdrop-blur-sm p-4 flex flex-col justify-between select-none">
    <div>
      <!-- Brand Logo / Workspace Title -->
      <div class="flex items-center space-x-2 px-2 py-1.5 font-semibold text-sm tracking-tight text-white hover:bg-white/5 rounded-md cursor-pointer transition-colors duration-150">
        <span class="w-4 h-4 bg-linear-border border border-white/20 rounded-md shadow-inner"></span>
        <span>Maksymilian Zdunski</span>
      </div>

      <!-- Primary Navigation Links Array -->
      <nav class="mt-6 space-y-1 text-zinc-400 text-sm font-medium">
        <div class="px-3 py-1.5 text-white bg-white/5 rounded-md cursor-pointer font-semibold">
          🚀 Projects Grid
        </div>
        <div class="px-3 py-1.5 hover:text-zinc-200 hover:bg-white/5 rounded-md cursor-pointer transition-colors duration-150">
          📄 My Resume
        </div>
        <div class="px-3 py-1.5 hover:text-zinc-200 hover:bg-white/5 rounded-md cursor-pointer transition-colors duration-150">
          ✉️ Contact Me
        </div>
      </nav>
    </div>

    <!-- Technical Environment Footer Metric Example -->
    <div class="text-[10px] text-zinc-600 font-mono px-3">
      v13.0.0-beta // Laravel + Vue3
    </div>
  </aside>
</template>

<script setup>
// Interactive state actions for the menu links will go here later
</script>
