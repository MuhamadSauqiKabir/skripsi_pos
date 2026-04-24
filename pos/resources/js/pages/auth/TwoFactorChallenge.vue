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

    <form class="panel-card form-grid" @submit.prevent="form.post('/two-factor-challenge')">
        <div>
            <p class="eyebrow">2FA Challenge</p>
            <h2>Masukkan kode autentikasi</h2>
        </div>
        <input v-model="form.code" type="text" placeholder="6 digit code" />
        <input v-model="form.recovery_code" type="text" placeholder="Recovery code jika diperlukan" />
        <button class="primary-button" type="submit">Verifikasi</button>
    </form>
</template>
