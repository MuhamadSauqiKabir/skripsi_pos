<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Chart from 'primevue/chart';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    analytics: {
        gross_profit: number;
        revenue: number;
        total_orders: number;
        customer_rating: number;
        daily_trends: Array<{ date: string; amount: number }>;
        category_share: Array<{ name: string; count: number }>;
        top_menus: Array<any>;
    };
    filters: {
        start: string;
        end: string;
    };
    predictions: Array<any>;
    roleScope: { showAudit: boolean };
    auditLogs?: Array<any>;
}>();

const startDate = ref(props.filters?.start || '');
const endDate = ref(props.filters?.end || '');

const updateFilters = () => {
    // Check if route() exists, otherwise use hardcoded path
    const url = typeof (window as any).route === 'function' 
        ? (window as any).route('dashboard.reports') 
        : '/dashboard/reports';

    router.get(url, {
        start: startDate.value,
        end: endDate.value,
    }, { preserveState: true, preserveScroll: true });
};

// PrimeVue Chart Data: Revenue Trend
const lineChartData = computed(() => ({
    labels: (props.analytics?.daily_trends || []).map(d => d.date),
    datasets: [{
        label: 'Revenue',
        data: (props.analytics?.daily_trends || []).map(d => d.amount),
        fill: true,
        borderColor: '#3d2b1f',
        backgroundColor: 'rgba(61, 43, 31, 0.05)',
        tension: 0.4
    }]
}));

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
    }
};

// PrimeVue Chart Data: Category Distribution
const pieChartData = computed(() => ({
    labels: (props.analytics?.category_share || []).map(c => c.name),
    datasets: [{
        data: (props.analytics?.category_share || []).map(c => c.count),
        backgroundColor: ['#3d2b1f', '#d6b35a', '#9b8a72', '#26312d', '#ece4d9']
    }]
}));

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } }
};

const getExportUrl = (type: 'pdf' | 'excel') => {
    const baseUrl = type === 'pdf' ? '/management/reports/pdf' : '/management/reports/weekly-export';
    return `${baseUrl}?start=${startDate.value}&end=${endDate.value}`;
};
</script>

