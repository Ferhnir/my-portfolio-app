<template>
  <aside
      class="border border-linear-border bg-linear-surface rounded-md p-6 space-y-4 shadow-[0_20px_70px_rgba(0,0,0,0.28)]"
  >
    <h2 class="text-xs font-bold uppercase tracking-widest font-cyber-display flex items-center space-x-2">
      <span class="text-linear-green">//</span>
      <span class="text-linear-text">{{ title }}</span>
    </h2>

    <hr class="border-0 h-px bg-linear-border">

    <p class="text-[10px] font-bold uppercase tracking-widest font-cyber-display flex items-center space-x-1.5">
      <span class="text-linear-green">//</span>
      <span class="text-linear-secondary">Unassigned</span>
    </p>

    <p v-if="loading" class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display">
      {{ loadingLabel }}
    </p>

    <VueDraggable
        v-else
        v-model="items"
        :animation="150"
        :group="{ name: dragGroup, pull: true, put: false }"
        :handle="`.${handleClass}`"
        :data-list="sourceList"
        class="min-h-10 flex flex-wrap items-start gap-2"
        @end="emit('end', $event)"
        @move="emit('move', $event)"
    >
      <BaseBadge
          v-for="item in items"
          :key="item.id"
          kind="source"
          :tone="tone"
          :handle-class="handleClass"
      >
        {{ label(item) }}
      </BaseBadge>
    </VueDraggable>

    <p
        v-if="!loading && items.length === 0"
        class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display"
    >
      {{ emptyLabel }}
    </p>

    <hr class="border-0 h-px bg-linear-border">

    <form class="flex flex-col gap-4" @submit.prevent="submit">
      <label class="w-full" :for="inputId">
        <BaseInput
            :id="inputId"
            v-model="name"
            :placeholder="placeholder"
            required
        />
      </label>
      <BaseButton type="submit" :disabled="creating">
        {{ submitLabel }}
      </BaseButton>
    </form>

    <hr class="border-0 h-px bg-linear-border">

    <div class="grid grid-cols-2 gap-2">
      <div
          class="action-drop relative overflow-hidden border border-linear-border bg-linear-field rounded-md"
          :class="unassignClass"
      >
        <p class="pointer-events-none absolute inset-0 z-10 flex flex-col items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-widest text-linear-green font-cyber-display">
          <FontAwesomeIcon :icon="['fas', 'link-slash']" class="text-sm" />
          Unassign
        </p>
        <VueDraggable
            v-model="unassignItems"
            :group="{ name: dragGroup, pull: false, put: [dragGroup] }"
            :data-list="unassignList"
            class="min-h-20"
            @add="emit('action', 'unassign', $event)"
        >
          <div v-for="item in unassignItems" :key="item.id" class="hidden">
            {{ label(item) }}
          </div>
        </VueDraggable>
      </div>

      <div
          class="action-drop relative overflow-hidden border border-linear-red bg-linear-field rounded-md"
          :class="deleteClass"
      >
        <p class="pointer-events-none absolute inset-0 z-10 flex flex-col items-center justify-center gap-1 text-[10px] font-bold uppercase tracking-widest text-linear-red font-cyber-display">
          <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
          Delete
        </p>
        <VueDraggable
            v-model="deleteItems"
            :group="{ name: dragGroup, pull: false, put: [dragGroup] }"
            :data-list="deleteList"
            class="min-h-20"
            @add="emit('action', 'delete', $event)"
        >
          <div v-for="item in deleteItems" :key="item.id" class="hidden">
            {{ label(item) }}
          </div>
        </VueDraggable>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts" generic="T extends { id: string | number }">
import { ref } from 'vue'
import { VueDraggable } from 'vue-draggable-plus'
import BaseBadge from '@/components/BaseBadge.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import { api } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { NamedRecord, StackDragEvent } from '@/types/techStack'

const props = withDefaults(defineProps<{
    title: string
    createUrl: string
    placeholder: string
    submitLabel: string
    loadingLabel: string
    emptyLabel: string
    dragGroup: string
    handleClass: string
    tone?: 'red' | 'blue'
    sourceList: string
    unassignList: string
    deleteList: string
    loading?: boolean
    label: (item: T) => string
    unassignClass?: string
    deleteClass?: string
}>(), {
    tone: 'blue',
    loading: false,
    unassignClass: '',
    deleteClass: '',
})

const items = defineModel<T[]>('items', { required: true })
const unassignItems = defineModel<T[]>('unassignItems', { required: true })
const deleteItems = defineModel<T[]>('deleteItems', { required: true })

const emit = defineEmits<{
    created: [record: NamedRecord]
    end: [event: StackDragEvent]
    move: [event: StackDragEvent]
    action: [action: 'unassign' | 'delete', event: StackDragEvent]
}>()

const inputId = `new-${props.title.toLowerCase()}-name`
const name = ref('')
const creating = ref(false)

async function submit() {
    const value = name.value.trim()

    if (!value || creating.value) {
        return
    }

    creating.value = true

    try {
        const body = await api<ApiResponse<NamedRecord>>(props.createUrl, {
            method: 'POST',
            body: { name: value },
        })

        name.value = ''
        emit('created', body.data)
    } finally {
        creating.value = false
    }
}
</script>

<style scoped>
.action-drop :deep(.sortable-ghost),
.action-drop :deep(.sortable-chosen),
.action-drop :deep(.sortable-drag),
.action-drop :deep(.sortable-fallback) {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    border: 0 !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.action-drop-hot-unassign {
    border-color: #8cff00;
    background-color: rgba(140, 255, 0, 0.08);
    box-shadow: 0 0 22px rgba(140, 255, 0, 0.35);
    animation: action-drop-pulse 0.8s ease-in-out infinite;
}

.action-drop-hot-delete {
    background-color: rgba(255, 51, 76, 0.12);
    box-shadow: 0 0 22px rgba(255, 51, 76, 0.4);
    animation: action-drop-shake 0.4s ease-in-out infinite;
}

@keyframes action-drop-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.045); }
}

@keyframes action-drop-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px) scale(1.03); }
    75% { transform: translateX(3px) scale(1.03); }
}
</style>
