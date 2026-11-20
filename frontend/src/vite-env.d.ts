/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_API_URL?: string
    readonly VITE_TURNSTILE_SITE_KEY?: string
}

declare module 'vue-draggable-plus' {
    import type { DefineComponent } from 'vue'

    export const VueDraggable: DefineComponent
}
