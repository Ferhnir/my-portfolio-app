import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url' // 💡 Modern native URL converter

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        },
    },
    server: {
        port: 3000,
        host: true, // Crucial for Docker container bridging
        allowedHosts: ['portfolio.test'],
    }
})
