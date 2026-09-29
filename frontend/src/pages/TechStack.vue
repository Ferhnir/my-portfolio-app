<template>
  <div class="p-8 max-w-6xl mx-auto space-y-8 animate-fade-in">

    <!-- View Header Panel -->
    <div class="space-y-2 border-b border-linear-border pb-6">
      <h1 class="flex items-center gap-3 text-[22px] font-bold text-linear-text uppercase tracking-tight font-cyber-display">
        <span class="text-linear-green">//</span>
        ENGINE // VERIFIED TECH STACK
      </h1>
      <p class="text-xs text-linear-blue uppercase tracking-wide font-medium font-cyber-display leading-relaxed">
        Core languages, modern frameworks, and isolated container environments.
      </p>
    </div>

    <!-- ⚡ The Tech Stack Grid -->
    <p v-if="loading" class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display">
      Loading tech stack.
    </p>

    <p v-else-if="errorMessage" class="text-xs text-linear-red uppercase tracking-wide font-cyber-display" role="alert">
      {{ errorMessage }}
    </p>

    <p v-else-if="techGroups.length === 0" class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display">
      No technologies yet.
    </p>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
          v-for="group in techGroups"
          :key="group.id"
          class="border border-linear-border bg-linear-surface rounded-md p-6 space-y-4 hover:border-linear-border-hover transition-colors duration-200 shadow-[0_20px_70px_rgba(0,0,0,0.28)]"
      >
        <h2 class="text-xs font-bold uppercase tracking-widest font-cyber-display flex items-center space-x-2">
          <span class="text-linear-green">{{ String(group.sort_order).padStart(2, '0') }} //</span>
          <span class="text-linear-text">{{ group.name }}</span>
        </h2>

        <div class="space-y-4 pt-2">
          <div v-for="link in group.categories" :key="link.id" class="space-y-2">
            <h3 class="text-[13px] font-bold text-linear-red uppercase tracking-wider font-cyber-display">
              {{ link.category.name }}
            </h3>

            <div class="flex flex-wrap gap-2">
              <span
                  v-for="placement in link.technologies"
                  :key="placement.id"
                  class="px-2.5 py-1 text-[12px] font-medium uppercase tracking-wide font-cyber-display bg-linear-field border border-linear-border text-linear-blue rounded-md hover:text-linear-text hover:border-linear-border-hover transition-colors duration-150"
              >
                {{ placement.technology.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const apiBase = import.meta.env.VITE_API_URL || 'https://api.portfolio.test'

const techGroups = ref([])
const loading = ref(true)
const errorMessage = ref('')

onMounted(async () => {
    try {
        const response = await fetch(`${apiBase}/api/tech-stack`, {
            headers: { Accept: 'application/json' },
        })

        const body = await response.json().catch(() => ({}))

        if (!response.ok) {
            errorMessage.value = body.message || 'Could not load the tech stack.'
            return
        }

        techGroups.value = body.data ?? []
    } catch {
        errorMessage.value = 'Could not load the tech stack.'
    } finally {
        loading.value = false
    }
})
</script>
