<template>
  <div class="p-8 max-w-6xl mx-auto space-y-8 animate-fade-in">

    <!-- View Header Panel -->
    <div class="space-y-2 border-b border-linear-border pb-6">
      <h1 class="text-xl font-black text-white uppercase tracking-wider font-cyber-display">
        ENGINE // VERIFIED TECH STACK
      </h1>
      <p class="text-xs text-zinc-500 uppercase tracking-widest font-cyber-display">
        Core languages, modern frameworks, and isolated container environments.
      </p>
    </div>

    <!-- ⚡ The Tech Stack Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Loop through each architectural layer group -->
      <div
          v-for="group in techGroups"
          :key="group.id"
          class="border border-linear-border bg-linear-surface/20 rounded-xl p-6 space-y-4 hover:border-zinc-800 transition-colors duration-200"
      >
        <!-- Group Title (CAPITAL MONO) -->
        <h2 class="text-xs font-black text-accent-bright uppercase tracking-widest font-cyber-display flex items-center space-x-2">
          <span class="text-zinc-600">{{ group.id }} //</span>
          <span class="text-lime-400">{{ group.title }}</span>
        </h2>

        <!-- Sub-items inside the group -->
        <div class="space-y-4 pt-2">
          <div v-for="sub in group.subCategories" :key="sub.name" class="space-y-2">
            <h3 class="text-[14px] font-bold text-red-600 uppercase tracking-wider font-cyber-display">
              {{ sub.name }}
            </h3>

            <!-- Tech Pills Array Layout -->
            <div class="flex flex-wrap gap-2">
              <span
                  v-for="tech in sub.items"
                  :key="tech"
                  class="px-2.5 py-1 text-[14px] font-bold uppercase tracking-wide font-cyber-display bg-linear-surface border border-linear-border text-blue-300 rounded-md shadow-sm hover:text-white hover:border-zinc-700 transition-colors duration-150"
              >
                {{ tech }}
              </span>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

// 💡 Structured dynamic JSON data object containing your tech layers
const techGroups = ref([
  {
    id: '01',
    title: 'Backend Ecosystem',
    subCategories: [
      { name: 'Core Languages', items: ['PHP', 'SQL', 'PYTHON'] },
      { name: 'Frameworks', items: ['Laravel', 'Symfony', 'Lumen', 'Slim 4'] },
      { name: 'Architecture & Packages', items: ['RESTful APIs', 'Fortify', 'Sanctum', 'PHPUnit / PEST', 'BREEZE'] },
      { name: 'CMS', items: ['OCTOBER'] }
    ]
  },
  {
    id: '02',
    title: 'Frontend Engineering',
    subCategories: [
      { name: 'Core Languages', items: ['JavaScript (ES6+)', 'HTML5', 'CSS3'] },
      { name: 'Frameworks & Preprocessors', items: ['Vue 3 (Composition API)', 'React', 'Tailwind CSS', 'SCSS'] },
      { name: 'Build Tools', items: ['Vite', 'NPM', 'PostCSS'] }
    ]
  },
  {
    id: '03',
    title: 'Data Systems & Caching',
    subCategories: [
      { name: 'Relational Databases', items: ['MySQL', 'Postgres', 'MariaDB'] },
      { name: 'In-Memory Engines', items: ['Redis', 'Redis Queues', 'Session Storage'] }
    ]
  },
  {
    id: '04',
    title: 'DevOps & Systems Control',
    subCategories: [
      { name: 'Virtualization & OS', items: ['Docker', 'Docker Compose', 'WSL 2 (Ubuntu)'] },
      { name: 'Web Proxies & Engineering Tools', items: ['Caddy Server', 'Git / GitHub', 'CI/CD Pipelines'] },
      { name: 'AI Coding Efficiency', items: ['Claude Code', 'Cursor IDE', 'Prompt Engineering'] }
    ]
  }
])
</script>
