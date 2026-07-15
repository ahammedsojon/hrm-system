<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import AuthLayout from '../layouts/AuthLayout.vue';
import BaseInput from '../components/ui/BaseInput.vue';
import BaseButton from '../components/ui/BaseButton.vue';
import BaseAlert from '../components/ui/BaseAlert.vue';

const router = useRouter();
const { login, isLoading } = useAuth();

const email = ref('');
const password = ref('');
const errors = ref({});
const generalError = ref('');

async function handleSubmit() {
    errors.value = {};
    generalError.value = '';

    try {
        await login({ email: email.value, password: password.value });
        router.push({ name: 'dashboard' });
    } catch (error) {
        if (error.response?.status === 422) {
            const validationErrors = error.response.data.errors ?? {};
            errors.value = Object.fromEntries(
                Object.entries(validationErrors).map(([key, messages]) => [key, messages[0]]),
            );
        } else if (error.response?.status === 401) {
            generalError.value = 'Invalid credentials. Please try again.';
        } else {
            generalError.value = 'An unexpected error occurred. Please try again.';
        }
    }
}
</script>

<template>
    <AuthLayout>
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900">Sign in</h1>
                <p class="mt-2 text-sm text-gray-500">Welcome back to HRM System</p>
            </div>

            <BaseAlert v-if="generalError" class="mb-6">
                {{ generalError }}
            </BaseAlert>

            <form class="space-y-5" @submit.prevent="handleSubmit">
                <BaseInput
                    id="email"
                    v-model="email"
                    label="Email"
                    type="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    :error="errors.email"
                />

                <BaseInput
                    id="password"
                    v-model="password"
                    label="Password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    :error="errors.password"
                />

                <BaseButton
                    type="submit"
                    class="w-full"
                    :loading="isLoading"
                >
                    Sign in
                </BaseButton>
            </form>
        </div>
    </AuthLayout>
</template>
