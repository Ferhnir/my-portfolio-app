<template>
  <VueDatePicker
      v-model="date"
      class="education-datepicker"
      model-type="yyyy-MM-dd"
      :formats="{ input: 'dd MMM yyyy' }"
      :time-config="{ enableTimePicker: false }"
      :input-attrs="{ id, name, clearable: true, autocomplete: 'off' }"
      :ui="{ menu: 'education-datepicker', input: 'education-datepicker-input' }"
      :placeholder="placeholder"
      auto-apply
      dark
      teleport
      week-start="1"
      :locale="enGB"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { VueDatePicker } from '@vuepic/vue-datepicker'
import { enGB } from 'date-fns/locale'
import '@vuepic/vue-datepicker/dist/main.css'

const model = defineModel<string>({ required: true })

withDefaults(defineProps<{
    id: string
    name?: string
    placeholder?: string
}>(), {
    name: 'date',
    placeholder: 'Select a date',
})

const date = computed<string | null>({
    get: () => model.value || null,
    set: (value) => {
        model.value = value ?? ''
    },
})
</script>

<style>
.education-datepicker {
    --dp-font-family: "JetBrains Mono", ui-monospace, monospace;
    --dp-font-size: 12px;
    --dp-preview-font-size: 12px;
    --dp-background-color: #09141c;
    --dp-text-color: #e3e8ed;
    --dp-hover-color: #0d2431;
    --dp-hover-text-color: #e3e8ed;
    --dp-hover-icon-color: #78b9df;
    --dp-primary-color: #8cff00;
    --dp-primary-disabled-color: #31536a;
    --dp-primary-text-color: #080d11;
    --dp-secondary-color: #6e94ad;
    --dp-border-color: #193344;
    --dp-menu-border-color: #193344;
    --dp-border-color-hover: #31536a;
    --dp-border-color-focus: #31536a;
    --dp-icon-color: #78b9df;
    --dp-border-radius: 6px;
    --dp-cell-border-radius: 6px;
    --dp-input-padding: 8px 12px 8px 36px;
}

.education-datepicker-input {
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
</style>
