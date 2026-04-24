<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

defineProps<{ status?: string }>();

const form = useForm({});
</script>

<template>
    <Head title="Verify Email" />

    <form class="panel-card form-grid" @submit.prevent="form.post('/email/verification-notification')">
        <div>
            <p class="eyebrow">Email Verification</p>
            <h2>Verifikasi email staff</h2>
            <p class="muted">Klik tombol di bawah untuk mengirim ulang link verifikasi.</p>
        </div>
        <button class="primary-button" type="submit">Kirim Ulang Verifikasi</button>
    </form>
</template>
