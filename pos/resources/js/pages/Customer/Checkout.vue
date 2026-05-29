<script setup lang="ts">
import { clearCart, loadCart } from '@/composables/useCustomerCart';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: CustomerLayout });

const props = defineProps<{
    table: any;
    paymentChannels: Array<{ value: string; label: string }>;
}>();

const cartItems = loadCart(props.table.public_token);

const form = useForm({
    customer: {
        name: '',
        email: '',
        phone: '',
    },
    payment_channel: 'qris',
    notes: '',
    items: cartItems.map((item) => ({
        menu_item_id: item.menu_item_id,
        quantity: item.quantity,
        notes: item.notes,
    })),
});

const grandTotal = computed(() =>
    cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0),
);

const submit = () =>
    form.post(`/qr/${props.table.public_token}/orders`, {
        onSuccess: () => clearCart(props.table.public_token),
    });
</script>

<template>
    <div class="customer-page-grid">
        <section class="customer-main-card !p-6">
            <h2 class="mb-6 font-serif text-2xl font-bold text-brand-espresso">Ringkasan Pesanan</h2>

            <div class="list-block space-y-3">
                <div v-for="item in cartItems" :key="item.menu_item_id" class="flex items-center justify-between border-b border-brand-gold/10 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-brand-gold">x{{ item.quantity }}</span>
                        <p class="text-sm font-bold text-brand-espresso">{{ item.name }}</p>
                    </div>
                    <span class="text-sm font-bold text-brand-espresso">Rp {{ Number(item.price * item.quantity).toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <div class="mt-6 space-y-2">
                <div class="flex justify-between text-xs text-brand-bronze/60">
                    <span>Subtotal + Pajak (11%)</span>
                    <span>Rp {{ Number(grandTotal * 1.11).toLocaleString('id-ID') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-brand-espresso">
                    <span class="font-serif">Total Akhir</span>
                    <span>Rp {{ Number(grandTotal * 1.11).toLocaleString('id-ID') }}</span>
                </div>
            </div>
        </section>

        <aside class="customer-side-card !p-6">
            <h3 class="mb-6 font-serif text-xl font-bold text-brand-espresso">Data & Pembayaran</h3>

            <form class="form-grid space-y-4" @submit.prevent="submit">
                <div class="grid gap-3">
                    <input v-model="form.customer.name" type="text" placeholder="Nama Lengkap" class="!py-2.5 !text-sm" required />
                    <input v-model="form.customer.phone" type="text" placeholder="WhatsApp" class="!py-2.5 !text-sm" required />
                </div>

                <div class="space-y-2">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-brand-gold">Metode Pembayaran</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div 
                            v-for="channel in paymentChannels" 
                            :key="channel.value"
                            class="payment-method-card !p-3"
                            :class="{ 'active': form.payment_channel === channel.value }"
                            @click="form.payment_channel = channel.value"
                        >
                            <div class="text-brand-gold">
                                <span class="material-symbols-outlined">{{ channel.value === 'qris' ? 'qr_code_2' : 'payments' }}</span>
                            </div>
                            <p class="text-[10px] font-bold text-brand-espresso">{{ channel.label }}</p>
                        </div>
                    </div>
                </div>

                <textarea v-model="form.notes" rows="2" placeholder="Catatan (opsional)..." class="!py-2.5 !text-sm"></textarea>

                <div class="pt-2">
                    <button class="primary-button w-full rounded-xl shadow-md active:scale-95 !min-h-[42px] text-sm" type="submit" :disabled="form.processing || form.items.length === 0">
                        {{ form.processing ? 'Memproses...' : 'Konfirmasi Pesanan' }}
                    </button>
                    <Link class="secondary-button mt-3 w-full rounded-xl !min-h-[40px] text-xs" :href="`/qr/${table.public_token}`">
                        <span class="material-symbols-outlined text-xs">arrow_back</span> Tambah Menu Lagi
                    </Link>
                </div>
            </form>
        </aside>
    </div>
</template>
