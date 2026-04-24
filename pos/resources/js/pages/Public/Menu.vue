<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    categories: Array<any>;
    tables: Array<{ name: string; public_token: string }>;
}>();
</script>

<template>
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Pesan / Menu</p>
                <h1 class="mt-4 font-serif text-5xl font-bold text-brand-espresso">Rasakan menu favorit kami, lalu pesan langsung dari meja.</h1>
            </div>
            <div class="rounded-3xl border border-brand-gold/10 bg-white p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Quick Order</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a
                        v-for="table in tables.slice(0, 4)"
                        :key="table.public_token"
                        :href="`/qr/${table.public_token}`"
                        class="rounded-xl bg-brand-cream px-4 py-3 text-xs font-bold uppercase tracking-[0.18em] text-brand-coffee transition hover:bg-brand-gold hover:text-white"
                    >
                        {{ table.name }}
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-14 grid gap-10">
            <section v-for="category in categories" :key="category.id">
                <div class="mb-6 flex items-end justify-between border-b border-brand-gold/20 pb-4">
                    <div>
                        <h2 class="font-serif text-3xl font-bold text-brand-espresso">{{ category.name }}</h2>
                        <p class="mt-2 text-sm text-brand-bronze">{{ category.description }}</p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="item in category.menu_items"
                        :key="item.id"
                        class="rounded-3xl border border-brand-gold/10 bg-white p-6 shadow-soft"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-serif text-2xl font-bold text-brand-espresso">{{ item.name }}</h3>
                                <p class="mt-3 text-sm leading-7 text-brand-bronze">{{ item.description }}</p>
                            </div>
                            <span class="rounded-full bg-brand-cream px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand-coffee">
                                {{ item.prep_time_minutes }}m
                            </span>
                        </div>
                        <div class="mt-6 flex items-center justify-between">
                            <strong class="text-brand-coffee">Rp {{ Number(item.price).toLocaleString('id-ID') }}</strong>
                            <a href="/store" class="text-xs font-bold uppercase tracking-[0.18em] text-brand-gold">Visit Store</a>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </section>
</template>
