<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useLanguage } from '@/composables/useLanguage';
import { computed, ref, watch } from 'vue';

type DiningTable = {
    id?: number;
    name: string;
    public_token?: string;
    floor?: number;
    capacity?: number;
};

type MenuOption = {
    id: number | string;
    name: string;
    price: number | string;
    category?: string;
};

type OnlineOrder = {
    id?: number | string;
    public_id?: string;
    customer?: string;
    payment_status?: string;
    total_amount?: number | string;
    service_type?: string;
    table_name?: string;
    floor?: number | string | null;
    quantity?: number;
};

const props = withDefaults(
    defineProps<{
        tables: DiningTable[];
        menuItems: MenuOption[];
        onlineOrders?: OnlineOrder[];
        selectedMenuId?: number | string | null;
        mode?: 'public' | 'dashboard';
        showOnlineOrders?: boolean;
    }>(),
    {
        selectedMenuId: null,
        mode: 'public',
        showOnlineOrders: true,
        onlineOrders: () => [],
    },
);

const emit = defineEmits<{
    'update:selectedMenuId': [value: number | string | null];
}>();

const { language } = useLanguage();

const copy = computed(() =>
    language.value === 'EN'
        ? {
              title: 'Order Sidebar',
              subtitle: 'Set floor, table, quantity, and payment before checkout.',
              floor: 'Floor',
              table: 'Table Number',
              menu: 'Menu',
              quantity: 'Order Quantity',
              payment: 'Payment',
              customer: 'Customer Name',
              total: 'Estimated Total',
              continue: 'Continue from Table QR',
              create: 'Create Order',
              creating: 'Creating...',
              emptyTable: 'No active tables on this floor.',
              emptyMenu: 'No menu available.',
              online: 'Incoming Orders',
              emptyOrder: 'No incoming orders yet.',
              cash: 'Cash',
              qris: 'QRIS',
              selectedTable: 'Selected table',
              selectedMenu: 'Selected menu',
              customerPlaceholder: 'Customer name',
          }
        : {
              title: 'Sidebar Pesanan',
              subtitle: 'Atur lantai, nomor meja, jumlah pesanan, dan pembayaran.',
              floor: 'Lantai',
              table: 'Nomor Meja',
              menu: 'Menu',
              quantity: 'Jumlah Pesanan',
              payment: 'Pembayaran',
              customer: 'Nama Pelanggan',
              total: 'Total Sementara',
              continue: 'Lanjut lewat QR Meja',
              create: 'Buat Pesanan',
              creating: 'Membuat...',
              emptyTable: 'Belum ada meja aktif di lantai ini.',
              emptyMenu: 'Belum ada menu tersedia.',
              online: 'Pesanan Masuk',
              emptyOrder: 'Belum ada pesanan masuk.',
              cash: 'Tunai',
              qris: 'QRIS',
              selectedTable: 'Meja terpilih',
              selectedMenu: 'Menu terpilih',
              customerPlaceholder: 'Nama pelanggan',
          },
);

const floors = computed(() => {
    const unique = new Set<number>();

    props.tables.forEach((table) => unique.add(Number(table.floor || 1)));

    return Array.from(unique).sort((first, second) => first - second);
});

const activeFloor = ref(floors.value[0] ?? 1);
const selectedTableName = ref('');
const selectedMenu = ref<number | string | null>(props.selectedMenuId);
const quantity = ref(1);
const paymentMethod = ref<'qris' | 'cash'>('qris');
const customerName = ref('');
const creatingOrder = ref(false);

const tablesOnFloor = computed(() =>
    props.tables.filter((table) => Number(table.floor || 1) === activeFloor.value),
);

const currentTable = computed(
    () =>
        tablesOnFloor.value.find((table) => table.name === selectedTableName.value) ??
        tablesOnFloor.value[0] ??
        null,
);

const currentMenu = computed(
    () =>
        props.menuItems.find((item) => String(item.id) === String(selectedMenu.value)) ??
        props.menuItems[0] ??
        null,
);

