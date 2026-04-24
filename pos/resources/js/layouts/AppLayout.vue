<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const page = usePage<{
    auth: { user?: { name: string; role?: string } };
    flash: { success?: string; error?: string };
    currentPage?: string;
    dashboardNav?: Array<{ label: string; href: string; key: string }>;
    sidebarUtilities?: Array<{ label: string; href: string; key: string }>;
}>();

const iconMap: Record<string, string> = {
    overview: 'space_dashboard',
    orders: 'receipt_long',
    inventory: 'inventory_2',
    tables: 'table_restaurant',
    reports: 'bar_chart',
    profile: 'person',
    settings: 'settings',
};

const darkMode = ref(false);
const profileOpen = ref(false);

const pageTitle = computed(() =>
    page.props.dashboardNav?.find((item) => item.key === page.props.currentPage)?.label
    || page.props.sidebarUtilities?.find((item) => item.key === page.props.currentPage)?.label
    || 'Dashboard / Dasbor',
);

const applyTheme = () => {
    document.documentElement.classList.toggle('dark', darkMode.value);
    window.localStorage.setItem('nineties-theme', darkMode.value ? 'dark' : 'light');
};

const toggleTheme = () => {
    darkMode.value = !darkMode.value;
    applyTheme();
};

const logout = () => {
    router.post('/logout');
};

onMounted(() => {
    darkMode.value = window.localStorage.getItem('nineties-theme') === 'dark';
    applyTheme();
});
</script>

<template>
    <Head title="Dashboard / Dasbor" />

    <div class="min-h-screen bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
        <div class="flex min-h-screen">
            <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 bg-[#4e73df] text-white dark:bg-slate-900 lg:flex lg:flex-col">
                <div class="px-6 py-7">
                    <div class="text-xs font-semibold uppercase tracking-[0.25em] text-white/60">SB Admin 2 Tailwind</div>
                    <div class="mt-2 font-serif text-2xl font-bold">Nineties POS</div>
                </div>

                <nav class="space-y-1 px-4 py-2">
                    <Link
                        v-for="item in page.props.dashboardNav || []"
                        :key="item.key"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition"
                        :class="page.props.currentPage === item.key ? 'bg-white/14 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'"
                    >
                        <span class="material-symbols-outlined text-base">{{ iconMap[item.key] || 'dashboard' }}</span>
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="mt-6 px-4">
                    <div class="rounded-2xl bg-white/10 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Orders Sidebar / Sidebar Pesanan</div>
                        <div class="mt-3 space-y-2 text-sm">
                            <Link href="/dashboard/orders" class="block rounded-lg px-3 py-2 hover:bg-white/10">In-store Orders / Pesanan Toko</Link>
                            <Link href="/dashboard/orders#online-orders" class="block rounded-lg px-3 py-2 hover:bg-white/10">Online Orders / Pesanan Online</Link>
                        </div>
                    </div>
                </div>

                <div class="mt-auto px-4 py-5">
                    <div class="space-y-1">
                        <Link
                            v-for="item in page.props.sidebarUtilities || []"
                            :key="item.key"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white/70 transition hover:bg-white/10 hover:text-white"
                        >
                            <span class="material-symbols-outlined text-base">{{ iconMap[item.key] || 'settings' }}</span>
                            {{ item.label }}
                        </Link>
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold text-white/70 transition hover:bg-white/10 hover:text-white"
                            @click="logout"
                        >
                            <span class="material-symbols-outlined text-base">logout</span>
                            Logout / Keluar
                        </button>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col lg:ml-72">
                <header class="sticky top-0 z-30 bg-white dark:bg-slate-900">
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Dashboard</p>
                            <h1 class="font-serif text-2xl font-bold">{{ pageTitle }}</h1>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-200"
                                @click="toggleTheme"
                            >
                                <span class="material-symbols-outlined text-base">{{ darkMode ? 'light_mode' : 'dark_mode' }}</span>
                            </button>

                            <div class="relative">
                                <button
                                    type="button"
                                    class="flex items-center gap-3 rounded-xl bg-slate-100 px-3 py-2 text-left dark:bg-slate-800"
                                    @click="profileOpen = !profileOpen"
                                >
                                    <span class="material-symbols-outlined text-slate-500 dark:text-slate-300">account_circle</span>
                                    <div>
                                        <strong class="block text-sm">{{ page.props.auth.user?.name }}</strong>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ page.props.auth.user?.role }}</span>
                                    </div>
                                </button>

                                <div v-if="profileOpen" class="absolute right-0 mt-2 w-64 rounded-2xl bg-white p-2 dark:bg-slate-900">
                                    <Link href="/dashboard/profile" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">
                                        <span class="material-symbols-outlined text-base">person</span>
                                        Profile / Profil
                                    </Link>
                                    <Link href="/dashboard/settings" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">
                                        <span class="material-symbols-outlined text-base">settings</span>
                                        Settings / Pengaturan
                                    </Link>
                                    <button type="button" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm hover:bg-slate-100 dark:hover:bg-slate-800" @click="logout">
                                        <span class="material-symbols-outlined text-base">logout</span>
                                        Logout / Keluar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    <div v-if="page.props.flash.success" class="mb-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                        {{ page.props.flash.success }}
                    </div>
                    <div v-if="page.props.flash.error" class="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                        {{ page.props.flash.error }}
                    </div>

                    <slot />
                </main>

                <footer class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                    Nineties Coffee Admin Panel - Dashboard, Orders, Inventory, Reports.
                </footer>
            </div>
        </div>
    </div>
</template>
