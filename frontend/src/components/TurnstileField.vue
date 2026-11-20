<script setup lang="ts">
import { onMounted, onUnmounted, useTemplateRef } from 'vue'

const token = defineModel<string>({ default: '' })

const siteKey = import.meta.env.VITE_TURNSTILE_SITE_KEY || ''
const box = useTemplateRef<HTMLElement>('box')
let widgetId = ''

interface TurnstileApi {
    render: (element: HTMLElement, options: Record<string, unknown>) => string
    reset: (id?: string) => void
    remove: (id: string) => void
}

function turnstile(): TurnstileApi | undefined {
    return (window as Window & { turnstile?: TurnstileApi }).turnstile
}

function loadScript(): Promise<void> {
    if (turnstile()) {
        return Promise.resolve()
    }

    return new Promise((resolve, reject) => {
        const src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit'
        const existing = document.querySelector<HTMLScriptElement>(`script[src="${src}"]`)

        if (existing) {
            existing.addEventListener('load', () => resolve(), { once: true })
            existing.addEventListener('error', () => reject(new Error('Turnstile failed to load.')), { once: true })
            return
        }

        const script = document.createElement('script')
        script.src = src
        script.async = true
        script.onload = () => resolve()
        script.onerror = () => reject(new Error('Turnstile failed to load.'))
        document.head.appendChild(script)
    })
}

function reset() {
    token.value = ''

    if (widgetId !== '') {
        turnstile()?.reset(widgetId)
    }
}

defineExpose({ reset })

onMounted(async () => {
    if (siteKey === '' || !box.value) {
        return
    }

    await loadScript()

    const api = turnstile()

    if (!api || !box.value) {
        return
    }

    widgetId = api.render(box.value, {
        sitekey: siteKey,
        action: 'contact',
        theme: 'dark',
        callback: (value: string) => {
            token.value = value
        },
        'expired-callback': () => {
            token.value = ''
        },
        'error-callback': () => {
            token.value = ''
        },
    })
})

onUnmounted(() => {
    if (widgetId !== '') {
        turnstile()?.remove(widgetId)
    }
})
</script>

<template>
  <div>
    <p v-if="siteKey === ''" class="text-xs uppercase tracking-wide text-linear-red" role="alert">
      The human check is not configured.
    </p>
    <div ref="box" />
  </div>
</template>
