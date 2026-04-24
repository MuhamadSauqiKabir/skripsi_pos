<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    recentOrders: { data: Array<any> };
    onlineOrders: Array<any>;
    shippingOptions: Array<any>;
    statusOptions: Array<{ value: string; label: string }>;
}>();

const query = ref('');
const modalOpen = ref(false);
const orderForm = useForm({
    customer_name: '',
    table_name: '',
    total: '',
});

const updateOrderStatus = (id: number, status: string) => {
    router.patch(`/management/orders/${id}/status`, { status }, { preserveScroll: true });
};

const filteredOrders = computed(() => {
    if (!query.value) return props.recentOrders.data;
    const term = query.value.toLowerCase();
    return props.recentOrders.data.filter((order) =>
        [order.public_id, order.customer?.name, order.dining_table?.name]
            .filter(Boolean)
            .some((value) => String(value).toLowerCase().includes(term)),
    );
});
</script>

<template>
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_360px]">
        <section class="rounded-3xl bg-white dark:bg-slate-900">
            <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="font-serif text-2xl font-bold">Orders / Pesanan</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola transaksi toko dan update status pesanan secara realtime.</p>
                </div>
                <div class="flex flex-col gap-3 md:flex-row">
                    <button type="button" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="modalOpen = true">
                        Add / Tambah
                    </button>
                    <div class="w-full md:w-72">
                        <InputFields v-model="query" label="Search / Cari" placeholder="Cari order, customer, table..." />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto px-6 pb-6">
                <table class="min-w-full text-left">
                    <thead class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
                        <tr>
                            <th class="py-3 pr-4">Order</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Table</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in filteredOrders" :key="order.id">
                            <td class="py-4 pr-4 font-semibold">{{ order.public_id }}</td>
                            <td class="px-4 py-4 text-slate-500 dark:text-slate-400">{{ order.customer?.name || 'Guest' }}</td>
                            <td class="px-4 py-4 text-slate-500 dark:text-slate-400">{{ order.dining_table?.name || 'Take Away' }}</td>
                            <td class="px-4 py-4">Rp {{ Number(order.total_amount).toLocaleString('id-ID') }}</td>
                            <td class="px-4 py-4">
                                <select class="rounded-2xl border-slate-200 bg-slate-50 text-sm dark:border-slate-700 dark:bg-slate-800" @change="updateOrderStatus(order.id, ($event.target as HTMLSelectElement).value)">
                                    <option v-for="option in statusOptions" :key="option.value" :selected="option.value === order.status" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <aside id="online-orders" class="space-y-6">
            <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-xl font-bold">Online Orders / Pesanan Online</h3>
                    <span class="text-xs uppercase tracking-[0.18em] text-slate-400">Xendit + RajaOngkir</span>
                </div>
                <div class="mt-5 space-y-3">
                    <article v-for="order in onlineOrders" :key="order.id" class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <strong class="block">{{ order.public_id }}</strong>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ order.customer }}</p>
                        <p class="mt-2 text-xs uppercase tracking-[0.16em] text-slate-400">{{ order.payment_vendor }} / {{ order.shipping_vendor }}</p>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span>{{ order.payment_status }}</span>
                            <strong>Rp {{ Number(order.total_amount).toLocaleString('id-ID') }}</strong>
                        </div>
                        <p class="mt-2 text-xs text-slate-400">{{ order.shipping_status }}</p>
                    </article>
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
                <h3 class="font-serif text-xl font-bold">RajaOngkir Shipping / Pengiriman</h3>
                <div class="mt-5 space-y-3">
                    <div v-for="option in shippingOptions" :key="`${option.courier}-${option.service}`" class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <div class="flex items-center justify-between">
                            <strong>{{ option.courier }} {{ option.service }}</strong>
                            <span>Rp {{ Number(option.cost).toLocaleString('id-ID') }}</span>
                        </div>
                        <p class="mt-2 text-xs uppercase tracking-[0.16em] text-slate-400">{{ option.etd }}</p>
                    </div>
                </div>
            </section>
        </aside>

        <FormModal :open="modalOpen" title="Add Order / Tambah Pesanan" subtitle="Form order cepat untuk kasir dan barista" @close="modalOpen = false">
            <div class="grid gap-4">
                <InputFields v-model="orderForm.customer_name" label="Customer Name / Nama Pelanggan" placeholder="Budi" />
                <InputFields v-model="orderForm.table_name" label="Table / Meja" placeholder="A1" />
                <InputFields v-model="orderForm.total" label="Total / Total Pembayaran" type="number" placeholder="50000" />
            </div>
            <FormModalActions>
                <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="modalOpen = false">Cancel / Batal</button>
                <button type="button" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="modalOpen = false">Save / Simpan</button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
