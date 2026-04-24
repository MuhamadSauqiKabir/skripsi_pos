<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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

    <form class="panel-card form-grid" @submit.prevent="form.post('/reset-password')">
        <div>
            <p class="eyebrow">Reset Password</p>
            <h2>Buat password baru</h2>
        </div>
        <input v-model="form.email" type="email" readonly />
        <input v-model="form.password" type="password" placeholder="Password baru" />
        <input v-model="form.password_confirmation" type="password" placeholder="Konfirmasi password" />
        <button class="primary-button" type="submit">Simpan Password</button>
    </form>
</template>
