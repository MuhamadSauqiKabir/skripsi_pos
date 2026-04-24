<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
    <Head title="Staff Login" />

    <form class="form-grid" @submit.prevent="submit">
        <div>
            <p class="eyebrow">Internal Access</p>
            <h2>Masuk ke POS Nineties Coffee</h2>
        </div>

        <label class="field">
            <span>Email</span>
            <input v-model="form.email" type="email" placeholder="owner@ninetiescoffee.test" />
        </label>

        <label class="field">
            <span>Password</span>
            <input v-model="form.password" type="password" placeholder="Masukkan password" />
        </label>

        <button class="primary-button" type="submit" :disabled="form.processing">
            {{ form.processing ? 'Memproses...' : 'Login' }}
        </button>

        <p class="muted">Demo akun: superadmin@ninetiescoffee.test / password</p>
        <p v-if="status" class="flash-success">{{ status }}</p>
        <p v-if="form.errors.email" class="flash-error">{{ form.errors.email }}</p>
    </form>
</template>
