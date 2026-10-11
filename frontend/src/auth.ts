import { computed, ref } from 'vue'

const TOKEN_KEY = 'sanctum_token'
const token = ref(localStorage.getItem(TOKEN_KEY) || '')

export const isAuthenticated = computed(() => token.value !== '')

export function setToken(value: string) {
    if (value) {
        token.value = value
        localStorage.setItem(TOKEN_KEY, value)
        return
    }

    token.value = ''
    localStorage.removeItem(TOKEN_KEY)
}
