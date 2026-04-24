<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const page = usePage<{ auth: { user?: { name: string; role?: string; email?: string } } }>();
</script>

<template>
    <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
        <h2 class="font-serif text-3xl font-bold">Profile / Profil</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Name / Nama</p>
                <strong class="mt-3 block text-xl">{{ page.props.auth.user?.name }}</strong>
            </div>
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Role / Peran</p>
                <strong class="mt-3 block text-xl">{{ page.props.auth.user?.role }}</strong>
            </div>
            <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Workspace / Area</p>
                <strong class="mt-3 block text-xl">Nineties Coffee Admin</strong>
            </div>
        </div>
    </section>
</template>
