<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

defineProps<{ status?: string }>();

const form = useForm({
    email: '',
});
</script>

<template>
    <Head title="Forgot Password" />

    <form class="panel-card form-grid" @submit.prevent="form.post('/forgot-password')">
        <div>
            <p class="eyebrow">Password Recovery</p>
            <h2>Reset akses staff</h2>
        </div>
        <input v-model="form.email" type="email" placeholder="Email akun staff" />
        <button class="primary-button" type="submit">Kirim Link Reset</button>
    </form>
</template>
