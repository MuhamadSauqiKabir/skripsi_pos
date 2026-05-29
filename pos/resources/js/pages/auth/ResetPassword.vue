<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});
</script>

<template>
    <Head title="Reset Password" />

    <form class="gap-4 grid" @submit.prevent="form.post('/reset-password')">
        <div class="nineties-auth-heading">
            <p>Reset Password</p>
            <h2>Buat password baru</h2>
        </div>

        <label class="nineties-field">
            <span>Email</span>
            <input
                v-model="form.email"
                class="nineties-input"
                type="email"
                readonly
            />
        </label>

        <label class="nineties-field">
            <span>Password Baru</span>
            <input
                v-model="form.password"
                class="nineties-input"
                type="password"
                autocomplete="new-password"
                placeholder="Password baru"
            />
        </label>

        <label class="nineties-field">
            <span>Konfirmasi Password</span>
            <input
                v-model="form.password_confirmation"
                class="nineties-input"
                type="password"
                autocomplete="new-password"
                placeholder="Ulangi password baru"
            />
        </label>

        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            <span class="material-symbols-outlined text-base">key</span>
            {{ form.processing ? 'Menyimpan...' : 'Simpan Password' }}
        </button>

        <p class="text-sm text-center text-[#9c7c5a]">
            Sudah aman?
            <Link href="/login" class="nineties-auth-link">Kembali login</Link>
        </p>

        <div
            v-if="Object.keys(form.errors).length"
            class="gap-2 px-4 py-3 text-sm grid rounded-[14px] bg-[#fee2e2] text-[#991b1b]"
        >
            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
        </div>
    </form>
</template>
