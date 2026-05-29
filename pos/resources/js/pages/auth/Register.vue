<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="Register" />

    <form class="gap-4 grid" @submit.prevent="submit">
        <div class="nineties-auth-heading">
            <p>Staff Registration</p>
            <h2>Buat akun POS baru</h2>
        </div>

        <label class="nineties-field">
            <span>Nama Lengkap</span>
            <input
                v-model="form.name"
                class="nineties-input"
                type="text"
                autocomplete="name"
                placeholder="Nama staff"
            />
        </label>

        <label class="nineties-field">
            <span>Email</span>
            <input
                v-model="form.email"
                class="nineties-input"
                type="email"
                autocomplete="email"
                placeholder="email@ninetiescoffee.id"
            />
        </label>

        <label class="nineties-field">
            <span>Password</span>
            <input
                v-model="form.password"
                class="nineties-input"
                type="password"
                autocomplete="new-password"
                placeholder="Min. 8 karakter"
            />
        </label>

        <label class="nineties-field">
            <span>Konfirmasi Password</span>
            <input
                v-model="form.password_confirmation"
                class="nineties-input"
                type="password"
                autocomplete="new-password"
                placeholder="Ulangi password"
            />
        </label>

        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            <span class="material-symbols-outlined text-base">person_add</span>
            {{ form.processing ? 'Mendaftarkan...' : 'Daftar Akun' }}
        </button>

        <p class="text-sm text-center text-[#9c7c5a]">
            Sudah punya akun?
            <Link href="/login" class="nineties-auth-link">Masuk</Link>
        </p>

        <div
            v-if="Object.keys(form.errors).length"
            class="gap-2 px-4 py-3 text-sm grid rounded-[14px] bg-[#fee2e2] text-[#991b1b]"
        >
            <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
        </div>
    </form>
</template>
