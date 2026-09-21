<template>
  <aside class="w-64 border-r border-linear-border bg-linear-surface/30 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-zinc-800/25 via-linear-surface/20 to-linear-base/40 backdrop-blur-md p-4 flex flex-col justify-between select-none h-screen sticky top-0">
    <div class="space-y-6">

      <div class="flex items-center space-x-2 px-2 py-1.5 font-cyber-display text-xs font-black tracking-wider text-white uppercase hover:bg-white/5 rounded-md cursor-pointer transition-colors duration-150">
        <span>M. ZDUNSKI // DEV</span>
      </div>

      <div v-for="group in menuGroups" :key="group.sectionTitle" class="space-y-1.5">

        <div class="text-[14px] font-black tracking-widest text-zinc-600 font-cyber-display uppercase px-3 flex items-center justify-between cursor-default">
          <span>{{ group.sectionTitle }}</span>
          <span class="text-[8px] opacity-40">▼</span>
        </div>

        <nav class="space-y-0.5">
          <RouterLink
              v-for="item in group.items"
              :key="item.routeName"
              :to="{ name: item.routeName }"
              class="flex items-center space-x-3 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider font-cyber-display transition-colors duration-150 group focus:outline-none focus:ring-0 select-none"
              active-class="transition-none text-white bg-white/5 border border-white/5 shadow-inner"
              inactive-class="text-zinc-400 hover:text-zinc-200 hover:bg-white/5"
          >

            <svg
                xmlns="http://w3.org"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                class="w-4 h-4 text-zinc-500 transition-colors duration-150 group-hover:text-zinc-200 group-[.router-link-active]:text-white"
            >
              <path stroke-linecap="round" stroke-linejoin="round" :d="item.svgPath" />
            </svg>

            <span>{{ item.title }}</span>
          </RouterLink>
        </nav>
      </div>
    </div>

    <!-- Technical System Footer Matrix Metadata Details -->
    <div class="text-[9px] text-zinc-600 font-mono px-3 tracking-widest font-cyber-display uppercase">
      SYS // L13.V6.T4
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

// 💡 Split JSON layout grouping navigation array with raw monochrome SVG path assets
const menuGroups = ref([
  {
    sectionTitle: 'Workspace',
    items: [
      {
        title: 'Cases & Projects',
        routeName: 'cases',
        svgPath: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
      },
      {
        title: 'My Tech Stack',
        routeName: 'tech-stack',
        svgPath: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'
      }
    ]
  },
  {
    sectionTitle: 'Personal Info',
    items: [
      {
        title: 'Employment',
        routeName: 'employment',
        svgPath: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
      },
      {
        title: 'Education',
        routeName: 'education',
        svgPath: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222'
      }
    ]
  },
  {
    sectionTitle: 'Systems',
    items: [
      {
        title: 'API Playground',
        routeName: 'api-docs',
        svgPath: 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
      },
      {
        title: 'Contact Inbox',
        routeName: 'contact',
        svgPath: 'M3 8l7.89 5.26a2 2 0 002.22 0L22 8m-2 11H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2z'
      }
    ]
  },
  {
    sectionTitle: 'Planner (Encrypted)',
    items: [
      {
        title: 'Completed Tasks',
        routeName: 'completed-tasks',
        svgPath: 'M3 8l7.89 5.26a2 2 0 002.22 0L22 8m-2 11H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2z'
      },
      {
        title: 'Job Applications',
        routeName: 'job-applications',
        svgPath: 'M3 8l7.89 5.26a2 2 0 002.22 0L22 8m-2 11H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2z'
      }
    ]
  }
])
</script>
