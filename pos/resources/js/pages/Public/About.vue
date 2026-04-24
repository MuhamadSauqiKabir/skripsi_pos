<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    storyTimeline: Array<{ year: string; title: string; description: string }>;
}>();
</script>

<template>
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
</template>