const estimatedTotal = computed(
    () => Number(currentMenu.value?.price ?? 0) * quantity.value,
);

const qrHref = computed(() =>
    currentTable.value?.public_token ? `/qr/${currentTable.value.public_token}` : '#',
);

const currency = (value: number | string | null | undefined) =>
    `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

const paymentLabel = (value?: string) => {
    const labels: Record<string, { IND: string; EN: string }> = {
        pending: { IND: 'Menunggu', EN: 'Pending' },
        settlement: { IND: 'Lunas', EN: 'Paid' },
        paid: { IND: 'Lunas', EN: 'Paid' },
        failed: { IND: 'Gagal', EN: 'Failed' },
        expired: { IND: 'Kadaluarsa', EN: 'Expired' },
        refunded: { IND: 'Dikembalikan', EN: 'Refunded' },
    };

    return labels[value ?? '']?.[language.value] ?? value ?? '-';
};

const serviceLabel = (value?: string) => {
    if (language.value === 'EN') {
        return value === 'dine_in' ? 'Dine-in' : value || 'Dine-in';
    }

    return value === 'dine_in' ? 'Makan di tempat' : 'Makan di tempat';
};

const changeQuantity = (amount: number) => {
    quantity.value = Math.max(1, quantity.value + amount);
};

const createDashboardOrder = () => {
    if (props.mode !== 'dashboard' || !currentMenu.value) return;

    creatingOrder.value = true;

    router.post(
        '/management/orders',
        {
            customer_name: customerName.value,
            table_id: currentTable.value?.id ?? null,
            menu_item_id: currentMenu.value.id,
            quantity: quantity.value,
            payment_channel: paymentMethod.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                quantity.value = 1;
                customerName.value = '';
            },
            onFinish: () => {
                creatingOrder.value = false;
            },
        },
    );
};

watch(
    floors,
    (items) => {
        if (!items.includes(activeFloor.value)) {
            activeFloor.value = items[0] ?? 1;
        }
    },
    { immediate: true },
);

watch(
    tablesOnFloor,
    (items) => {
        if (!items.some((table) => table.name === selectedTableName.value)) {
            selectedTableName.value = items[0]?.name ?? '';
        }
    },
    { immediate: true },
);

watch(
    () => props.selectedMenuId,
    (value) => {
        if (value !== null && value !== undefined) {
            selectedMenu.value = value;
        }
    },
);

watch(selectedMenu, (value) => emit('update:selectedMenuId', value));

watch(quantity, (value) => {
    if (!Number.isFinite(value) || value < 1) {
        quantity.value = 1;
    }
});
</script>

<template>
    <aside class="grid h-fit gap-5 xl:sticky xl:top-24">
        <section class="rounded-[24px] border border-[#eadfca] bg-white p-5 shadow-[0_22px_60px_rgba(61,43,31,0.09)]">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#a07830]">
                        {{ copy.title }}
                    </p>
                    <p class="mt-2 text-sm leading-6 text-[#7a6a58]">
                        {{ copy.subtitle }}
                    </p>
                </div>
                <span class="material-symbols-outlined rounded-2xl bg-[#3d2b1f] p-3 text-[#f7f2e8]">
                    shopping_bag
                </span>
            </div>

            <div class="grid gap-5">
                <div>
                    <p class="mb-2 text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.floor }}
                    </p>
                    <div class="grid grid-cols-3 gap-2 rounded-2xl bg-[#f7f2e8] p-1">
                        <button
                            v-for="floor in floors"
                            :key="floor"
                            type="button"
                            :class="[
                                'rounded-xl px-3 py-2 text-xs font-black transition',
                                activeFloor === floor
                                    ? 'bg-[#3d2b1f] text-white shadow-sm'
                                    : 'text-[#6b4226] hover:bg-white',
                            ]"
                            @click="activeFloor = floor"
                        >
                            {{ copy.floor }} {{ floor }}
                        </button>
                    </div>
                </div>

                <div>
                    <p class="mb-2 text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.table }}
                    </p>
                    <div
                        v-if="tablesOnFloor.length"
                        class="grid max-h-32 grid-cols-3 gap-2 overflow-y-auto pr-1"
                    >
                        <button
                            v-for="table in tablesOnFloor"
                            :key="table.name"
                            type="button"
                            :class="[
                                'rounded-xl border px-3 py-2 text-xs font-black uppercase tracking-[0.08em] transition',
                                selectedTableName === table.name
                                    ? 'border-[#c9a84c] bg-[#f5e6b8] text-[#3d2b1f]'
                                    : 'border-[#eadfca] bg-[#fffaf2] text-[#6b4226] hover:border-[#c9a84c]',
                            ]"
                            @click="selectedTableName = table.name"
                        >
                            {{ table.name }}
                        </button>
                    </div>
                    <p v-else class="rounded-2xl bg-[#fffaf2] px-4 py-3 text-sm text-[#9b8a72]">
                        {{ copy.emptyTable }}
                    </p>
                </div>

                <label class="grid gap-2">
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.menu }}
                    </span>
                    <select
                        v-if="menuItems.length"
                        v-model="selectedMenu"
                        class="min-h-11 rounded-2xl border border-[#eadfca] bg-[#fffaf2] px-4 text-sm font-bold text-[#3d2b1f] outline-none transition focus:border-[#c9a84c]"
                    >
                        <option
                            v-for="item in menuItems"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }} - {{ currency(item.price) }}
                        </option>
                    </select>
                    <span v-else class="rounded-2xl bg-[#fffaf2] px-4 py-3 text-sm text-[#9b8a72]">
                        {{ copy.emptyMenu }}
                    </span>
                </label>

                <label v-if="mode === 'dashboard'" class="grid gap-2">
                    <span class="text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.customer }}
                    </span>
                    <input
                        v-model="customerName"
                        type="text"
                        class="min-h-11 rounded-2xl border border-[#eadfca] bg-[#fffaf2] px-4 text-sm font-bold text-[#3d2b1f] outline-none transition focus:border-[#c9a84c]"
                        :placeholder="copy.customerPlaceholder"
                    />
                </label>

                <div>
                    <p class="mb-2 text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.quantity }}
                    </p>
                    <div class="grid grid-cols-[44px_1fr_44px] overflow-hidden rounded-2xl border border-[#eadfca] bg-[#fffaf2]">
                        <button
                            type="button"
                            class="grid min-h-11 place-items-center text-[#6b4226] transition hover:bg-[#f5e6b8]"
                            @click="changeQuantity(-1)"
                        >
                            <span class="material-symbols-outlined text-base">remove</span>
                        </button>
                        <input
                            v-model.number="quantity"
                            type="number"
                            min="1"
                            class="min-h-11 border-x border-[#eadfca] bg-white text-center text-sm font-black text-[#3d2b1f] outline-none"
                        />
                        <button
                            type="button"
                            class="grid min-h-11 place-items-center text-[#6b4226] transition hover:bg-[#f5e6b8]"
                            @click="changeQuantity(1)"
                        >
                            <span class="material-symbols-outlined text-base">add</span>
                        </button>
                    </div>
                </div>

                <div>
                    <p class="mb-2 text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                        {{ copy.payment }}
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            :class="[
                                'rounded-2xl border px-4 py-3 text-sm font-black transition',
                                paymentMethod === 'qris'
                                    ? 'border-[#c9a84c] bg-[#3d2b1f] text-white'
                                    : 'border-[#eadfca] bg-[#fffaf2] text-[#6b4226]',
                            ]"
                            @click="paymentMethod = 'qris'"
                        >
                            {{ copy.qris }}
                        </button>
                        <button
                            type="button"
                            :class="[
                                'rounded-2xl border px-4 py-3 text-sm font-black transition',
                                paymentMethod === 'cash'
                                    ? 'border-[#c9a84c] bg-[#3d2b1f] text-white'
                                    : 'border-[#eadfca] bg-[#fffaf2] text-[#6b4226]',
                            ]"
                            @click="paymentMethod = 'cash'"
                        >
                            {{ copy.cash }}
                        </button>
                    </div>
                </div>

                <div class="rounded-[20px] border border-[#eadfca] bg-[#fffaf2] p-4">
                    <div class="grid gap-3 text-sm text-[#6b4226]">
                        <div class="flex items-center justify-between gap-3">
                            <span>{{ copy.selectedTable }}</span>
                            <strong class="text-[#3d2b1f]">
                                {{ currentTable?.name || '-' }}
                            </strong>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>{{ copy.selectedMenu }}</span>
                            <strong class="max-w-[170px] truncate text-[#3d2b1f]">
                                {{ currentMenu?.name || '-' }}
                            </strong>
                        </div>
                    </div>
                    <div class="mt-4 border-t border-[#eadfca] pt-4">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#9b8a72]">
                            {{ copy.total }}
                        </p>
                        <p class="mt-1 text-2xl font-black text-[#3d2b1f]">
                            {{ currency(estimatedTotal) }}
                        </p>
                    </div>
                </div>

                <a
                    v-if="mode === 'public'"
                    :href="qrHref"
                    :class="[
                        'inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl px-4 text-sm font-black transition',
                        currentTable?.public_token
                            ? 'bg-[#3d2b1f] text-[#f7f2e8] hover:bg-[#1f1a17]'
                            : 'pointer-events-none bg-[#eadfca] text-[#9b8a72]',
                    ]"
                >
                    <span class="material-symbols-outlined text-base">qr_code_2</span>
                    {{ copy.continue }}
                </a>
                <button
                    v-else
                    type="button"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl bg-[#3d2b1f] px-4 text-sm font-black text-[#f7f2e8] transition hover:bg-[#1f1a17] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="creatingOrder || !currentMenu"
                    @click="createDashboardOrder"
                >
                    <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                    {{ creatingOrder ? copy.creating : copy.create }}
                </button>
            </div>
        </section>

        <section
            v-if="showOnlineOrders"
            class="rounded-[24px] border border-[#eadfca] bg-white p-5 shadow-[0_22px_60px_rgba(61,43,31,0.08)]"
        >
            <h3 class="font-serif text-xl font-black text-[#3d2b1f]">
                {{ copy.online }}
            </h3>

            <div v-if="onlineOrders.length" class="mt-4 grid max-h-[440px] gap-3 overflow-y-auto pr-1">
                <article
                    v-for="order in onlineOrders"
                    :key="order.id ?? order.public_id"
                    class="rounded-[18px] bg-[#f7f2e8] p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <strong class="block truncate text-sm text-[#3d2b1f]">
                                {{ order.public_id }}
                            </strong>
                            <p class="mt-1 truncate text-xs text-[#7a6a58]">
                                {{ order.customer }}
                            </p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-[10px] font-black uppercase tracking-[0.08em] text-[#6b4226]">
                            {{ paymentLabel(order.payment_status) }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="font-black text-[#3d2b1f]">
                                {{ currency(order.total_amount) }}
                            </p>
                            <p class="mt-1 text-[11px] font-semibold text-[#7a6a58]">
                                {{ copy.floor }} {{ order.floor || '-' }} / {{ order.table_name || '-' }} / {{ copy.quantity }} {{ order.quantity || 0 }}
                            </p>
                        </div>
                        <p class="text-right text-[10px] font-black uppercase tracking-[0.14em] text-[#9b8a72]">
                            {{ serviceLabel(order.service_type) }}
                        </p>
                    </div>
                </article>
            </div>
            <p v-else class="mt-4 rounded-2xl bg-[#fffaf2] px-4 py-5 text-center text-sm text-[#9b8a72]">
                {{ copy.emptyOrder }}
            </p>
        </section>
    </aside>
</template>
