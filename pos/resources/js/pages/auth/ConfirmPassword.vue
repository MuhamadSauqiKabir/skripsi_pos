<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AuthLayout });

const form = useForm({
    password: '',
});
</script>

<template>
    <Head title="Confirm Password" />

    <form class="panel-card form-grid" @submit.prevent="form.post('/user/confirm-password')">
        <div>
            <p class="eyebrow">Security Check</p>
            <h2>Konfirmasi password</h2>
        </div>
        <input v-model="form.password" type="password" placeholder="Password saat ini" />
        <button class="primary-button" type="submit">Konfirmasi</button>
    </form>
</template>
