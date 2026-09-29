<script setup>
import { ref } from 'vue'
import { api } from '@/api/client'
import '@/assets/styles/auth-card.css'

const email = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const submitting = ref(false)

async function submit() {
    errorMessage.value = ''
    successMessage.value = ''
    submitting.value = true

    try {
        const body = await api('/forgot-password', {
            method: 'POST',
            auth: false,
            toast: false,
            body: {
                email: email.value,
            },
        })

        successMessage.value = body.message || 'Password reset link sent to your email'
    } catch (error) {
        errorMessage.value = error.message || 'Password reset request failed. Please try again later.'
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <main class="auth-screen">
        <section class="reset-container">
            <div class="reset-card">
                <h1 class="heading">FORGOT PASSWORD</h1>

                <p class="description">
                    Enter your email and we will send a link to reset your password.
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

                    <button class="submit-button" type="submit" :disabled="submitting">
                        SEND RESET LINK
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

                <p v-if="successMessage" class="success-alert" role="status">
                    {{ successMessage }}
                </p>

                <div class="bottom-divider"></div>

                <RouterLink class="back-link" :to="{ name: 'login' }">
                    <span class="arrow">←</span>
                    [ BACK TO SIGN IN ]
                </RouterLink>
            </div>
        </section>
    </main>
</template>
