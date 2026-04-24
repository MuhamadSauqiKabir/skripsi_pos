<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    contacts: { address: string; phone: string; email: string; hours: string };
}>();
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
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">General Inquiry</p>
                <h2 class="mt-4 font-serif text-4xl font-bold text-brand-espresso">Mari bicara soal kopi, event, atau kolaborasi.</h2>
                <form class="mt-8 grid gap-5">
                    <input type="text" placeholder="Nama lengkap" class="rounded-2xl border-brand-gold/20 bg-brand-mist" />
                    <input type="text" placeholder="Nomor Whatsapp" class="rounded-2xl border-brand-gold/20 bg-brand-mist" />
                    <input type="email" placeholder="Alamat email" class="rounded-2xl border-brand-gold/20 bg-brand-mist" />
                    <textarea rows="5" placeholder="Tulis pesan Anda" class="rounded-2xl border-brand-gold/20 bg-brand-mist"></textarea>
                    <button type="button" class="rounded-2xl bg-brand-gold px-6 py-4 text-sm font-bold uppercase tracking-[0.2em] text-brand-espresso transition hover:brightness-110">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
