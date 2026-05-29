<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref } from 'vue';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    contacts: { address: string; phone: string; email: string; hours: string };
}>();

const form = ref({
    name: '',
    phone: '',
    email: '',
    message: ''
});

const sendToWhatsApp = () => {
    let waNumber = props.contacts.phone.replace(/[^0-9]/g, '');
    if (waNumber.startsWith('0')) {
        waNumber = '62' + waNumber.substring(1);
    }
    
    const text = `Halo Nineties Coffee, saya tertarik untuk Reservasi Mood / Bertanya:\n\n*Nama:* ${form.value.name}\n*WA:* ${form.value.phone}\n*Email:* ${form.value.email || '-'}\n\n*Pesan:*\n${form.value.message}`;
    
    const url = `https://wa.me/${waNumber}?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-10 lg:grid-cols-[0.9fr_minmax(0,1fr)]">
            <div class="rounded-3xl bg-brand-espresso p-10 text-white shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Contact</p>
                <h1 class="mt-4 font-serif text-5xl font-bold">Hubungi Kami</h1>
                <div class="mt-10 grid gap-6 text-sm leading-7 text-white/75">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Alamat</h3>
                        <p class="mt-2">{{ contacts.address }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Telepon</h3>
                        <p class="mt-2">{{ contacts.phone }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Email</h3>
                        <p class="mt-2">{{ contacts.email }}</p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Operational Hours</h3>
                        <p class="mt-2">{{ contacts.hours }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-gold/10 bg-white p-10 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Reservasi Mood / Inquiry</p>
                <h2 class="mt-4 font-serif text-4xl font-bold text-brand-espresso">Mari bicara soal meja, event, atau kolaborasi.</h2>
                <form @submit.prevent="sendToWhatsApp" class="mt-8 grid gap-5">
                    <input v-model="form.name" required type="text" placeholder="Nama lengkap" class="rounded-2xl border-brand-gold/20 bg-brand-mist focus:border-brand-gold focus:ring-1 focus:ring-brand-gold" />
                    <input v-model="form.phone" required type="text" placeholder="Nomor Whatsapp" class="rounded-2xl border-brand-gold/20 bg-brand-mist focus:border-brand-gold focus:ring-1 focus:ring-brand-gold" />
                    <input v-model="form.email" type="email" placeholder="Alamat email (Opsional)" class="rounded-2xl border-brand-gold/20 bg-brand-mist focus:border-brand-gold focus:ring-1 focus:ring-brand-gold" />
                    <textarea v-model="form.message" required rows="5" placeholder="Detail Reservasi Mood atau pesan Anda..." class="rounded-2xl border-brand-gold/20 bg-brand-mist focus:border-brand-gold focus:ring-1 focus:ring-brand-gold"></textarea>
                    <button type="submit" class="flex items-center justify-center gap-2 rounded-2xl bg-brand-gold px-6 py-4 text-sm font-bold uppercase tracking-[0.2em] text-brand-espresso transition hover:brightness-110">
                        Kirim via WhatsApp
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
