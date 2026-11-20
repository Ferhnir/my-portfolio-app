<script setup lang="ts">
withDefaults(defineProps<{
    id: string
    placeholder?: string
    name?: string
    required?: boolean
    type?: 'text' | 'date' | 'url' | 'email'
    multiline?: boolean
    plain?: boolean
    autocomplete?: string
}>(), {
    placeholder: '',
    name: 'name',
    required: false,
    type: 'text',
    multiline: false,
    plain: false,
    autocomplete: undefined,
})

const model = defineModel<string>({ required: true })
</script>

<template>
  <textarea
      v-if="multiline"
      :id="id"
      v-model="model"
      class="w-full bg-linear-field border border-linear-border text-linear-text text-xs font-cyber-display px-3 py-2 rounded-md outline-none focus:border-linear-border-hover"
      rows="3"
      :name="name"
      :placeholder="placeholder"
      :required="required"
  />
  <input
      v-else
      :id="id"
      v-model="model"
      class="w-full bg-linear-field border border-linear-border text-linear-text text-xs font-cyber-display px-3 py-2 rounded-md outline-none focus:border-linear-border-hover"
      :class="plain || type === 'email' ? 'normal-case' : 'uppercase tracking-wide'"
      :type="type"
      :name="name"
      :placeholder="placeholder"
      :required="required"
      :autocomplete="autocomplete"
  >
</template>
