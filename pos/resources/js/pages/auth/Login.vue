<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Masuk Staf" />

    <form class="gap-4 grid" @submit.prevent="submit">
        <div class="nineties-auth-heading">
            <p>Akses Internal</p>
            <h2>Masuk ke Dashboard</h2>
        </div>

        <label class="nineties-field">
            <span>Email</span>
            <input
                v-model="form.email"
                class="nineties-input"
                type="email"
                autocomplete="email"
                placeholder="superadmin@nineties.id"
            />
        </label>

        <label class="nineties-field">
            <span>Kata Sandi</span>
            <input
                v-model="form.password"
                class="nineties-input"
                type="password"
                autocomplete="current-password"
                placeholder="Masukkan kata sandi"
            />
        </label>

        <div class="gap-3 text-sm flex items-center justify-between">
            <label class="gap-2 flex items-center text-[#6b4226]">
                <input
                    v-model="form.remember"
                    type="checkbox"
                    class="rounded border-[#f5e6c8] text-[#c9a84c]"
                />
                Ingat saya
            </label>
            <Link
                v-if="canResetPassword"
                href="/forgot-password"
                class="nineties-auth-link"
                >Lupa kata sandi?</Link
            >
        </div>

        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            <span class="material-symbols-outlined text-base">lock_open</span>
            {{ form.processing ? 'Memproses...' : 'Masuk ke Dashboard' }}
        </button>

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
        <p
            v-if="form.errors.password"
            class="px-4 py-3 text-sm rounded-[14px] bg-[#fee2e2] text-[#991b1b]"
        >
            {{ form.errors.password }}
        </p>
    </form>
</template>
