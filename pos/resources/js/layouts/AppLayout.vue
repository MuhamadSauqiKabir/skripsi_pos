<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import OrderSidebar from '@/components/OrderSidebar.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

type DashboardUser = {
    name?: string;
    role?: string;
};

const page = usePage<{
    auth: { user?: DashboardUser };
    flash: { success?: string; error?: string };
    currentPage?: string;
    analytics?: {
        activeOrders?: number;
        weeklyRevenue?: number;
    };
    settings?: Record<string, string | null>;
    dashboardNav?: Array<{ label: string; href: string; key: string }>;
    sidebarUtilities?: Array<{ label: string; href: string; key: string }>;
    orderSidebar?: {
        tables: Array<any>;
        menuItems: Array<any>;
        onlineOrders: Array<any>;
    };
}>();

const { language, labels, setLanguage } = useLanguage();

const iconMap: Record<string, string> = {
    overview: 'space_dashboard',
    orders: 'receipt_long',
    inventory: 'inventory_2',
    tables: 'table_restaurant',
    reports: 'bar_chart',
    profile: 'person',
    settings: 'settings',
    users: 'groups',
    'web-content': 'web',
};

const descriptionMap = {
    IND: {
        overview:
            'Pantau performa, stok, pesanan, dan tren operasional hari ini.',
        orders: 'Kelola antrean pesanan toko, online order, dan status pembayaran.',
        inventory:
            'Atur produk, kategori, restock bahan, dan alert stok menipis.',
        tables: 'Kelola QR meja, kapasitas, dan efisiensi penggunaan area makan di tempat.',
        reports: 'Lihat performa mingguan, prediksi stok, dan audit aktivitas.',
        profile: 'Kelola profil akun pengguna dashboard.',
        settings: 'Konfigurasi integrasi, laporan, pembayaran, dan pengiriman.',
        users: 'Kelola akun staf, jabatan, dan akses dashboard.',
        'web-content': 'Kelola konten website publik Nineties Coffee.',
    },
    EN: {
        overview:
            'Monitor performance, stock, orders, and operational trends today.',
        orders: 'Manage in-store queues, online orders, and payment status.',
        inventory:
            'Manage products, categories, restock actions, and low-stock alerts.',
        tables: 'Manage table QR codes, capacity, and dine-in efficiency.',
        reports:
            'Review weekly performance, stock predictions, and activity audits.',
        profile: 'Manage the current dashboard account profile.',
        settings: 'Configure integrations, reports, payments, and shipping.',
        users: 'Manage staff accounts, roles, and dashboard access.',
        'web-content': 'Manage public website content for Nineties Coffee.',
    },
};

const navLabelMap = {
    IND: {
        overview: 'Dasbor',
        orders: 'Pesanan',
        inventory: 'Stok',
        tables: 'Meja',
        reports: 'Laporan',
        profile: 'Profil',
        settings: 'Pengaturan',
        users: 'Staf',
        'web-content': 'Konten Web',
    },
    EN: {
        overview: 'Dashboard',
        orders: 'Orders',
        inventory: 'Inventory',
        tables: 'Tables',
        reports: 'Reports',
        profile: 'Profile',
        settings: 'Settings',
        users: 'Staff',
        'web-content': 'Web Content',
    },
};

const darkMode = ref(false);
const profileOpen = ref(false);
const mobileSidebarOpen = ref(false);
const languageOpen = ref(false);
const notificationOpen = ref(false);

const displayLabel = (label?: string) => {
    if (!label) return '';

    const parts = label.split('/').map((part) => part.trim());

    return language.value === 'EN' ? parts[0] : parts[1] || parts[0];
};

const navLabel = (item?: { label?: string; key?: string }) => {
    if (!item) return '';

    const key = item.key as keyof (typeof navLabelMap)['IND'];

    return navLabelMap[language.value][key] || displayLabel(item.label);
};

const currentNavItem = computed(
    () =>
        page.props.dashboardNav?.find(
            (item) => item.key === page.props.currentPage,
        ) ||
        page.props.sidebarUtilities?.find(
            (item) => item.key === page.props.currentPage,
        ),
);

const pageTitle = computed(
    () => navLabel(currentNavItem.value) || labels.value.dashboard,
);

const pageTabs = computed(() => [
    ...(page.props.dashboardNav ?? []),
    ...(page.props.sidebarUtilities ?? []),
]);

const orderSidebar = computed(() => page.props.orderSidebar ?? null);

