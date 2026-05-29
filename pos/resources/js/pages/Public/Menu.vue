<script setup lang="ts">
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    categories: Array<any>;
    tables: Array<{ name: string; public_token: string; floor: number }>;
}>();

const activeFloor = ref(1);

const groupedTables = computed(() => {
    const groups: Record<number, Array<any>> = { 1: [], 2: [], 3: [] };
    props.tables.forEach((table) => {
        const floor = table.floor || 1;
        if (groups[floor]) {
            groups[floor].push(table);
        }
    });
    return groups;
});
</script>

<template>
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Pesan / Menu</p>
                <h1 class="mt-4 font-serif text-5xl font-bold text-brand-espresso">Rasakan menu favorit kami, lalu pesan langsung dari meja.</h1>
            </div>
            <div class="rounded-3xl border border-brand-gold/10 bg-white p-5 shadow-soft w-full lg:w-96">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Quick Order / Pesan Cepat</p>
                
                <!-- Floor selector tabs -->
                <div class="mt-3 flex gap-1 rounded-xl bg-brand-cream/40 p-1">
                    <button
                        v-for="floor in [1, 2, 3]"
                        :key="floor"
                        type="button"
                        class="flex-1 rounded-lg py-1.5 text-xs font-bold transition-all"
                        :class="activeFloor === floor ? 'bg-[#3d2b1f] text-white shadow-sm' : 'text-brand-coffee hover:bg-brand-cream/50'"
                        @click="activeFloor = floor"
                    >
                        Lantai {{ floor }}
                    </button>
                </div>

                <!-- Table selection -->
                <div class="mt-4">
                    <div v-if="groupedTables[activeFloor].length > 0" class="flex flex-wrap gap-2 max-h-[140px] overflow-y-auto pr-1">
                        <a
                            v-for="table in groupedTables[activeFloor]"
                            :key="table.public_token"
                            :href="`/qr/${table.public_token}`"
                            class="rounded-xl bg-brand-cream px-3 py-2 text-xs font-bold uppercase tracking-[0.12em] text-brand-coffee transition hover:bg-brand-gold hover:text-white"
                        >
                            {{ table.name }}
                        </a>
                    </div>
                    <div v-else class="py-4 text-center text-xs text-brand-bronze/60">
                        Belum ada meja aktif di Lantai {{ activeFloor }}
                    </div>
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
                        class="client-menu-card group flex flex-col"
                    >
                        <div class="relative h-48 overflow-hidden">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                :alt="item.name"
                                class="menu-card-image"
                                style="height: 100%; width: 100%; object-fit: cover;"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-brand-cream/50 text-brand-gold/30">
                                <span class="material-symbols-outlined text-6xl">image</span>
                            </div>
                            
                            <!-- Price Tag on Image -->
                            <div class="absolute bottom-4 left-4 rounded-xl bg-white/90 px-3 py-2 text-sm font-bold text-brand-espresso backdrop-blur-sm shadow-sm">
                                Rp {{ Number(item.price).toLocaleString('id-ID') }}
                            </div>

                            <!-- Prep Time Tag on Image -->
                            <div class="absolute right-4 top-4">
                                <span class="rounded-full bg-[#3d2b1f]/90 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[0.18em] text-[#f7f2e8] backdrop-blur-sm">
                                    {{ item.prep_time_minutes }} Min
                                </span>
                            </div>
                        </div>

                        <div class="menu-card-content flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-serif text-xl font-bold text-brand-espresso">{{ item.name }}</h4>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-brand-bronze/70">{{ item.description }}</p>
                            </div>
                            
                            <div class="menu-card-footer mt-4">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">
                                    {{ item.is_featured ? '★ Recommended' : 'Freshly Served' }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </section>
</template>
