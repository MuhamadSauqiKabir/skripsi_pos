<script setup lang="ts">
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

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

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const payNow = () => {
    if (typeof window.snap === 'undefined') {
        alert('Midtrans Snap is not loaded yet. Please refresh the page.');
        return;
    }

    window.snap.pay(props.payment.provider_payment_id, {
        onSuccess: async function (result) {
            // Verify payment on server and update order status
            try {
                await fetch(`/orders/${props.order.id}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                });
            } catch (e) {
                console.error('Verification error:', e);
            }
            window.location.reload();
        },
        onPending: function (result) {
            window.location.reload();
        },
        onError: function (result) {
            alert('Pembayaran gagal, silakan coba lagi.');
        },
        onClose: async function () {
            // Also verify when popup is closed (user might have paid already)
            try {
                await fetch(`/orders/${props.order.id}/verify-payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                });
            } catch (e) {
                console.error('Verification error:', e);
            }
            window.location.reload();
        }
    });
};
</script>

<template>
    <div class="customer-page-grid">
        <section class="customer-main-card">
            <header class="mb-10 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-gold">Order Tracking</p>
                    <h2 class="mt-2 font-serif text-4xl font-bold text-brand-espresso">{{ order.public_id }}</h2>
                    <p class="mt-2 text-brand-bronze/60">Terima kasih telah memesan. Silakan pantau status pesanan Anda di bawah ini.</p>
                </div>
                <Link href="/" class="inline-flex items-center gap-2 rounded-xl border border-brand-gold/20 px-4 py-2 text-xs font-bold text-brand-gold hover:bg-brand-gold hover:text-white transition">
                    <span class="material-symbols-outlined text-sm">home</span>
                    Jelajahi Website
                </Link>
            </header>

            <!-- Stepper Status -->
            <div class="stepper mb-12">
                <div class="step-item" :class="{ active: ['awaiting_payment', 'paid', 'brewing', 'ready', 'completed'].includes(order.status) }">
                    <div class="step-dot">
                        <span class="material-symbols-outlined text-lg">assignment</span>
                    </div>
                    <div class="step-content">
                        <h4>Pesanan Dibuat</h4>
                        <p>Pesanan Anda telah kami terima.</p>
                    </div>
                </div>
                <div class="step-item" :class="{ active: ['paid', 'brewing', 'ready', 'completed'].includes(order.status) }">
                    <div class="step-dot">
                        <span class="material-symbols-outlined text-lg">payments</span>
                    </div>
                    <div class="step-content">
                        <h4>Pembayaran Valid</h4>
                        <p>Pembayaran telah dikonfirmasi.</p>
                    </div>
                </div>
                <div class="step-item" :class="{ active: ['brewing', 'ready', 'completed'].includes(order.status) }">
                    <div class="step-dot">
                        <span class="material-symbols-outlined text-lg">coffee</span>
                    </div>
                    <div class="step-content">
                        <h4>Sedang Diracik</h4>
                        <p>Barista kami sedang menyiapkan menu Anda.</p>
                    </div>
                </div>
                <div class="step-item" :class="{ active: ['ready', 'completed'].includes(order.status) }">
                    <div class="step-dot">
                        <span class="material-symbols-outlined text-lg">celebration</span>
                    </div>
                    <div class="step-content">
                        <h4>Siap Dinikmati</h4>
                        <p>Pesanan segera diantar ke meja Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Payment Action Section for QRIS/Midtrans -->
            <div v-if="order.payment_status === 'pending' && order.payment_channel === 'qris'" class="qr-container mb-12 border-brand-gold bg-brand-cream/20 shadow-lg">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-gold text-white shadow-md">
                    <span class="material-symbols-outlined text-3xl">account_balance_wallet</span>
                </div>
                <h3 class="font-serif text-2xl font-bold text-brand-espresso">Selesaikan Pembayaran</h3>
                <p class="max-w-xs text-sm text-brand-bronze">Klik tombol di bawah untuk membayar menggunakan Midtrans (QRIS, GoPay, dll).</p>
                
                <!-- Tombol Bayar jika token tersedia -->
                <button 
                    v-if="payment?.provider_payment_id"
                    @click="payNow"
                    class="primary-button mt-4 w-full max-w-sm rounded-2xl py-4 text-lg shadow-xl transition hover:scale-105 active:scale-95"
                >
                    <span class="material-symbols-outlined">payments</span> Bayar Sekarang!!
                </button>

                <!-- Tombol Retry jika token gagal / belum ada -->
                <form v-else :action="`/orders/${order.id}/regenerate-token`" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <p class="mt-3 text-xs text-red-500 font-bold">Token pembayaran belum tersedia. Klik tombol di bawah untuk memuat ulang.</p>
                    <button 
                        type="submit"
                        class="primary-button mt-3 w-full max-w-sm rounded-2xl py-4 text-lg shadow-xl transition hover:scale-105 active:scale-95"
                    >
                        <span class="material-symbols-outlined">refresh</span> Muat Ulang Token Pembayaran
                    </button>
                </form>
                
                <p class="mt-4 text-[10px] uppercase tracking-widest text-brand-bronze/50">Securely processed by Midtrans</p>
            </div>

            <!-- Payment Action Section for Cash -->
            <div v-else-if="order.payment_status === 'pending' && order.payment_channel === 'cash'" class="qr-container mb-12 border-dashed">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-espresso text-white">
                    <span class="material-symbols-outlined text-3xl">storefront</span>
                </div>
                <h3 class="font-serif text-xl font-bold text-brand-espresso">Bayar di Kasir</h3>
                <p class="text-sm text-brand-bronze">Silakan sebutkan ID Pesanan <strong>{{ order.public_id }}</strong> saat membayar di kasir.</p>
            </div>

            <!-- Items Summary -->
            <div class="list-block border-t border-brand-gold/10 pt-8">
                <h4 class="mb-4 text-xs font-bold uppercase tracking-widest text-brand-gold">Detail Pesanan</h4>
                <div v-for="item in order.items" :key="item.id" class="flex justify-between py-2">
                    <div class="flex gap-3">
                        <span class="text-brand-gold font-bold">x{{ item.quantity }}</span>
                        <span class="text-brand-espresso font-medium">{{ item.menu_name_snapshot }}</span>
                    </div>
                    <span class="text-brand-bronze">Rp {{ Number(item.line_total).toLocaleString('id-ID') }}</span>
                </div>
            </div>
        </section>

        <aside class="customer-side-card">
            <header class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-gold">Feedback</p>
                <h3 class="mt-2 font-serif text-2xl font-bold text-brand-espresso">Kepuasan Anda</h3>
            </header>

            <form class="form-grid space-y-6" @submit.prevent="submitFeedback">
                <div class="flex justify-center gap-2 py-4">
                    <button 
                        v-for="star in 5" 
                        :key="star"
                        type="button"
                        @click="feedbackForm.rating = star"
                        class="transition"
                        :class="feedbackForm.rating >= star ? 'text-brand-gold scale-110' : 'text-brand-gold/20 scale-100'"
                    >
                        <span class="material-symbols-outlined text-4xl" :style="{ fontVariationSettings: feedbackForm.rating >= star ? '\'FILL\' 1' : '\'FILL\' 0' }">star</span>
                    </button>
                </div>
                <textarea v-model="feedbackForm.comment" rows="4" placeholder="Bagikan pengalaman Anda bersama kami..."></textarea>
                <button class="primary-button w-full rounded-2xl shadow-md" type="submit" :disabled="feedbackForm.processing">
                    {{ feedbackForm.processing ? 'Mengirim...' : 'Kirim Penilaian' }}
                </button>
                <p class="text-center text-[10px] text-brand-bronze/50 italic">Suara Anda sangat berarti bagi kami.</p>
                <div class="mt-8 border-t border-brand-gold/10 pt-6">
                    <Link href="/my-orders" class="flex items-center justify-center gap-2 text-xs font-bold text-brand-gold hover:underline">
                        <span class="material-symbols-outlined text-sm">history_edu</span>
                        Lihat Semua Pesanan Saya
                    </Link>
                </div>
            </form>
        </aside>
    </div>
</template>
