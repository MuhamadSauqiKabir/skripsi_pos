<script setup lang="ts">
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: CustomerLayout });

const props = defineProps<{
    order: any;
    payment: any;
}>();

const feedbackForm = useForm({
    rating: 5,
    comment: '',
});

const submitFeedback = () => feedbackForm.post(`/tracking/${props.order.public_id}/feedback`);
</script>

<template>
    <div class="customer-page-grid">
        <section class="customer-main-card">
            <p class="eyebrow">Tracking Order</p>
            <h2>{{ order.public_id }}</h2>
            <p class="muted">Status pesanan: {{ order.status }} | Status pembayaran: {{ order.payment_status }}</p>

            <div class="alert-list">
                <div class="alert-panel" :class="{ active: ['awaiting_payment', 'paid', 'brewing', 'ready', 'completed'].includes(order.status) }">Order dibuat</div>
                <div class="alert-panel" :class="{ active: ['paid', 'brewing', 'ready', 'completed'].includes(order.status) }">Pembayaran tervalidasi</div>
                <div class="alert-panel" :class="{ active: ['brewing', 'ready', 'completed'].includes(order.status) }">Diracik barista</div>
                <div class="alert-panel" :class="{ active: ['ready', 'completed'].includes(order.status) }">Siap diambil</div>
            </div>

            <div v-if="payment?.qr_string" class="alert-panel">
                <strong>QRIS Payload</strong>
                <p class="muted">{{ payment.qr_string }}</p>
            </div>

            <div class="list-block">
                <div v-for="item in order.items" :key="item.id" class="list-row">
                    <div>
                        <strong>{{ item.menu_name_snapshot }}</strong>
                        <p class="muted">x{{ item.quantity }}</p>
                    </div>
                    <span>Rp {{ Number(item.line_total).toLocaleString('id-ID') }}</span>
                </div>
            </div>
        </section>

        <section class="customer-side-card">
            <p class="eyebrow">Customer Feedback</p>
            <h2>Nilai pengalaman Anda</h2>
            <form class="form-grid" @submit.prevent="submitFeedback">
                <select v-model="feedbackForm.rating">
                    <option :value="5">5 - Sangat puas</option>
                    <option :value="4">4 - Puas</option>
                    <option :value="3">3 - Cukup</option>
                    <option :value="2">2 - Kurang</option>
                    <option :value="1">1 - Buruk</option>
                </select>
                <textarea v-model="feedbackForm.comment" rows="3" placeholder="Komentar tambahan"></textarea>
                <button class="primary-button" type="submit">Kirim Penilaian</button>
            </form>
        </section>
    </div>
</template>
