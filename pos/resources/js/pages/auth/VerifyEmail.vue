<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

defineProps<{ status?: string }>();

const form = useForm({});
</script>

<template>
    <Head title="Verify Email" />

    <form
        class="gap-4 grid"
        @submit.prevent="form.post('/email/verification-notification')"
    >
        <div class="nineties-auth-heading">
            <p>Email Verification</p>
            <h2>Verifikasi email staff</h2>
        </div>
        <p class="nineties-auth-copy">
            Klik tombol di bawah untuk mengirim ulang link verifikasi.
        </p>
        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            Kirim Ulang Verifikasi
        </button>
    </form>
</template>
