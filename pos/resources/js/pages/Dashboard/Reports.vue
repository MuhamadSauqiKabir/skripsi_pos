<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import FormReports from '@/components/FormReports.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

defineProps<{
    report: Record<string, any>;
    predictions: Array<any>;
    auditLogs: Array<any>;
    roleScope: { showAudit: boolean };
}>();

const reportModal = ref(false);
</script>

<template>
    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="font-serif text-2xl font-bold">Reports / Laporan</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Rekap mingguan performa penjualan, profit, dan prediksi stok.</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="reportModal = true">Open FormReports.vue</button>
                    <a href="/management/reports/weekly-export" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900">
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Revenue</p>
                    <strong class="mt-2 block text-2xl">Rp {{ Number(report.revenue).toLocaleString('id-ID') }}</strong>
                </div>
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Gross Profit</p>
                    <strong class="mt-2 block text-2xl">Rp {{ Number(report.gross_profit).toLocaleString('id-ID') }}</strong>
                </div>
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Orders</p>
                    <strong class="mt-2 block text-2xl">{{ report.total_orders }}</strong>
                </div>
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Rating</p>
                    <strong class="mt-2 block text-2xl">{{ report.average_rating || 0 }}/5</strong>
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-2xl font-bold">Predictive Stock / Prediksi Stok</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article v-for="prediction in predictions" :key="prediction.id" class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Trend</p>
                    <h3 class="mt-3 text-xl font-bold">{{ prediction.menu_item?.name }}</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Prediksi {{ prediction.predicted_qty }} porsi</p>
                    <p class="mt-2 text-xs uppercase tracking-[0.16em] text-slate-400">{{ prediction.trend_score }}%</p>
                </article>
            </div>
        </section>

        <section v-if="roleScope.showAudit" class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-2xl font-bold">Audit Log</h2>
            <div class="mt-4 grid gap-3">
                <div v-for="log in auditLogs" :key="log.id" class="grid gap-1 rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-800/70 md:grid-cols-[minmax(0,1.2fr)_180px_180px]">
                    <strong>{{ log.action }}</strong>
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ log.user?.name || 'System' }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ log.entity_type }} #{{ log.entity_id }}</span>
                </div>
            </div>
        </section>

        <FormModal :open="reportModal" title="FormReports.vue" subtitle="Report Settings / Pengaturan Laporan" @close="reportModal = false">
            <FormReports />
            <FormModalActions>
                <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="reportModal = false">Close</button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
