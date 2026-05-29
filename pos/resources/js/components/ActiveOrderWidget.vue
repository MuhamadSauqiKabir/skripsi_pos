<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<{
    activeOrder: any;
}>();

const order = computed(() => page.props.activeOrder);

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'awaiting_payment': 'Menunggu Pembayaran',
        'paid': 'Pembayaran Berhasil',
        'brewing': 'Sedang Diracik',
        'ready': 'Siap Dinikmati',
    };
    return labels[status] || status;
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'awaiting_payment': return 'payments';
        case 'paid': return 'check_circle';
        case 'brewing': return 'coffee';
        case 'ready': return 'celebration';
        default: return 'info';
    }
};
</script>

<template>
    <div v-if="order" class="fixed bottom-6 left-1/2 z-50 w-[90%] max-w-lg -translate-x-1/2 transform">
        <div class="flex items-center justify-between overflow-hidden rounded-2xl border border-brand-gold/30 bg-brand-espresso/95 p-4 text-white shadow-2xl backdrop-blur-md transition-all duration-500">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 animate-nineties-pulse items-center justify-center rounded-full bg-brand-gold text-brand-espresso">
                    <span class="material-symbols-outlined text-xl">{{ getStatusIcon(order.status) }}</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-brand-gold/80">Pesanan Aktif</p>
                    <p class="text-xs font-medium">
                        {{ getStatusLabel(order.status) }} 
                        <span class="ml-1 text-brand-gold opacity-60">#{{ order.public_id }}</span>
                    </p>
                </div>
            </div>
            <Link 
                :href="`/tracking/${order.public_id}`" 
                class="rounded-xl bg-brand-gold px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-brand-espresso transition hover:brightness-110 active:scale-95"
            >
                Detail
            </Link>
        </div>
    </div>
</template>

<style scoped>
@keyframes nineties-pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(201, 168, 76, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(201, 168, 76, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(201, 168, 76, 0); }
}
.animate-nineties-pulse {
    animation: nineties-pulse 2s infinite;
}
</style>
