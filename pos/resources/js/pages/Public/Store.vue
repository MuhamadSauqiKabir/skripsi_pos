<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    stores: Array<{ name: string; hours: string; address: string }>;
}>();
</script>

<template>
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Store Locator</p>
            <h1 class="mt-4 font-serif text-5xl font-bold text-brand-espresso">Temukan rumah terdekat untuk menikmati kopi.</h1>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            <article v-for="store in stores" :key="store.name" class="overflow-hidden rounded-3xl border border-brand-gold/10 bg-white shadow-soft">
                <img
                    src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=80"
                    alt="Store"
                    class="h-56 w-full object-cover"
                />
                <div class="p-6">
                    <h2 class="font-serif text-3xl font-bold text-brand-espresso">{{ store.name }}</h2>
                    <p class="mt-3 text-sm leading-7 text-brand-bronze">{{ store.address }}</p>
                    <div class="mt-5 inline-flex rounded-full bg-brand-cream px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-brand-coffee">
                        {{ store.hours }}
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
