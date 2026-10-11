/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_API_URL?: string
}

declare module 'vue-draggable-plus' {
    import type { DefineComponent } from 'vue'

    export const VueDraggable: DefineComponent
}
