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

    <form
        class="gap-4 grid"
        @submit.prevent="form.post('/user/confirm-password')"
    >
        <div class="nineties-auth-heading">
            <p>Security Check</p>
            <h2>Konfirmasi password</h2>
        </div>
        <label class="nineties-field">
            <span>Password Saat Ini</span>
            <input
                v-model="form.password"
                class="nineties-input"
                type="password"
                autocomplete="current-password"
                placeholder="Password saat ini"
            />
        </label>
        <button
            class="nineties-auth-submit"
            type="submit"
            :disabled="form.processing"
        >
            Konfirmasi
        </button>
    </form>
</template>
