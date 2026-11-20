import { ref } from 'vue'
import { api } from '@/api/client'
import type { ApiResponse } from '@/api/client'
import type { ContactMessage } from '@/types/contact'

const minWaitMs = 3000

interface FormTokenResponse {
    data: {
        form_token: string
    }
}

export function useContact() {
    const messages = ref<ContactMessage[]>([])
    const inboxLoading = ref(false)
    const inboxError = ref('')
    const sending = ref(false)
    const formReady = ref(false)

    let formToken = ''
    let openedAt = 0

    async function openForm() {
        formReady.value = false
        const response = await api<FormTokenResponse>('/contact-messages/form', { auth: false, toast: false })
        formToken = response.data.form_token
        openedAt = Date.now()
        formReady.value = true
    }

    async function loadInbox() {
        inboxLoading.value = true
        inboxError.value = ''

        try {
            const response = await api<ApiResponse<ContactMessage[]>>('/contact-messages', { toast: false })
            messages.value = response.data
        } catch (error) {
            inboxError.value = error instanceof Error ? error.message : 'Could not load messages.'
        } finally {
            inboxLoading.value = false
        }
    }

    async function sendMessage(input: { name: string, email: string, message: string, companyWebsite: string, turnstileToken: string }) {
        sending.value = true

        try {
            const wait = minWaitMs - (Date.now() - openedAt)

            if (wait > 0) {
                await new Promise((resolve) => window.setTimeout(resolve, wait))
            }

            const response = await api<ApiResponse<unknown>>('/contact-messages', {
                method: 'POST',
                auth: false,
                toast: false,
                body: {
                    name: input.name,
                    email: input.email,
                    message: input.message,
                    form_token: formToken,
                    turnstile_token: input.turnstileToken,
                    company_website: input.companyWebsite,
                },
            })

            try {
                await openForm()
            } catch {
                formToken = ''
                formReady.value = false
            }

            return response.message || 'Thanks, your message was sent.'
        } finally {
            sending.value = false
        }
    }

    return {
        messages,
        inboxLoading,
        inboxError,
        sending,
        formReady,
        openForm,
        loadInbox,
        sendMessage,
    }
}