<template>
    <div class="gap-5 grid">
        <!-- Header & Filters -->
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div class="gap-4 md:flex-row md:items-end md:justify-between flex flex-col">
                <div>
                    <p class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase">
                        Business Insight
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold">
                        Reports / Laporan
                    </h2>
                    <p class="mt-2 text-sm text-[#6d6255] dark:text-[#c8bdaa]">
                        Pantau performa penjualan dan analisis profitabilitas.
                    </p>
                </div>
                <div class="gap-3 flex flex-wrap items-end">
                    <div class="gap-2 flex flex-col">
                        <label class="text-[10px] font-bold uppercase text-[#9b8a72]">Mulai</label>
                        <input type="date" v-model="startDate" @change="updateFilters" class="rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-2 text-xs" />
                    </div>
                    <div class="gap-2 flex flex-col">
                        <label class="text-[10px] font-bold uppercase text-[#9b8a72]">Selesai</label>
                        <input type="date" v-model="endDate" @change="updateFilters" class="rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-2 text-xs" />
                    </div>
                    <div class="gap-2 flex">
                        <a :href="getExportUrl('pdf')" class="min-h-11 gap-2 px-4 text-sm font-bold inline-flex items-center rounded-full bg-[#3d2b1f] text-[#f7f1e8]">
                            <span class="material-symbols-outlined text-base">picture_as_pdf</span> PDF
                        </a>
                        <a :href="getExportUrl('excel')" class="min-h-11 gap-2 px-4 text-sm font-bold inline-flex items-center rounded-full bg-[#26312d] text-[#f7f1e8]">
                            <span class="material-symbols-outlined text-base">download</span> Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="mt-8 gap-4 sm:grid-cols-2 lg:grid-cols-4 grid">
                <div class="rounded-lg p-6 bg-[#f1ece3] dark:bg-[#28322e]">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#9b8a72]">Revenue / Omzet</span>
                    <strong class="mt-3 block text-2xl">Rp {{ (analytics?.revenue || 0).toLocaleString('id-ID') }}</strong>
                </div>
                <div class="rounded-lg p-6 bg-[#e5eddf] text-[#273528] dark:bg-[#24342b] dark:text-[#edf7e8]">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#63745d] dark:text-[#c8d9c1]">Gross Profit</span>
                    <strong class="mt-3 block text-2xl">Rp {{ (analytics?.gross_profit || 0).toLocaleString('id-ID') }}</strong>
                </div>
                <div class="rounded-lg p-6 bg-[#e1e8ef] text-[#25384d] dark:bg-[#223142] dark:text-[#dcecff]">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#526b88] dark:text-[#b9d0ea]">Total Transaksi</span>
                    <strong class="mt-3 block text-2xl">{{ analytics?.total_orders || 0 }}</strong>
                </div>
                <div class="rounded-lg p-6 bg-[#efe8da] dark:bg-[#2d2b24]">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#867762]">Rating Pelanggan</span>
                    <strong class="mt-3 block text-2xl">{{ analytics?.customer_rating || 0 }} / 5</strong>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <div class="gap-5 grid lg:grid-cols-12">
            <section class="rounded-lg p-6 bg-[#fffaf2] dark:bg-[#1d2521] lg:col-span-8">
                <h3 class="font-serif text-xl font-bold mb-6">Daily Revenue Trend</h3>
                <div class="h-[300px]">
                    <Chart type="line" :data="lineChartData" :options="lineChartOptions" />
                </div>
            </section>
            <section class="rounded-lg p-6 bg-[#fffaf2] dark:bg-[#1d2521] lg:col-span-4">
                <h3 class="font-serif text-xl font-bold mb-6">Top Categories</h3>
                <div class="h-[300px] flex items-center justify-center">
                    <Chart type="pie" :data="pieChartData" :options="pieChartOptions" class="w-full" />
                </div>
            </section>
        </div>

        <!-- Predictive Stock -->
        <section class="rounded-lg p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div class="gap-4 flex items-center justify-between mb-6">
                <h2 class="font-serif text-xl font-bold">Predictive Stock / Prediksi Stok</h2>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-[#e5eddf] text-[#63745d] uppercase tracking-widest">Forecast</span>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <article v-for="prediction in predictions" :key="prediction.id" class="rounded-lg p-5 bg-[#f1ece3] dark:bg-[#28322e]">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#9b8a72] uppercase">Trend</p>
                    <h3 class="mt-2 text-lg font-bold">{{ prediction.menu_item?.name }}</h3>
                    <p class="mt-1 text-sm text-[#6d6255]">Prediksi {{ prediction.predicted_qty }} porsi terjual</p>
                    <div class="mt-4 h-1.5 rounded-full bg-[#ddd3c2]">
                        <div class="h-full rounded-full bg-[#d6b35a]" :style="{ width: `${prediction.trend_score}%` }"></div>
                    </div>
                </article>
            </div>
        </section>

        <!-- Top Menu Items Table -->
        <section class="rounded-lg p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <h3 class="font-serif text-xl font-bold mb-6">Top Selling Menus / Menu Terpopuler</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#ece4d9] text-[#7a6a58] uppercase text-[10px] tracking-widest">
                            <th class="pb-4 font-bold">Nama Menu</th>
                            <th class="pb-4 font-bold text-center">Terjual</th>
                            <th class="pb-4 font-bold text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ece4d9]">
                        <tr v-for="menu in (analytics?.top_menus || [])" :key="menu.name">
                            <td class="py-4 font-bold text-[#3d2b1f]">{{ menu.name }}</td>
                            <td class="py-4 text-center font-medium">{{ menu.sold_qty }} porsi</td>
                            <td class="py-4 text-right font-black">Rp {{ Number(menu.sales || 0).toLocaleString('id-ID') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Audit Logs (Admin Only) -->
        <section v-if="roleScope.showAudit && auditLogs" class="rounded-lg p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <h2 class="font-serif text-xl font-bold mb-6">Recent Activity / Audit Log</h2>
            <div class="space-y-3">
                <div v-for="log in auditLogs" :key="log.id" class="p-4 rounded-lg bg-[#f1ece3] dark:bg-[#28322e] flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <strong class="text-sm block">{{ log.action }}</strong>
                        <span class="text-xs text-[#6d6255]">{{ log.entity_type }} #{{ log.entity_id }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold block">{{ log.user?.name || 'System' }}</span>
                        <span class="text-[10px] text-[#9b8a72]">{{ new Date(log.created_at).toLocaleString() }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
