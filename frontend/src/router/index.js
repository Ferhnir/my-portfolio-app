import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'projects',
        component: () => import('@/pages/Cases.vue')
    },
    {
        path: '/resume',
        name: 'resume',
        component: () => import('@/pages/Employment.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router // 💡 Make sure this is an export default statement
