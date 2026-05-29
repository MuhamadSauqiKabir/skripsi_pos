<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useLanguage } from '@/composables/useLanguage';
import { usePage } from '@inertiajs/vue3';
import Chart from 'primevue/chart';
import { computed } from 'vue';

defineOptions({ layout: AppLayout });

type Analytics = {
    activeOrders: number;
    weeklyRevenue: number;
    grossProfit: number;
    averageRating: number;
};

type TopMenu = {
    id: number;
    name: string;
    sold_qty: number;
};

type Prediction = {
    id: number;
    predicted_qty: number;
    trend_score: number;
    menu_item?: {
        name?: string;
    };
};

type SalesByDay = {
    day: string;
    total: number;
};

const props = defineProps<{
    analytics: Analytics;
    topMenus: TopMenu[];
    predictions: Prediction[];
    salesByDay: SalesByDay[];
    bentoCards: Array<{ key: string; value: number | string }>;
}>();

const { language } = useLanguage();
const page = usePage<{ settings?: Record<string, string | null> }>();

const copy = computed(() =>
    language.value === 'EN'
        ? {
              online: 'System Online',
              title: 'Performance Center',
              intro: 'Monitor operational metrics, sales trends, and stock efficiency from one clean dashboard.',
              activeOrders: 'Active Orders',
              revenue: 'Revenue',
              grossProfit: 'Gross Profit',
              orders: 'Orders',
              satisfaction: 'Satisfaction',
              thisWeek: 'This week',
              grossMeta: 'Gross margin',
              activeMeta: 'Active queue',
              satisfactionMeta: 'Customer rating',
              stable: 'Stable',
              excellent: 'Excellent',
              salesTrend: 'Sales Trend',
              salesTrendMeta: 'Revenue analysis from the last 7 days',
              topSellers: 'Top Sellers',
              topSellersMeta: 'Most popular menu this month',
              soldPortion: 'portions sold',
              stockForecast: 'Stock Forecast',
              estimatedNeed: 'Estimated need',
              portions: 'portions',
              operationalStatus: 'Operational Status',
          }
        : {
              online: 'Sistem Aktif',
              title: 'Pusat Performa',
              intro: 'Pantau metrik operasional, tren penjualan, dan efisiensi stok dari satu dashboard yang rapi.',
              activeOrders: 'Pesanan Aktif',
              revenue: 'Pendapatan',
              grossProfit: 'Laba Kotor',
              orders: 'Pesanan',
              satisfaction: 'Kepuasan',
              thisWeek: 'Minggu ini',
              grossMeta: 'Margin kotor',
              activeMeta: 'Antrean aktif',
              satisfactionMeta: 'Rating pelanggan',
              stable: 'Stabil',
              excellent: 'Baik',
              salesTrend: 'Tren Penjualan',
              salesTrendMeta: 'Analisis pendapatan 7 hari terakhir',
              topSellers: 'Menu Terlaris',
              topSellersMeta: 'Menu paling diminati bulan ini',
              soldPortion: 'porsi terjual',
              stockForecast: 'Prediksi Stok',
              estimatedNeed: 'Estimasi kebutuhan',
              portions: 'porsi',
              operationalStatus: 'Status Operasional',
          },
);

