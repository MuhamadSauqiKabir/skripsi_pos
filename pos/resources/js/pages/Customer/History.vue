<script setup lang="ts">
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: CustomerLayout });

const props = defineProps<{
    orders: Array<any>;
}>();

const getStatusClass = (status: string) => {
    switch (status) {
        case 'completed': return 'bg-green-100 text-green-700';
        case 'paid': return 'bg-blue-100 text-blue-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        default: return 'bg-brand-gold/10 text-brand-gold';
    }
};
</script>

<template>
    <div class="mx-auto max-w-2xl px-4 py-8">
        <header class="mb-10">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-gold">Your Journey</p>
            <h2 class="mt-2 font-serif text-4xl font-bold text-brand-espresso">Riwayat Pesanan</h2>
            <p class="mt-2 text-sm text-brand-bronze/60">Terima kasih telah menjadi bagian dari Nineties Coffee.</p>
        </header>

        <div v-if="orders.length === 0" class="rounded-3xl border border-dashed border-brand-gold/20 py-20 text-center">
            <span class="material-symbols-outlined text-6xl text-brand-gold/20">history_edu</span>
            <p class="mt-4 text-brand-bronze/50 italic">Anda belum memiliki riwayat pesanan.</p>
            <Link href="/pesan-menu" class="primary-button mt-6 inline-block rounded-xl px-8">Mulai Pesan</Link>
        </div>

        <div v-else class="space-y-6">
            <div v-for="order in orders" :key="order.id" class="overflow-hidden rounded-3xl border border-brand-gold/10 bg-white shadow-soft transition hover:shadow-lg">
                <div class="flex items-center justify-between border-b border-brand-gold/5 bg-brand-cream/10 p-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-brand-bronze/50">{{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                        <h3 class="font-bold text-brand-espresso">{{ order.public_id }}</h3>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase" :class="getStatusClass(order.status)">
                        {{ order.status }}
                    </span>
                </div>
                
                <div class="p-5">
                    <div class="space-y-2">
                        <div v-for="item in order.items.slice(0, 2)" :key="item.id" class="flex justify-between text-sm">
                            <span class="text-brand-espresso">{{ item.menu_name_snapshot }} <span class="text-brand-bronze/50">x{{ item.quantity }}</span></span>
                            <span class="font-medium text-brand-espresso">Rp {{ Number(item.line_total).toLocaleString('id-ID') }}</span>
                        </div>
                        <p v-if="order.items.length > 2" class="text-[10px] italic text-brand-bronze/50">+ {{ order.items.length - 2 }} item lainnya</p>
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-brand-gold/5 pt-4">
                        <div>
                            <p class="text-[10px] text-brand-bronze/60 uppercase font-bold">Total Pembayaran</p>
                            <p class="text-lg font-bold text-brand-espresso">Rp {{ Number(order.total_amount).toLocaleString('id-ID') }}</p>
                        </div>
                        <Link :href="`/tracking/${order.public_id}`" class="rounded-xl border border-brand-gold/20 px-4 py-2 text-xs font-bold text-brand-gold hover:bg-brand-gold hover:text-white transition">
                            Detail Pesanan
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <Link href="/pesan-menu" class="text-sm font-bold text-brand-gold underline underline-offset-4 hover:text-brand-coffee transition">
                ← Kembali ke Menu Utama
            </Link>
        </div>
    </div>
</template>
