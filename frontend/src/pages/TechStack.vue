<template>
  <div class="p-8 max-w-7xl mx-auto space-y-8 animate-fade-in">

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

    <p v-if="loading" class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display">
      Loading tech stack.
    </p>

    <p v-else-if="errorMessage" class="text-xs text-linear-red uppercase tracking-wide font-cyber-display" role="alert">
      {{ errorMessage }}
    </p>

    <div v-else class="flex flex-col lg:flex-row lg:items-start gap-6">
      <div class="min-w-0 flex-1">
        <p v-if="techGroups.length === 0 && !isAuthenticated" class="text-xs text-linear-secondary uppercase tracking-wide font-cyber-display">
          No technologies yet.
        </p>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <TechStackGroupCard
              v-for="group in techGroups"
              :key="group.id"
              :group="group"
              :authenticated="isAuthenticated"
              :deleting="deletingGroupId === group.id"
              @delete="deleteGroup(group)"
              @category-drop="onCategoryDrop"
              @technology-drop="onTechnologyDrop"
              @drag-move="onDragHover"
          />

          <form
              v-if="isAuthenticated"
              class="border border-linear-border bg-linear-surface rounded-md p-6 min-h-56 flex flex-col items-center justify-center gap-4 shadow-[0_20px_70px_rgba(0,0,0,0.28)]"
              @submit.prevent="createGroup"
          >
            <label class="w-full space-y-2" for="new-group-name">
              <span class="text-[11px] font-bold uppercase tracking-widest text-linear-red font-cyber-display">Name</span>
              <BaseInput
                  id="new-group-name"
                  v-model="newGroupName"
                  placeholder="Group name"
                  required
              />
            </label>
            <BaseButton type="submit" :disabled="creatingGroup">
              Add new group
            </BaseButton>
          </form>
        </div>
      </div>

      <div v-if="isAuthenticated" class="w-full lg:w-72 shrink-0 space-y-4">
      <UnassignedStackPanel
          v-model:items="unassignedCategories"
          v-model:unassign-items="unassignDrop"
          v-model:delete-items="deleteDrop"
          title="Categories"
          create-url="/tech-stack-categories"
          placeholder="Category name"
          submit-label="Create new category"
          loading-label="Loading categories."
          empty-label="No unassigned categories."
          drag-group="tech-stack-categories"
          handle-class="category-handle"
          tone="red"
          source-list="unassigned"
          unassign-list="unassign"
          delete-list="delete"
          :loading="unassignedLoading"
          :label="(item) => item.category.name"
          :unassign-class="actionDropClass('unassign')"
          :delete-class="actionDropClass('delete')"
          @created="unassignedCategories.push(asUnassigned($event))"
          @end="onCategoryDrop"
          @move="onDragHover"
          @action="onActionDrop"
      />

      <UnassignedStackPanel
          v-model:items="unassignedTechnologies"
          v-model:unassign-items="technologyUnassignDrop"
          v-model:delete-items="technologyDeleteDrop"
          title="Technologies"
          create-url="/technologies"
          placeholder="Technology name"
          submit-label="Create new technology"
          loading-label="Loading technologies."
          empty-label="No unassigned technologies."
          drag-group="tech-stack-technologies"
          handle-class="technology-handle"
          tone="blue"
          source-list="unassigned-technologies"
          unassign-list="technology-unassign"
          delete-list="technology-delete"
          :loading="unassignedTechnologiesLoading"
          :label="(item) => item.technology.name"
          :unassign-class="actionDropClass('technology-unassign')"
          :delete-class="actionDropClass('technology-delete')"
          @created="unassignedTechnologies.push(asUnassignedTechnology($event))"
          @end="onTechnologyDrop"
          @move="onDragHover"
          @action="onTechnologyActionDrop"
      />

      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import TechStackGroupCard from '@/components/TechStackGroupCard.vue'
import UnassignedStackPanel from '@/components/UnassignedStackPanel.vue'
import { useCategoryStack } from '@/composables/useCategoryStack'
import { useTechnologyStack } from '@/composables/useTechnologyStack'
import { isAuthenticated } from '@/auth'
import { api } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { StackDragEvent, TechStackGroup } from '@/types/techStack'

const techGroups = ref<TechStackGroup[]>([])
const loading = ref(true)
const errorMessage = ref('')
const newGroupName = ref('')
const creatingGroup = ref(false)
const deletingGroupId = ref<number | null>(null)
const hoveredAction = ref('')

const {
    unassignedCategories,
    unassignedLoading,
    unassignDrop,
    deleteDrop,
    asUnassigned,
    loadUnassigned,
    onActionDrop,
    onCategoryDrop: dropCategory,
} = useCategoryStack(techGroups)

const {
    unassignedTechnologies,
    unassignedTechnologiesLoading,
    technologyUnassignDrop,
    technologyDeleteDrop,
    asUnassignedTechnology,
    loadUnassignedTechnologies,
    onTechnologyDrop: dropTechnology,
    onTechnologyActionDrop,
} = useTechnologyStack(techGroups)

const actionDropLists = new Set(['unassign', 'delete', 'technology-unassign', 'technology-delete'])

function onDragHover(event: StackDragEvent) {
    const list = event.to?.dataset?.list ?? ''
    hoveredAction.value = actionDropLists.has(list) ? list : ''
}

function actionDropClass(list: string) {
    if (hoveredAction.value !== list) {
        return ''
    }

    if (list === 'delete' || list === 'technology-delete') {
        return 'action-drop-hot action-drop-hot-delete'
    }

    return 'action-drop-hot action-drop-hot-unassign'
}

function onCategoryDrop(event: StackDragEvent) {
    hoveredAction.value = ''
    return dropCategory(event)
}

function onTechnologyDrop(event: StackDragEvent) {
    hoveredAction.value = ''
    return dropTechnology(event)
}

async function createGroup() {
    const name = newGroupName.value.trim()

    if (!name || creatingGroup.value) {
        return
    }

    const sortOrder = techGroups.value.reduce(
        (max, group) => Math.max(max, Number(group.sort_order) || 0),
        0,
    )

    creatingGroup.value = true

    try {
        const body = await api<ApiResponse<TechStackGroup>>('/tech-stack-groups', {
            method: 'POST',
            body: {
                name,
                sort_order: sortOrder,
            },
        })

        techGroups.value.push({
            ...body.data,
            categories: body.data?.categories ?? [],
        })
        newGroupName.value = ''
    } finally {
        creatingGroup.value = false
    }
}

async function deleteGroup(group: TechStackGroup) {
    if (deletingGroupId.value) {
        return
    }

    deletingGroupId.value = group.id

    try {
        await api(`/tech-stack-groups/${group.id}`, { method: 'DELETE' })
        techGroups.value = techGroups.value.filter((item) => item.id !== group.id)
    } finally {
        deletingGroupId.value = null
    }
}

onMounted(async () => {
    try {
        const body = await api<ApiResponse<TechStackGroup[]>>('/tech-stack', { auth: false, toast: false })
        techGroups.value = body.data ?? []
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Could not load the tech stack.'
    } finally {
        loading.value = false
    }

    if (isAuthenticated.value) {
        await Promise.all([loadUnassigned(), loadUnassignedTechnologies()])
    }
})

watch(isAuthenticated, (signedIn) => {
    if (signedIn) {
        loadUnassigned()
        loadUnassignedTechnologies()
        return
    }

    unassignedCategories.value = []
    unassignedTechnologies.value = []
})
</script>
