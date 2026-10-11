<script setup lang="ts">
import { dismissToast, toasts } from '@/toast'
</script>

<template>
    <div class="pointer-events-none fixed top-4 right-4 z-50 flex w-[min(100%-2rem,22rem)] flex-col gap-3">
        <article
            v-for="toast in toasts"
            :key="toast.id"
            class="toast pointer-events-auto border border-linear-red bg-linear-surface px-4 py-3 shadow-[0_20px_70px_rgba(0,0,0,0.28)]"
            role="alert"
        >
            <div class="flex items-start justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-widest text-linear-red font-cyber-display">
                    // {{ toast.title }}
                </p>
                <button
                    type="button"
                    class="text-linear-muted hover:text-linear-text text-xs leading-none"
                    aria-label="Dismiss"
                    @click="dismissToast(toast.id)"
                >
                    ×
                </button>
            </div>
            <p class="mt-2 whitespace-pre-line text-[12px] uppercase tracking-wide text-linear-text font-cyber-display">
                {{ toast.message }}
            </p>
        </article>
    </div>
</template>

<style scoped>
.toast {
    animation: toast-in 160ms ease-out;
}

@keyframes toast-in {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
