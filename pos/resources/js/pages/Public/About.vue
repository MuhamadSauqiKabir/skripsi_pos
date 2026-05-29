<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    storyTimeline: Array<{ year: string; title: string; description: string }>;
    location: {
        address: string;
        phone: string;
        email: string;
        hours: string;
    };
}>();
</script>

<template>
    <div>
        <!-- Story Section -->
        <section class="mx-auto max-w-6xl px-6 py-20">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">About Us</p>
                <h1 class="mt-4 font-serif text-5xl font-bold text-brand-espresso">Cerita yang dimulai dari secangkir kopi.</h1>
                <p class="mt-6 text-base leading-8 text-brand-bronze">
                    Nineties Coffee menggabungkan rasa nostalgia, keramahan pelayanan, dan teknologi cafe modern agar setiap kunjungan terasa akrab sekaligus efisien.
                </p>
            </div>

            <div class="mt-16 grid gap-8">
                <article
                    v-for="item in storyTimeline"
                    :key="item.year"
                    class="grid gap-6 rounded-3xl border border-brand-gold/10 bg-white p-8 shadow-soft md:grid-cols-[140px_minmax(0,1fr)]"
                >
                    <div class="font-serif text-4xl font-bold text-brand-gold">{{ item.year }}</div>
                    <div>
                        <h2 class="font-serif text-3xl font-bold text-brand-espresso">{{ item.title }}</h2>
                        <p class="mt-3 text-base leading-8 text-brand-bronze">{{ item.description }}</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- Lokasi Section -->
        <section class="bg-brand-cream/50 py-20">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Kunjungi Kami</p>
                    <h2 class="mt-4 font-serif text-4xl font-bold text-brand-espresso">Lokasi Kami</h2>
                    <p class="mt-4 text-base leading-8 text-brand-bronze">
                        Temukan kami dan nikmati pengalaman kopi yang tak terlupakan.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Alamat -->
                    <div class="flex flex-col items-center rounded-3xl border border-brand-gold/10 bg-white p-8 text-center shadow-soft">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-gold/10 text-2xl">
                            📍
                        </div>
                        <h3 class="mt-4 font-serif text-lg font-bold text-brand-espresso">Alamat</h3>
                        <p class="mt-2 text-sm leading-6 text-brand-bronze">{{ location.address }}</p>
                    </div>

                    <!-- Jam Buka -->
                    <div class="flex flex-col items-center rounded-3xl border border-brand-gold/10 bg-white p-8 text-center shadow-soft">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-gold/10 text-2xl">
                            🕐
                        </div>
                        <h3 class="mt-4 font-serif text-lg font-bold text-brand-espresso">Jam Buka</h3>
                        <p class="mt-2 text-sm leading-6 text-brand-bronze">{{ location.hours }}</p>
                    </div>

                    <!-- Telepon -->
                    <div class="flex flex-col items-center rounded-3xl border border-brand-gold/10 bg-white p-8 text-center shadow-soft">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-gold/10 text-2xl">
                            📞
                        </div>
                        <h3 class="mt-4 font-serif text-lg font-bold text-brand-espresso">Telepon</h3>
                        <a :href="'tel:' + location.phone" class="mt-2 text-sm leading-6 text-brand-gold hover:underline">
                            {{ location.phone }}
                        </a>
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col items-center rounded-3xl border border-brand-gold/10 bg-white p-8 text-center shadow-soft">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-gold/10 text-2xl">
                            ✉️
                        </div>
                        <h3 class="mt-4 font-serif text-lg font-bold text-brand-espresso">Email</h3>
                        <a :href="'mailto:' + location.email" class="mt-2 text-sm leading-6 text-brand-gold hover:underline">
                            {{ location.email }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
