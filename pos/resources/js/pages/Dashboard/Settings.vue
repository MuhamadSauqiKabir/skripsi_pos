<script setup lang="ts">
import FormReports from '@/components/FormReports.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    integrations: Record<string, { label: string; status: string }>;
}>();
</script>

<template>
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-3xl font-bold">Settings / Pengaturan</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <article v-for="(integration, key) in integrations" :key="key" class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ key }}</p>
                    <strong class="mt-3 block text-xl">{{ integration.label }}</strong>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ integration.status }}</p>
                </article>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h3 class="font-serif text-2xl font-bold">Report Settings / Pengaturan Laporan</h3>
            <div class="mt-6">
                <FormReports />
            </div>
        </section>
    </div>
</template>
