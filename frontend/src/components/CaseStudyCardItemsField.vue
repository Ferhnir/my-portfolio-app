<script setup lang="ts">
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import type { CaseStudyCardItemDraft } from '@/types/caseStudy'

const items = defineModel<CaseStudyCardItemDraft[]>({ required: true })

function addItem() {
    items.value.push({ id: null, body: '' })
}

function removeItem(index: number) {
    items.value.splice(index, 1)
}
</script>

<template>
  <div class="space-y-3">
    <p class="text-[11px] font-bold uppercase tracking-widest text-linear-secondary">List</p>
    <div v-for="(item, index) in items" :key="item.id ?? `new-${index}`" class="flex items-center gap-2">
      <BaseInput
          :id="`card-list-item-${index}`"
          v-model="item.body"
          class="min-w-0 flex-1"
          :name="`item-${index}`"
          plain
          placeholder="List item"
      />
      <button
          type="button"
          class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md border border-linear-border text-linear-secondary transition-colors hover:border-linear-red hover:text-linear-red"
          aria-label="Remove list item"
          @click="removeItem(index)"
      >
        <FontAwesomeIcon :icon="['fas', 'trash-can']" class="text-sm" />
      </button>
    </div>
    <BaseButton type="button" @click="addItem">Add list item</BaseButton>
  </div>
</template>
