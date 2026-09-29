import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/Login.vue')
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/ForgotPassword.vue')
    },
    {
        path: '/',
        name: 'cases',
        component: () => import('@/pages/Cases.vue')
    },
    {
        path: '/tech-stack',
        name: 'tech-stack',
        component: () => import('@/pages/TechStack.vue')
    },
    {
        path: '/employment',
        name: 'employment',
        component: () => import('@/pages/Employment.vue')
    },
    {
        path: '/education',
        name: 'education',
        component: () => import('@/pages/Employment.vue')
    },
    {
        path: '/api-docs',
        name: 'api-docs',
        component: () => import('@/pages/Employment.vue')
    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('@/pages/Employment.vue')
    },
    {
        path: '/completed-tasks',
        name: 'completed-tasks',
        component: () => import('@/pages/Employment.vue')
    },
    {
        path: '/job-applications',
        name: 'job-applications',
        component: () => import('@/pages/Employment.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router // 💡 Make sure this is an export default statement
