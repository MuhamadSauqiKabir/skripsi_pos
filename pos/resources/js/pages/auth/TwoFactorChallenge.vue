<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

const form = useForm({
    code: '',
    recovery_code: '',
});
</script>

<template>
    <Head title="Two Factor Challenge" />

    <form
        class="gap-4 grid"
        @submit.prevent="form.post('/two-factor-challenge')"
    >
        <div class="nineties-auth-heading">
            <p>2FA Challenge</p>
            <h2>Masukkan kode autentikasi</h2>
        </div>
        <label class="nineties-field">
            <span>Kode 2FA</span>
            <input
                v-model="form.code"
                class="nineties-input"
                type="text"
                inputmode="numeric"
                placeholder="6 digit code"
            />
        </label>
        <label class="nineties-field">
            <span>Recovery Code</span>
            <input
                v-model="form.recovery_code"
                class="nineties-input"
                type="text"
                placeholder="Recovery code jika diperlukan"
            />
        </label>
        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            Verifikasi
        </button>
    </form>
</template>
