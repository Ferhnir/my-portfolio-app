<script setup lang="ts">
import { VueDraggable } from 'vue-draggable-plus'
import BaseBadge from '@/components/BaseBadge.vue'
import BaseButton from '@/components/BaseButton.vue'
import type { StackDragEvent, TechStackGroup } from '@/types/techStack'

defineProps<{
    group: TechStackGroup
    authenticated: boolean
    deleting: boolean
}>()

const emit = defineEmits<{
    delete: []
    categoryDrop: [event: StackDragEvent]
    technologyDrop: [event: StackDragEvent]
    dragMove: [event: StackDragEvent]
}>()
</script>

<template>
  <div class="border border-linear-border bg-linear-surface rounded-md p-6 space-y-4 hover:border-linear-border-hover transition-colors duration-200 shadow-[0_20px_70px_rgba(0,0,0,0.28)] flex flex-col">
    <h2 class="text-xs font-bold uppercase tracking-widest font-cyber-display flex items-center space-x-2">
      <span class="text-linear-green">{{ String(group.sort_order).padStart(2, '0') }} //</span>
      <span class="text-linear-text">{{ group.name }}</span>
    </h2>

    <VueDraggable
        v-model="group.categories"
        :animation="150"
        :disabled="!authenticated"
        group="tech-stack-categories"
        handle=".category-handle"
        data-list="group"
        :data-group-id="group.id"
        class="min-h-10 flex-1 space-y-4 pt-2"
        @end="emit('categoryDrop', $event)"
        @move="emit('dragMove', $event)"
    >
      <div v-for="link in group.categories" :key="link.id" class="space-y-2">
        <h3
            class="text-[13px] font-bold text-linear-red uppercase tracking-wider font-cyber-display"
            :class="authenticated ? 'category-handle cursor-grab active:cursor-grabbing' : 'cursor-default'"
        >
          {{ link.category.name }}
        </h3>

        <VueDraggable
            v-if="authenticated"
            v-model="link.technologies"
            :animation="150"
            :sort="false"
            :group="{ name: 'tech-stack-technologies', pull: true, put: ['tech-stack-technologies'] }"
            draggable=".technology-handle"
            handle=".technology-handle"
            data-list="technologies"
            :data-link-id="link.id"
            class="flex flex-wrap items-start gap-2 min-h-8"
            @end="emit('technologyDrop', $event)"
            @move="emit('dragMove', $event)"
        >
          <BaseBadge
              v-for="placement in link.technologies"
              :key="placement.id"
              kind="handle"
              handle-class="technology-handle"
          >
            {{ placement.technology.name }}
          </BaseBadge>
        </VueDraggable>

        <div v-else class="flex flex-wrap gap-2">
          <BaseBadge
              v-for="placement in link.technologies"
              :key="placement.id"
          >
            {{ placement.technology.name }}
          </BaseBadge>
        </div>
      </div>
    </VueDraggable>

    <BaseButton
        v-if="authenticated && (group.categories?.length ?? 0) === 0"
        variant="danger"
        class="mt-auto"
        :disabled="deleting"
        @click="emit('delete')"
    >
      <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
      Delete
    </BaseButton>
  </div>
</template>
