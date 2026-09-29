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

        <VueDraggable
            v-model="group.categories"
            :animation="150"
            group="tech-stack-categories"
            handle=".category-handle"
            :data-group-id="group.id"
            class="min-h-10 space-y-4 pt-2"
            @end="onCategoryDrop"
        >
          <div v-for="link in group.categories" :key="link.id" class="space-y-2">
            <h3 class="category-handle cursor-grab text-[13px] font-bold text-linear-red uppercase tracking-wider font-cyber-display active:cursor-grabbing">
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
        </VueDraggable>
      </div>
    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import { api } from '@/api/client'

const techGroups = ref([])
const loading = ref(true)
const errorMessage = ref('')

function renumber(categories) {
    categories.forEach((item, index) => {
        item.sort_order = index + 1
    })
}

async function onCategoryDrop(event) {
    const fromId = Number(event.from?.dataset?.groupId)
    const toId = Number(event.to?.dataset?.groupId)
    const { oldIndex, newIndex } = event

    if (oldIndex == null || newIndex == null) {
        return
    }

    if (fromId === toId && oldIndex === newIndex) {
        return
    }

    const source = techGroups.value.find((group) => group.id === fromId)
    const destination = techGroups.value.find((group) => group.id === toId)
    const link = destination?.categories?.[newIndex]

    if (!source || !destination || !link) {
        return
    }

    const body = { sort_order: newIndex + 1 }

    if (fromId !== toId) {
        body.group_id = toId
    }

    try {
        await api(`/tech-stack-group-category/${link.id}`, {
            method: 'PATCH',
            body,
        })

        renumber(source.categories)
        if (source !== destination) {
            renumber(destination.categories)
        }
    } catch {
        const [moved] = destination.categories.splice(newIndex, 1)
        source.categories.splice(oldIndex, 0, moved)
    }
}

onMounted(async () => {
    try {
        const body = await api('/tech-stack', { auth: false, toast: false })
        techGroups.value = body.data ?? []
    } catch (error) {
        errorMessage.value = error.message || 'Could not load the tech stack.'
    } finally {
        loading.value = false
    }
})
</script>
