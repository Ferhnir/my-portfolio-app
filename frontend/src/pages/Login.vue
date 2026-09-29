<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { setToken } from '@/auth'
import { api } from '@/api/client'
import '@/assets/styles/auth-card.css'

const router = useRouter()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')
const submitting = ref(false)

function togglePassword() {
    showPassword.value = !showPassword.value
}

async function submit() {
    errorMessage.value = ''
    submitting.value = true

    try {
        const body = await api('/login', {
            method: 'POST',
            auth: false,
            toast: false,
            body: {
                email: email.value,
                password: password.value,
            },
        })

        setToken(body.data?.sanctum_token || body.sanctum_token || '')

        await router.push({ name: 'cases' })
    } catch (error) {
        errorMessage.value = error.message || 'Login failed. Please try again later.'
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <main class="auth-screen">
        <section class="reset-container">
            <div class="reset-card">
                <h1 class="heading">SIGN IN</h1>

                <p class="description">
                    Enter your email and password below to access your account.
                </p>

                <div class="divider"></div>

                <form @submit.prevent="submit">
                    <div class="form-group">
                        <label class="form-label" for="email">Email *</label>
                        <p class="form-helper">USE THE EMAIL ADDRESS ASSOCIATED WITH YOUR ACCOUNT.</p>
                        <div class="input-wrapper">
                            <span class="input-icon input-icon-start" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path d="m3 7 9 6 9-6" />
                                </svg>
                            </span>
                            <input
                                id="email"
                                v-model="email"
                                class="form-input"
                                type="email"
                                name="email"
                                placeholder="Enter your email"
                                autocomplete="email"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password *</label>
                        <p class="form-helper">ENTER THE PASSWORD FOR YOUR ACCOUNT.</p>
                        <div class="input-wrapper">
                            <span class="input-icon input-icon-start" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="10" width="14" height="11" rx="2" />
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                                </svg>
                            </span>
                            <input
                                id="password"
                                v-model="password"
                                class="form-input"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                class="toggle-password"
                                type="button"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                @click="togglePassword"
                            >
                                <svg v-if="showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 3 18 18" />
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                    <path d="M9.9 4.2A10.7 10.7 0 0 1 12 4c6.5 0 10 8 10 8a18 18 0 0 1-3.2 4.3" />
                                    <path d="M6.6 6.6C3.7 8.8 2 12 2 12s3.5 8 10 8a10.7 10.7 0 0 0 4.1-.8" />
                                </svg>
                                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="forgot-row">
                        <RouterLink class="forgot-link" :to="{ name: 'forgot-password' }">
                            [ Forgot password ]
                            <span class="arrow">→</span>
                        </RouterLink>
                    </div>

                    <button class="submit-button" type="submit" :disabled="submitting">
                        <span class="button-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="10" width="14" height="11" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                        </span>
                        SIGN IN
                        <span class="button-arrow">→</span>
                    </button>
                </form>

                <div v-if="errorMessage" class="error-alert" role="alert">
                    <span class="error-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m10.3 3.6-8 14A2 2 0 0 0 4 20.5h16a2 2 0 0 0 1.7-2.9l-8-14a2 2 0 0 0-3.4 0Z" />
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                        </svg>
                    </span>
                    <span>{{ errorMessage }}</span>
                </div>

                <div class="bottom-divider"></div>

                <RouterLink class="back-link" :to="{ name: 'cases' }">
                    <span class="arrow">←</span>
                    [ BACK TO PORTFOLIO ]
                </RouterLink>
            </div>
        </section>
    </main>
</template>
