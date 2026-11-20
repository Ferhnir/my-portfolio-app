<script setup lang="ts">
import { onMounted, ref, useTemplateRef } from 'vue'
import { ApiError } from '@/api/client'
import { isAuthenticated } from '@/auth'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import TurnstileField from '@/components/TurnstileField.vue'
import { useContact } from '@/composables/useContact'

const name = ref('')
const email = ref('')
const message = ref('')
const companyWebsite = ref('')
const turnstileToken = ref('')
const turnstile = useTemplateRef<{ reset: () => void }>('turnstile')
const fieldErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const formNotice = ref('')
const formLoading = ref(true)

const { messages, inboxLoading, inboxError, sending, formReady, openForm, loadInbox, sendMessage } = useContact()

onMounted(async () => {
    try {
        await openForm()
    } catch (error) {
        formError.value = error instanceof ApiError ? error.message : 'Could not open the form.'
    } finally {
        formLoading.value = false
    }

    if (isAuthenticated.value) {
        await loadInbox()
    }
})

function sentAt(value: string | null) {
    if (!value) {
        return ''
    }

    return new Date(value).toLocaleString()
}

async function onSubmit() {
    fieldErrors.value = {}
    formError.value = ''
    formNotice.value = ''

    try {
        formNotice.value = await sendMessage({
            name: name.value,
            email: email.value,
            message: message.value,
            companyWebsite: companyWebsite.value,
            turnstileToken: turnstileToken.value,
        })
        name.value = ''
        email.value = ''
        message.value = ''
        companyWebsite.value = ''
        turnstile.value?.reset()

        if (isAuthenticated.value) {
            await loadInbox()
        }
    } catch (error) {
        if (error instanceof ApiError) {
            fieldErrors.value = error.errors
            const fieldError = ['name', 'email', 'message'].some((key) => error.errors[key]?.length)

            if (!fieldError) {
                formError.value = error.errors.turnstile_token?.[0] || error.errors.form_token?.[0] || error.message
            }

            turnstile.value?.reset()

            return
        }

        formError.value = 'Could not send the message.'
    }
}
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-8 p-8">
    <header class="space-y-2 border-b border-linear-border pb-6">
      <h1 class="flex items-center gap-3 text-[22px] font-bold uppercase tracking-tight text-linear-text">
        <span class="text-linear-green">//</span>
        Contact
      </h1>
      <p class="text-xs font-medium uppercase tracking-wide text-linear-blue">
        Send a note. It comes to the inbox.
      </p>
    </header>

    <section v-if="isAuthenticated" class="max-w-3xl space-y-4">
      <h2 class="text-sm font-bold uppercase tracking-widest text-linear-secondary">Inbox</h2>
      <p v-if="inboxLoading" class="text-xs uppercase tracking-wide text-linear-secondary">Loading messages.</p>
      <p v-else-if="inboxError" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ inboxError }}</p>
      <p v-else-if="messages.length === 0" class="text-xs uppercase tracking-wide text-linear-secondary">No messages yet.</p>
      <article
          v-for="item in messages"
          :key="item.id"
          class="space-y-2 rounded-md border border-linear-border bg-linear-surface p-5"
      >
        <div class="flex flex-wrap items-baseline justify-between gap-2">
          <h3 class="text-sm font-bold uppercase tracking-wide text-linear-text">{{ item.name }}</h3>
          <time class="text-[11px] uppercase tracking-wide text-linear-muted" :datetime="item.created_at || undefined">
            {{ sentAt(item.created_at) }}
          </time>
        </div>
        <p class="text-xs text-linear-blue">{{ item.email }}</p>
        <p class="whitespace-pre-wrap text-sm leading-relaxed text-linear-secondary">{{ item.message }}</p>
      </article>
    </section>

    <form class="relative max-w-xl space-y-4 rounded-md border border-linear-border bg-linear-surface p-6" @submit.prevent="onSubmit">
      <p class="text-[11px] font-bold uppercase tracking-widest text-linear-red">Send a message</p>

      <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
        <label>
          Company website
          <input v-model="companyWebsite" type="text" name="company_website" tabindex="-1" autocomplete="off">
        </label>
      </div>

      <label class="block space-y-2" for="contact-name">
        <span class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">Name</span>
        <BaseInput id="contact-name" v-model="name" name="name" required placeholder="Your name" autocomplete="name" />
        <p v-if="fieldErrors.name?.[0]" class="text-[11px] uppercase tracking-wide text-linear-red">{{ fieldErrors.name[0] }}</p>
      </label>

      <label class="block space-y-2" for="contact-email">
        <span class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">Email</span>
        <BaseInput id="contact-email" v-model="email" name="email" type="email" required placeholder="you@example.com" autocomplete="email" />
        <p v-if="fieldErrors.email?.[0]" class="text-[11px] uppercase tracking-wide text-linear-red">{{ fieldErrors.email[0] }}</p>
      </label>

      <label class="block space-y-2" for="contact-message">
        <span class="block text-[11px] font-bold uppercase tracking-widest text-linear-secondary">Message</span>
        <BaseInput id="contact-message" v-model="message" name="message" multiline required placeholder="What would you like to talk about?" />
        <p v-if="fieldErrors.message?.[0]" class="text-[11px] uppercase tracking-wide text-linear-red">{{ fieldErrors.message[0] }}</p>
      </label>

      <TurnstileField ref="turnstile" v-model="turnstileToken" />

      <p v-if="formLoading" class="text-xs uppercase tracking-wide text-linear-secondary">Opening the form.</p>
      <p v-if="formNotice" class="text-xs text-linear-green" role="status">{{ formNotice }}</p>
      <p v-if="formError" class="text-xs uppercase tracking-wide text-linear-red" role="alert">{{ formError }}</p>

      <BaseButton type="submit" :disabled="sending || formLoading || !formReady || turnstileToken === ''">
        {{ sending ? 'Sending' : 'Send message' }}
      </BaseButton>
    </form>
  </div>
</template>