const formatCurrency = (value: number | string | null | undefined) =>
    `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

const formatCompactCurrency = (value: number | string) =>
    new Intl.NumberFormat('id-ID', {
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(Number(value || 0));

const formatDay = (value: string) => {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
    }).format(date);
};

const metricCards = computed(() => [
    {
        title: copy.value.revenue,
        value: formatCurrency(props.analytics.weeklyRevenue),
        meta: copy.value.thisWeek,
        icon: 'payments',
        trend: '+12.5%',
    },
    {
        title: copy.value.grossProfit,
        value: formatCurrency(props.analytics.grossProfit),
        meta: copy.value.grossMeta,
        icon: 'account_balance_wallet',
        trend: '+8.2%',
    },
    {
        title: copy.value.orders,
        value: props.analytics.activeOrders || 0,
        meta: copy.value.activeMeta,
        icon: 'shopping_bag',
        trend: copy.value.stable,
    },
    {
        title: copy.value.satisfaction,
        value: `${props.analytics.averageRating || 0}/5.0`,
        meta: copy.value.satisfactionMeta,
        icon: 'stars',
        trend: copy.value.excellent,
    },
]);

const salesChartData = computed(() => ({
    labels: props.salesByDay.map((row) => formatDay(row.day)),
    datasets: [
        {
            label: copy.value.revenue,
            data: props.salesByDay.map((row) => Number(row.total || 0)),
            fill: true,
            borderColor: '#3d2b1f',
            backgroundColor: 'rgba(61, 43, 31, 0.05)',
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#3d2b1f',
            borderWidth: 3,
        },
    ],
}));

const statusCardText = computed(() => ({
    online_orders:
        language.value === 'EN'
            ? ['Online Orders', 'Digital payment is ready']
            : ['Pesanan Online', 'Pembayaran digital siap diproses'],
    low_stock:
        language.value === 'EN'
            ? ['Low Stock', 'Ingredient needs attention']
            : ['Stok Menipis', 'Bahan perlu diperhatikan'],
    table_efficiency:
        language.value === 'EN'
            ? ['Table Efficiency', 'Average table usage']
            : ['Efisiensi Meja', 'Rata-rata penggunaan meja'],
    satisfaction:
        language.value === 'EN'
            ? ['Satisfaction', 'Customer rating']
            : ['Kepuasan', 'Rating pelanggan'],
}));

const statusCardLabel = (key: string, index: 0 | 1) =>
    (statusCardText.value as Record<string, string[]>)[key]?.[index] ?? '';

const shopName = computed(
    () => page.props.settings?.shop_name || 'Nineties Coffee',
);

const salesChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#3d2b1f',
            titleFont: { family: 'Plus Jakarta Sans', size: 12 },
            bodyFont: { family: 'Plus Jakarta Sans', size: 14, weight: 'bold' },
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: (context: any) => ` ${formatCurrency(context.raw)}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: '#9b8a72', font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 } },
        },
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(155, 138, 114, 0.1)', drawBorder: false },
            ticks: {
                color: '#9b8a72',
                font: { family: 'Plus Jakarta Sans', size: 11 },
                callback: (value: any) => formatCompactCurrency(value),
            },
        },
    },
}));
</script>

