<script setup lang="ts">
import OrderSidebar from '@/components/OrderSidebar.vue';
import { useLanguage } from '@/composables/useLanguage';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ layout: PublicLayout });

type MenuItem = {
    id: number;
    name: string;
    description: string | null;
    image_url: string | null;
    price: number | string;
    prep_time_minutes: number;
    is_featured: boolean;
};

type Category = {
    id: number;
    name: string;
    description: string | null;
    menu_items: MenuItem[];
};

type DiningTable = {
    name: string;
    public_token: string;
    floor: number;
};

const props = defineProps<{
    categories: Category[];
    tables: DiningTable[];
}>();

const { language } = useLanguage();
const selectedMenuId = ref<number | string | null>(null);

const copy = computed(() =>
    language.value === 'EN'
        ? {
              title: 'Order Menu',
              kicker: 'Order Menu',
              heading: 'Pick your favorite menu, then order from the table.',
              intro: 'Choose a menu item, set floor, table, quantity, and payment from the order sidebar.',
              choose: 'Choose Menu',
              featured: 'Recommended',
              served: 'Freshly Served',
              minute: 'Min',
          }
        : {
              title: 'Pesan Menu',
              kicker: 'Pesan Menu',
              heading: 'Pilih menu favorit, lalu pesan langsung dari meja.',
              intro: 'Pilih menu, atur lantai, nomor meja, jumlah pesanan, dan pembayaran dari sidebar pesanan.',
              choose: 'Pilih Menu',
              featured: 'Rekomendasi',
              served: 'Disajikan Segar',
              minute: 'Menit',
          },
);

const flattenedMenuItems = computed(() =>
    props.categories.flatMap((category) =>
        category.menu_items.map((item) => ({
            id: item.id,
            name: item.name,
            price: item.price,
            category: category.name,
        })),
    ),
);

const translatedText = (value: string | null | undefined) => {
    if (!value || language.value === 'EN') {
        return value || '';
    }

    const map: Record<string, string> = {
        Snacks: 'Camilan',
        'Snack and Small Bites': 'Camilan dan kudapan ringan',
        'Coffee Signature': 'Kopi Andalan',
        'Non Coffee': 'Non Kopi',
        'Freshly Served': 'Disajikan Segar',
        Recommended: 'Rekomendasi',
    };

    return map[value] ?? value;
};

const selectMenu = (item: MenuItem) => {
    selectedMenuId.value = item.id;
};
</script>

<template>
    <Head :title="copy.title" />

    <section class="mx-auto max-w-[1540px] px-6 py-16 lg:px-10">
        <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_390px]">
            <main class="min-w-0">
                <div class="max-w-4xl">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-brand-gold">
                        {{ copy.kicker }}
                    </p>
                    <h1 class="mt-5 font-serif text-4xl font-black leading-tight text-brand-espresso md:text-6xl">
                        {{ copy.heading }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-sm leading-7 text-brand-bronze md:text-base">
                        {{ copy.intro }}
                    </p>
                </div>

                <div class="mt-14 grid gap-12">
                    <section v-for="category in categories" :key="category.id">
                        <div class="mb-6 flex items-end justify-between border-b border-brand-gold/20 pb-4">
                            <div>
                                <h2 class="font-serif text-3xl font-bold text-brand-espresso">
                                    {{ translatedText(category.name) }}
                                </h2>
                                <p class="mt-2 text-sm text-brand-bronze">
                                    {{ translatedText(category.description) }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2 2xl:grid-cols-3">
                            <article
                                v-for="item in category.menu_items"
                                :key="item.id"
                                :class="[
                                    'client-menu-card group flex flex-col',
                                    String(selectedMenuId) === String(item.id)
                                        ? 'ring-2 ring-[#c9a84c]'
                                        : '',
                                ]"
                            >
                                <div class="relative h-48 overflow-hidden">
                                    <img
                                        v-if="item.image_url"
                                        :src="item.image_url"
                                        :alt="item.name"
                                        class="menu-card-image h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center bg-brand-cream/50 text-brand-gold/30"
                                    >
                                        <span class="material-symbols-outlined text-6xl">image</span>
                                    </div>

                                    <div class="absolute bottom-4 left-4 rounded-xl bg-white/90 px-3 py-2 text-sm font-bold text-brand-espresso shadow-sm backdrop-blur-sm">
                                        Rp {{ Number(item.price).toLocaleString('id-ID') }}
                                    </div>

                                    <div class="absolute right-4 top-4">
                                        <span class="rounded-full bg-[#3d2b1f]/90 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[0.18em] text-[#f7f2e8] backdrop-blur-sm">
                                            {{ item.prep_time_minutes }} {{ copy.minute }}
                                        </span>
                                    </div>
                                </div>

                                <div class="menu-card-content flex flex-1 flex-col justify-between">
                                    <div>
                                        <h4 class="font-serif text-xl font-bold text-brand-espresso">
                                            {{ item.name }}
                                        </h4>
                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-brand-bronze/70">
                                            {{ item.description }}
                                        </p>
                                    </div>

                                    <div class="menu-card-footer mt-4 gap-3">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">
                                            {{ item.is_featured ? copy.featured : copy.served }}
                                        </span>
                                        <button
                                            type="button"
                                            class="rounded-full bg-[#3d2b1f] px-4 py-2 text-[11px] font-black uppercase tracking-[0.12em] text-[#f7f2e8] transition hover:bg-[#1f1a17]"
                                            @click="selectMenu(item)"
                                        >
                                            {{ copy.choose }}
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>
                </div>
            </main>

            <OrderSidebar
                v-model:selected-menu-id="selectedMenuId"
                :tables="tables"
                :menu-items="flattenedMenuItems"
                :show-online-orders="false"
                mode="public"
            />
        </div>
    </section>
</template>