const mainLabel = computed(() => (language.value === 'EN' ? 'Main' : 'Utama'));
const accountLabel = computed(() =>
    language.value === 'EN' ? 'Account' : 'Akun',
);
const summaryLabel = computed(() =>
    language.value === 'EN' ? 'Live Summary' : 'Ringkasan',
);
const ordersLabel = computed(() =>
    language.value === 'EN' ? 'Orders' : 'Pesanan',
);
const revenueLabel = computed(() =>
    language.value === 'EN' ? 'Revenue' : 'Pendapatan',
);
const logoutLabel = computed(() =>
    language.value === 'EN' ? 'Logout' : 'Keluar',
);
const profileLabel = computed(() =>
    language.value === 'EN' ? 'Profile' : 'Profil',
);
const settingsLabel = computed(() =>
    language.value === 'EN' ? 'Settings' : 'Pengaturan',
);
const shopTagline = computed(() => {
    if (language.value === 'IND') {
        return 'Sistem Kasir';
    }

    return page.props.settings?.shop_tagline || 'Coffee POS';
});

const pageDescription = computed(() => {
    const key = (page.props.currentPage ||
        'overview') as keyof (typeof descriptionMap)['IND'];

    return (
        descriptionMap[language.value][key] ||
        descriptionMap[language.value].overview
    );
});

