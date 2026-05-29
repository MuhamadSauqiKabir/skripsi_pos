<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

defineProps<{ status?: string }>();

const form = useForm({
    email: '',
});
</script>

<template>
    <Head title="Forgot Password" />

    <form class="gap-4 grid" @submit.prevent="form.post('/forgot-password')">
        <div class="nineties-auth-heading">
            <p>Password Recovery</p>
            <h2>Reset akses staff</h2>
        </div>

        <p class="nineties-auth-copy">
            Masukkan email akun staff. Kami akan mengirim link reset password
            agar kamu bisa masuk lagi ke dashboard.
        </p>

        <label class="nineties-field">
            <span>Email</span>
            <input
                v-model="form.email"
                class="nineties-input"
                type="email"
                autocomplete="email"
                placeholder="Email akun staff"
            />
        </label>

        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            <span class="material-symbols-outlined text-base">mail</span>
            {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset' }}
        </button>

        <p class="text-sm text-center text-[#9c7c5a]">
            Ingat password?
            <Link href="/login" class="nineties-auth-link">Kembali login</Link>
        </p>

        <p
            v-if="status"
            class="px-4 py-3 text-sm rounded-[14px] bg-[#d1fae5] text-[#065f46]"
        >
            {{ status }}
        </p>
        <p
            v-if="form.errors.email"
            class="px-4 py-3 text-sm rounded-[14px] bg-[#fee2e2] text-[#991b1b]"
        >
            {{ form.errors.email }}
        </p>
    </form>
</template>
