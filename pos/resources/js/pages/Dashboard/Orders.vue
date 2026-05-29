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
    router.patch(
        `/management/orders/${id}/status`,
        { status },
        { preserveScroll: true },
    );
};

const statusText = (value: string) => {
    const map: Record<string, string> = {
        pending: 'Menunggu',
        awaiting_payment: 'Menunggu Pembayaran',
        paid: 'Dibayar',
        brewing: 'Diracik',
        ready: 'Siap Diambil',
        completed: 'Selesai',
        cancelled: 'Dibatalkan',
        expired: 'Kadaluarsa',
    };

    return map[value] ?? value;
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
    <div class="space-y-5">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div
                class="gap-4 md:flex-row md:items-end md:justify-between flex flex-col"
            >
                <div>
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
                    >
                        Meja Pesanan
                    </p>
                    <h2 class="mt-2 font-serif text-2xl font-bold">
                        Pesanan
                    </h2>
                    <p
                        class="mt-2 max-w-2xl text-sm leading-6 text-[#6d6255] dark:text-[#c8bdaa]"
                    >
                        Kelola transaksi toko dan ubah status pesanan tanpa
                        berpindah halaman.
                    </p>
                </div>
                <div
                    class="gap-3 md:w-[420px] md:flex-row md:items-end flex flex-col"
                >
                    <div class="min-w-0 flex-1">
                        <InputFields
                            v-model="query"
                            label="Cari"
                            placeholder="Cari pesanan, pelanggan, meja..."
                        />
                    </div>
                    <button
                        type="button"
                        class="min-h-11 gap-2 px-5 text-sm font-bold inline-flex items-center justify-center rounded-full bg-[#3d2b1f] text-[#f7f1e8] hover:bg-[#1f2926]"
                        @click="modalOpen = true"
                    >
                        <span class="material-symbols-outlined text-base"
                            >add</span
                        >
                        Tambah
                    </button>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead
                        class="text-xs font-bold tracking-[0.16em] text-[#9b8a72] uppercase"
                    >
                        <tr>
                            <th class="px-4 py-2">Pesanan</th>
                            <th class="px-4 py-2">Pelanggan</th>
                            <th class="px-4 py-2">Meja</th>
                            <th class="px-4 py-2">Total</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="order in filteredOrders"
                            :key="order.id"
                            class="bg-[#f1ece3] dark:bg-[#28322e]"
                        >
                            <td class="rounded-l-lg px-4 py-4 font-semibold">
                                {{ order.public_id }}
                            </td>
                            <td
                                class="px-4 py-4 text-[#6d6255] dark:text-[#c8bdaa]"
                            >
                                {{ order.customer?.name || 'Pelanggan' }}
                            </td>
                            <td
                                class="px-4 py-4 text-[#6d6255] dark:text-[#c8bdaa]"
                            >
                                {{ order.dining_table?.name || 'Tanpa meja' }}
                            </td>
                            <td class="px-4 py-4 font-semibold">
                                Rp
                                {{
                                    Number(order.total_amount).toLocaleString(
                                        'id-ID',
                                    )
                                }}
                            </td>
                            <td class="rounded-r-lg px-4 py-4">
                                <select
                                    class="min-h-10 rounded-lg px-3 text-sm bg-[#fffaf2] dark:bg-[#1d2521]"
                                    @change="
                                        updateOrderStatus(
                                            order.id,
                                            ($event.target as HTMLSelectElement)
                                                .value,
                                        )
                                    "
                                >
                                    <option
                                        v-for="option in statusOptions"
                                        :key="option.value"
                                        :selected="
                                            option.value === order.status
                                        "
                                        :value="option.value"
                                    >
                                        {{ statusText(option.value) }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <FormModal
            :open="modalOpen"
            title="Tambah Pesanan"
            subtitle="Form pesanan cepat untuk kasir dan barista"
            @close="modalOpen = false"
        >
            <div class="gap-4 grid">
                <InputFields
                    v-model="orderForm.customer_name"
                    label="Nama Pelanggan"
                    placeholder="Budi"
                />
                <InputFields
                    v-model="orderForm.table_name"
                    label="Meja"
                    placeholder="A1"
                />
                <InputFields
                    v-model="orderForm.total"
                    label="Total Pembayaran"
                    type="number"
                    placeholder="50000"
                />
            </div>
            <FormModalActions>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                    @click="modalOpen = false"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                    @click="modalOpen = false"
                >
                    Simpan
                </button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