const userInitials = computed(() => {
    const name = page.props.auth.user?.name || 'Admin';

    return name
        .split(' ')
        .filter(Boolean)
        .map((word) => word[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});

const applyTheme = () => {
    document.documentElement.classList.toggle('dark', darkMode.value);
    window.localStorage.setItem(
        'nineties-theme',
        darkMode.value ? 'dark' : 'light',
    );
};

const toggleTheme = () => {
    darkMode.value = !darkMode.value;
    applyTheme();
};

const selectLanguage = (value: 'IND' | 'EN') => {
    setLanguage(value);
    languageOpen.value = false;
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
    <Head :title="pageTitle" />

    <div class="dashboard-ui nineties-dashboard">
        <div
            v-if="mobileSidebarOpen"
            class="inset-0 bg-black/45 lg:hidden fixed z-40"
            @click="mobileSidebarOpen = false"
        ></div>

        <aside class="nineties-sidebar" :class="{ open: mobileSidebarOpen }">
            <div class="nineties-sidebar-brand">
                <Link
                    href="/dashboard/overview"
                    class="gap-3 flex items-center"
                    @click="mobileSidebarOpen = false"
                >
                    <span class="nineties-brand-icon">
                        <span class="material-symbols-outlined"
                            >local_cafe</span
                        >
                    </span>
                    <span>
                        <strong class="nineties-sidebar-title block">{{ page.props.settings?.shop_name || 'Nineties' }}</strong>
                        <small class="nineties-sidebar-subtitle">{{ shopTagline }}</small>
                    </span>
                </Link>
            </div>

            <nav class="nineties-sidebar-nav">
                <div class="nineties-sidebar-label">{{ mainLabel }}</div>
                <Link
                    v-for="item in page.props.dashboardNav || []"
                    :key="item.key"
                    :href="item.href"
                    class="nineties-sidebar-item"
                    :class="{ active: page.props.currentPage === item.key }"
                    @click="mobileSidebarOpen = false"
                >
                    <span class="material-symbols-outlined text-[1.15rem]">{{
                        iconMap[item.key] || 'dashboard'
                    }}</span>
                    <span>{{ navLabel(item) }}</span>
                    <span
                        v-if="
                            item.key === 'orders' &&
                            (page.props.analytics?.activeOrders || 0) > 0
                        "
                        class="px-2 py-0.5 font-black ml-auto rounded-full bg-[#c0392b] text-[0.65rem]"
                    >
                        {{ page.props.analytics?.activeOrders }}
                    </span>
                </Link>

                <div class="mt-5 bg-[#f1ece3] p-4 text-sm rounded-[14px]">
                    <p
                        class="font-bold text-[#7a6a58] text-[0.68rem] tracking-[0.2em] uppercase"
                    >
                        {{ summaryLabel }}
                    </p>
                    <div class="mt-3 gap-3 grid grid-cols-2">
                        <div>
                            <span class="text-xs text-[#9b8a72] block"
                                >{{ ordersLabel }}</span
                            >
                            <strong class="mt-1 text-2xl block text-[#3d2b1f]">{{
                                page.props.analytics?.activeOrders || 0
                            }}</strong>
                        </div>
                        <div>
                            <span class="text-xs text-[#9b8a72] block"
                                >{{ revenueLabel }}</span
                            >
                            <strong class="mt-1 text-sm block text-[#3d2b1f]">
                                Rp
                                {{
                                    Number(
                                        page.props.analytics?.weeklyRevenue ||
                                            0,
                                    ).toLocaleString('id-ID')
                                }}
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="nineties-sidebar-label">{{ accountLabel }}</div>
                <Link
                    v-for="item in page.props.sidebarUtilities || []"
                    :key="item.key"
                    :href="item.href"
                    class="nineties-sidebar-item"
                    :class="{ active: page.props.currentPage === item.key }"
                    @click="mobileSidebarOpen = false"
                >
                    <span class="material-symbols-outlined text-[1.15rem]">{{
                        iconMap[item.key] || 'settings'
                    }}</span>
                    <span>{{ navLabel(item) }}</span>
                </Link>
            </nav>

            <div class="nineties-sidebar-footer">
                <div
                    class="mb-3 gap-3 px-3 py-2 flex items-center rounded-[14px]"
                >
                    <div
                        class="h-9 w-9 bg-white/10 text-[#f7f2e8] text-sm font-black flex items-center justify-center rounded-full"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <strong class="text-sm block truncate text-[#f7f2e8]">{{
                            page.props.auth.user?.name || 'Admin'
                        }}</strong>
                        <span class="text-xs text-[#f7f2e8]/60 block">{{
                            page.props.auth.user?.role || 'staff'
                        }}</span>
                    </div>
                </div>
                <button
                    type="button"
                    class="nineties-pill-button w-full"
                    @click="logout"
                >
                    <span class="material-symbols-outlined text-base"
                        >logout</span
                    >
                    {{ logoutLabel }}
                </button>
            </div>
        </aside>

        <div class="nineties-dashboard-main">
            <header class="nineties-header">
                <button
                    type="button"
                    class="nineties-icon-button lg:hidden"
                    aria-label="Buka menu dashboard"
                    @click="mobileSidebarOpen = true"
                >
                    <span class="material-symbols-outlined text-xl">menu</span>
                </button>

                <div class="nineties-header-title">
                    <h1>{{ pageTitle }}</h1>
                    <p>{{ pageDescription }}</p>
                </div>

                <div class="nineties-header-actions">
                    <Link href="/" class="nineties-pill-button">
                        <span class="material-symbols-outlined text-base"
                            >public</span
                        >
                        <span class="label">{{ labels.visitStore }}</span>
                    </Link>

                    <div class="relative">
                        <button
                            type="button"
                            class="nineties-icon-button"
                            aria-label="Notifikasi"
                            @click="notificationOpen = !notificationOpen"
                        >
                            <span class="material-symbols-outlined text-base"
                                >notifications</span
                            >
                            <span
                                class="right-1.5 top-1.5 h-2 w-2 absolute rounded-full bg-[#c0392b]"
                            ></span>
                        </button>
                        <div
                            v-if="notificationOpen"
                            class="right-0 mt-2 bg-white absolute w-[340px] overflow-hidden rounded-[14px] text-[#2c1a0e]"
                        >
                            <div
                                class="px-5 py-4 flex items-center justify-between bg-[#f1ece3]"
                            >
                                <h4 class="text-sm font-black">
                                    {{ labels.notifications }}
                                </h4>
                                <button
                                    type="button"
                                    class="text-xs font-bold border-0 bg-transparent text-[#a07830]"
                                >
                                    {{ labels.markRead }}
                                </button>
                            </div>
                            <div class="grid">
                                <div
                                    class="gap-3 px-5 py-3 text-sm flex bg-[#f5e6b8]"
                                >
                                    <span
                                        class="mt-1 h-2 w-2 rounded-full bg-[#c9a84c]"
                                    ></span>
                                    <p>
                                        Pesanan baru menunggu konfirmasi
                                        pembayaran.
                                    </p>
                                </div>
                                <div class="gap-3 px-5 py-3 text-sm flex">
                                    <span
                                        class="mt-1 h-2 w-2 rounded-full bg-[#c0392b]"
                                    ></span>
                                    <p>Stok bahan mendekati batas minimum.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nineties-language">
                        <button
                            type="button"
                            class="nineties-lang-button"
                            @click="languageOpen = !languageOpen"
                        >
                            <span class="material-symbols-outlined text-base"
                                >language</span
                            >
                            {{ language }}
                            <span class="material-symbols-outlined text-base"
                                >expand_more</span
                            >
                        </button>
                        <div v-if="languageOpen" class="nineties-lang-menu">
                            <button
                                type="button"
                                :class="{ active: language === 'IND' }"
                                @click="selectLanguage('IND')"
                            >
                                Indonesia
                                <span v-if="language === 'IND'" class="material-symbols-outlined text-base">check</span>
                            </button>
                            <button
                                type="button"
                                :class="{ active: language === 'EN' }"
                                @click="selectLanguage('EN')"
                            >
                                English
                                <span v-if="language === 'EN'" class="material-symbols-outlined text-base">check</span>
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="nineties-icon-button"
                        aria-label="Ganti tema"
                        @click="toggleTheme"
                    >
                        <span class="material-symbols-outlined text-base">{{
                            darkMode ? 'light_mode' : 'dark_mode'
                        }}</span>
                    </button>

                    <div class="relative">
                        <button
                            type="button"
                            class="nineties-pill-button"
                            @click="profileOpen = !profileOpen"
                        >
                            <span
                                class="h-7 w-7 bg-white/20 text-xs font-black flex items-center justify-center rounded-full"
                                >{{ userInitials }}</span
                            >
                            <span class="label">{{
                                page.props.auth.user?.name || 'Admin'
                            }}</span>
                        </button>

                        <div
                            v-if="profileOpen"
                            class="right-0 mt-2 w-60 bg-white p-2 absolute rounded-[14px] text-[#2c1a0e]"
                        >
                            <Link
                                href="/dashboard/profile"
                                class="gap-3 px-3 py-3 text-sm flex items-center rounded-[10px] hover:bg-[#fdf6e3]"
                                @click="profileOpen = false"
                            >
                                <span
                                    class="material-symbols-outlined text-base"
                                    >person</span
                                >
                                {{ profileLabel }}
                            </Link>
                            <Link
                                href="/dashboard/settings"
                                class="gap-3 px-3 py-3 text-sm flex items-center rounded-[10px] hover:bg-[#fdf6e3]"
                                @click="profileOpen = false"
                            >
                                <span
                                    class="material-symbols-outlined text-base"
                                    >settings</span
                                >
                                {{ settingsLabel }}
                            </Link>
                            <button
                                type="button"
                                class="gap-3 bg-white px-3 py-3 text-sm flex w-full items-center rounded-[10px] border-0 text-left hover:bg-[#fdf6e3]"
                                @click="logout"
                            >
                                <span
                                    class="material-symbols-outlined text-base"
                                    >logout</span
                                >
                                {{ logoutLabel }}
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="nineties-dashboard-body">
                <nav
                    class="mb-5 flex gap-2 overflow-x-auto rounded-[18px] border border-[#eadfca] bg-[#fffaf2] p-2 shadow-sm"
                    aria-label="Tab dashboard"
                >
                    <Link
                        v-for="item in pageTabs"
                        :key="item.key"
                        :href="item.href"
                        :class="[
                            'inline-flex min-h-10 shrink-0 items-center gap-2 rounded-[14px] px-4 text-sm font-black transition',
                            page.props.currentPage === item.key
                                ? 'bg-[#3d2b1f] text-[#f7f2e8]'
                                : 'text-[#6b4226] hover:bg-white',
                        ]"
                    >
                        <span class="material-symbols-outlined text-base">
                            {{ iconMap[item.key] || 'dashboard' }}
                        </span>
                        {{ navLabel(item) }}
                    </Link>
                </nav>

                <div
                    v-if="page.props.flash.success"
                    class="mb-4 px-4 py-3 text-sm font-semibold rounded-[14px] bg-[#d1fae5] text-[#065f46]"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.flash.error"
                    class="mb-4 px-4 py-3 text-sm font-semibold rounded-[14px] bg-[#fee2e2] text-[#991b1b]"
                >
                    {{ page.props.flash.error }}
                </div>

                <div
                    v-if="orderSidebar"
                    class="grid gap-6 2xl:grid-cols-[minmax(0,1fr)_390px]"
                >
                    <section class="min-w-0">
                        <slot />
                    </section>

                    <OrderSidebar
                        :tables="orderSidebar.tables"
                        :menu-items="orderSidebar.menuItems"
                        :online-orders="orderSidebar.onlineOrders"
                        mode="dashboard"
                    />
                </div>
                <slot v-else />
            </main>
        </div>
    </div>
</template>
