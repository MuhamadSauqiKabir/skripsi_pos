<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    analytics: Record<string, number>;
    topMenus: Array<any>;
    predictions: Array<any>;
    salesByDay: Array<{ day: string; total: number }>;
    bentoCards: Array<{ title: string; value: number | string; meta: string }>;
}>();
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-12">
        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900 xl:col-span-7">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Dashboard / Dasbor</p>
            <h2 class="mt-3 font-serif text-4xl font-bold">Nineties Coffee Performance Hub</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-500 dark:text-slate-400">
                Ringkasan performa bisnis, pesanan aktif, prediksi stok, dan insight operasional untuk owner, kasir, dan barista.
            </p>

            <div class="mt-8 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Revenue / Pendapatan</p>
                    <strong class="mt-2 block text-3xl">Rp {{ Number(analytics.weeklyRevenue).toLocaleString('id-ID') }}</strong>
                </div>
                <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Profit / Laba</p>
                    <strong class="mt-2 block text-3xl">Rp {{ Number(analytics.grossProfit).toLocaleString('id-ID') }}</strong>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:col-span-5">
            <article v-for="item in bentoCards" :key="item.title" class="rounded-3xl bg-white p-5 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ item.title }}</p>
                <strong class="mt-4 block text-3xl">{{ item.value }}</strong>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ item.meta }}</p>
            </article>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900 xl:col-span-5">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-2xl font-bold">Top Menu / Menu Terlaris</h3>
                <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Top 6</span>
            </div>
            <div class="mt-6 space-y-3">
                <div v-for="item in topMenus" :key="item.id" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                    <div>
                        <strong class="block">{{ item.name }}</strong>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ item.sold_qty }} terjual</span>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Top</span>
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900 xl:col-span-4">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-2xl font-bold">Forecast / Prediksi</h3>
                <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Next Week</span>
            </div>
            <div class="mt-6 space-y-3">
                <article v-for="prediction in predictions" :key="prediction.id" class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">
                    <strong class="block">{{ prediction.menu_item?.name }}</strong>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Prediksi {{ prediction.predicted_qty }} porsi</p>
                    <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-400">Trend {{ prediction.trend_score }}%</p>
                </article>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900 xl:col-span-3">
            <h3 class="font-serif text-2xl font-bold">Live Status / Status Aktif</h3>
            <div class="mt-6 rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Active Orders / Pesanan Aktif</p>
                <strong class="mt-3 block text-4xl">{{ analytics.activeOrders }}</strong>
            </div>
            <div class="mt-4 rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Customer Rating / Rating Pelanggan</p>
                <strong class="mt-3 block text-4xl">{{ analytics.averageRating || 0 }}/5</strong>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900 xl:col-span-12">
            <div class="flex items-center justify-between">
                <h3 class="font-serif text-2xl font-bold">Sales Trend / Tren Penjualan</h3>
                <span class="text-xs uppercase tracking-[0.18em] text-slate-400">7 Days</span>
            </div>
            <div class="mt-6 grid gap-3">
                <div v-for="row in salesByDay" :key="row.day" class="grid gap-2 rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-800/70 md:grid-cols-[140px_minmax(0,1fr)_160px] md:items-center">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ row.day }}</span>
                    <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-full rounded-full bg-slate-800 dark:bg-slate-200" :style="{ width: `${Math.max(12, Number(row.total) / 1500)}%` }"></div>
                    </div>
                    <strong>Rp {{ Number(row.total).toLocaleString('id-ID') }}</strong>
                </div>
            </div>
        </section>
    </div>
</template>