<template>
    <div class="space-y-6">
        <!-- Header Section -->
        <header class="relative overflow-hidden rounded-2xl bg-[#3d2b1f] p-8 text-[#f7f2e8] shadow-xl">
            <div class="relative z-10 flex flex-col justify-between gap-6 md:flex-row md:items-center">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em]">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                        </span>
                        {{ copy.online }}
                    </div>
                    <h1 class="font-serif text-3xl font-bold tracking-tight md:text-4xl">
                        {{ shopName }} {{ copy.title }}
                    </h1>
                    <p class="max-w-xl text-sm leading-relaxed text-[#f7f2e8]/70">
                        {{ copy.intro }}
                    </p>
                </div>
                <div class="flex items-center gap-4 border-l border-white/10 pl-6">
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{ copy.activeOrders }}</p>
                        <p class="text-3xl font-black">{{ analytics.activeOrders }}</p>
                    </div>
                    <span class="material-symbols-outlined text-4xl text-white/20">monitoring</span>
                </div>
            </div>
            <!-- Decorative background element -->
            <div class="absolute -right-10 -top-20 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
        </header>

        <!-- Metric Grid -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article
                v-for="item in metricCards"
                :key="item.title"
                class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:bg-[#1d2521]"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-[#9b8a72]">
                            {{ item.title }}
                        </p>
                        <h3 class="mt-2 text-2xl font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">
                            {{ item.value }}
                        </h3>
                    </div>
                    <div class="rounded-lg bg-[#f7f2e8] p-2 text-[#3d2b1f] group-hover:bg-[#3d2b1f] group-hover:text-[#f7f2e8] transition-colors">
                        <span class="material-symbols-outlined text-xl">{{ item.icon }}</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span :class="[
                        'text-[10px] font-bold px-1.5 py-0.5 rounded',
                        item.trend.startsWith('+') ? 'bg-green-100 text-green-700' : 'bg-[#f1ece3] text-[#7a6a58]'
                    ]">
                        {{ item.trend }}
                    </span>
                    <span class="text-[11px] text-[#9b8a72]">{{ item.meta }}</span>
                </div>
            </article>
        </section>

        <!-- Main Content Grid -->
        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Sales Chart -->
            <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-8 dark:bg-[#1d2521]">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h3 class="font-serif text-xl font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ copy.salesTrend }}</h3>
                        <p class="text-xs text-[#9b8a72]">{{ copy.salesTrendMeta }}</p>
                    </div>
                    <div class="flex gap-2">
                        <div class="h-3 w-3 rounded-full bg-[#3d2b1f]"></div>
                        <span class="text-xs font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ copy.revenue }}</span>
                    </div>
                </div>
                <div class="h-[340px]">
                    <Chart type="line" :data="salesChartData" :options="salesChartOptions" />
                </div>
            </section>

            <!-- Top Menu List -->
            <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-4 dark:bg-[#1d2521]">
                <h3 class="font-serif text-xl font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ copy.topSellers }}</h3>
                <p class="mb-6 text-xs text-[#9b8a72]">{{ copy.topSellersMeta }}</p>
                
                <div class="space-y-4">
                    <div
                        v-for="(item, index) in topMenus"
                        :key="item.id"
                        class="flex items-center gap-4 rounded-xl border border-transparent bg-[#f7f2e8]/50 p-3 transition-all hover:border-[#ece4d9] hover:bg-white dark:bg-[#28322e]"
                    >
                        <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-[#3d2b1f] text-sm font-black text-[#f7f2e8]">
                            {{ index + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ item.name }}</p>
                            <p class="text-[11px] text-[#9b8a72]">{{ item.sold_qty }} {{ copy.soldPortion }}</p>
                        </div>
                        <div class="h-2 w-12 rounded-full bg-[#ece4d9] overflow-hidden">
                            <div class="h-full bg-[#3d2b1f]" :style="{ width: `${100 - (index * 15)}%` }"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Forecasts -->
            <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-6 dark:bg-[#1d2521]">
                <h3 class="font-serif text-xl font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ copy.stockForecast }}</h3>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <article
                        v-for="prediction in predictions"
                        :key="prediction.id"
                        class="rounded-xl border border-[#ece4d9] p-4 transition-colors hover:bg-[#f7f2e8]/30"
                    >
                        <div class="flex justify-between">
                            <strong class="text-sm text-[#3d2b1f] dark:text-[#f7f2e8]">{{ prediction.menu_item?.name }}</strong>
                            <span class="text-[10px] font-bold text-green-600">+{{ prediction.trend_score }}%</span>
                        </div>
                        <div class="mt-3 h-1.5 w-full rounded-full bg-[#ece4d9] overflow-hidden">
                            <div class="h-full bg-[#3d2b1f]" :style="{ width: `${prediction.trend_score}%` }"></div>
                        </div>
                        <p class="mt-3 text-[11px] text-[#9b8a72]">{{ copy.estimatedNeed }}: <strong>{{ prediction.predicted_qty }} {{ copy.portions }}</strong></p>
                    </article>
                </div>
            </section>

            <!-- Live Status / Bento -->
            <section class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-6 dark:bg-[#1d2521]">
                <h3 class="font-serif text-xl font-bold text-[#3d2b1f] dark:text-[#f7f2e8]">{{ copy.operationalStatus }}</h3>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="item in bentoCards"
                        :key="item.key"
                        class="flex flex-col justify-between rounded-xl bg-[#f1ece3]/40 p-4 dark:bg-[#28322e]"
                    >
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#9b8a72]">{{ statusCardLabel(item.key, 0) || item.key }}</p>
                        <div class="mt-4 flex items-baseline justify-between">
                            <strong class="text-xl text-[#3d2b1f] dark:text-[#f7f2e8]">{{ item.value }}</strong>
                            <span class="material-symbols-outlined text-lg opacity-20">sensors</span>
                        </div>
                        <p class="mt-2 text-[11px] text-[#7a6a58]">{{ statusCardLabel(item.key, 1) }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for small areas if needed */
.overview-page {
    scrollbar-width: thin;
    scrollbar-color: #3d2b1f #f7f2e8;
}
</style>
