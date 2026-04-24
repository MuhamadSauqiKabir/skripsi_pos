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
        <section class="customer-main-card">
            <p class="eyebrow">Checkout Page</p>
            <h2>Konfirmasi pesanan {{ table.name }}</h2>

            <div class="list-block">
                <div v-for="item in cartItems" :key="item.menu_item_id" class="list-row">
                    <div>
                        <strong>{{ item.name }}</strong>
                        <span class="muted">x{{ item.quantity }}</span>
                    </div>
                    <span>Rp {{ Number(item.price * item.quantity).toLocaleString('id-ID') }}</span>
                </div>
            </div>
        </section>

        <aside class="customer-side-card">
            <p class="eyebrow">Customer Data</p>
            <form class="form-grid" @submit.prevent="submit">
                <input v-model="form.customer.name" type="text" placeholder="Nama pelanggan" />
                <input v-model="form.customer.phone" type="text" placeholder="Nomor WhatsApp" />
                <input v-model="form.customer.email" type="email" placeholder="Email receipt" />
                <select v-model="form.payment_channel">
                    <option v-for="channel in paymentChannels" :key="channel.value" :value="channel.value">{{ channel.label }}</option>
                </select>
                <textarea v-model="form.notes" rows="4" placeholder="Catatan tambahan"></textarea>
                <div class="checkout-total">
                    <span>Total sementara</span>
                    <strong>Rp {{ Number(grandTotal).toLocaleString('id-ID') }}</strong>
                </div>
                <button class="primary-button" type="submit" :disabled="form.processing || form.items.length === 0">Buat Pesanan</button>
                <Link class="secondary-button" :href="`/qr/${table.public_token}`">Kembali ke Menu</Link>
            </form>
        </aside>
    </div>
</template>
