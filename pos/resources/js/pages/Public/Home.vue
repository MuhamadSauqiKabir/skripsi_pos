<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    featuredMenus: Array<any>;
}>();
</script>

<template>
    <div>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0">
                <img
                    src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1600&q=80"
                    alt="Coffee hero"
                    class="h-full w-full object-cover brightness-[0.35]"
                />
            </div>
            <div class="relative mx-auto flex min-h-[78vh] max-w-7xl items-center px-6 py-20 text-white">
                <div class="max-w-3xl">
                    <span class="mb-4 block text-xs font-semibold uppercase tracking-[0.35em] text-brand-gold">Feels Like Home</span>
                    <h1 class="font-serif text-5xl font-bold leading-tight md:text-7xl">
                        Kembali ke rasa, menetap di jiwa.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                        Nineties Coffee menghadirkan kehangatan cafe modern dengan sentuhan nostalgia, pelayanan ramah, dan pengalaman digital untuk order meja dan pembayaran QRIS.
                    </p>
                    <div class="mt-10 flex flex-wrap gap-4">
                        <a href="/pesan-menu" class="rounded-xl bg-brand-gold px-7 py-4 text-sm font-bold uppercase tracking-[0.18em] text-brand-espresso transition hover:brightness-110">
                            Jelajahi Menu
                        </a>
                        <a href="/store" class="rounded-xl border border-white/30 px-7 py-4 text-sm font-bold uppercase tracking-[0.18em] text-white transition hover:bg-white/10">
                            Reservasi Mood
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-24">
            <div class="mb-12 flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Signature Selection</p>
                    <h2 class="mt-3 font-serif text-4xl font-bold text-brand-espresso">Pilihan favorit tamu kami</h2>
                </div>
                <a href="/pesan-menu" class="hidden text-sm font-semibold uppercase tracking-[0.2em] text-brand-coffee md:block">Lihat Semua</a>
            </div>

            <div class="grid gap-6 md:grid-cols-12">
                <article class="relative overflow-hidden rounded-3xl md:col-span-7">
                    <img
                        src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1200&q=80"
                        alt="Featured menu"
                        class="h-full min-h-[480px] w-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-10 text-white">
                        <span class="inline-flex rounded-full bg-brand-gold px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-brand-espresso">Best Seller</span>
                        <h3 class="mt-4 font-serif text-4xl font-bold">{{ featuredMenus[0]?.name || 'Nineties Gold Latte' }}</h3>
                        <p class="mt-3 max-w-lg text-white/75">
                            Rasa creamy, roasty, dan lembut yang jadi signature pengalaman ngopi di Nineties Coffee.
                        </p>
                    </div>
                </article>

                <div class="grid gap-6 md:col-span-5">
                    <article
                        v-for="item in featuredMenus.slice(1, 4)"
                        :key="item.id"
                        class="rounded-3xl border border-brand-gold/10 bg-white p-6 shadow-soft"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Featured Menu</p>
                        <h3 class="mt-3 font-serif text-2xl font-bold text-brand-espresso">{{ item.name }}</h3>
                        <p class="mt-3 text-sm leading-7 text-brand-bronze">{{ item.description }}</p>
                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-sm font-semibold text-brand-coffee">Rp {{ Number(item.price).toLocaleString('id-ID') }}</span>
                            <a href="/pesan-menu" class="text-xs font-bold uppercase tracking-[0.18em] text-brand-gold">Pesan</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="border-y border-brand-gold/10 bg-white">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 py-24 lg:grid-cols-2 lg:items-center">
                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1200&q=80"
                        alt="Cafe interior"
                        class="h-[520px] w-full rounded-3xl object-cover shadow-soft"
                    />
                    <div class="absolute -bottom-6 -right-6 rounded-2xl bg-brand-gold px-6 py-5 text-brand-espresso shadow-soft">
                        <div class="font-serif text-3xl font-bold">QR Order</div>
                        <div class="mt-1 text-xs font-semibold uppercase tracking-[0.2em]">Table Experience</div>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Why Nineties Coffee</p>
                    <h2 class="mt-4 font-serif text-4xl font-bold text-brand-espresso">Setiap sudut dibuat untuk percakapan yang hangat.</h2>
                    <p class="mt-6 text-base leading-8 text-brand-bronze">
                        Terinspirasi dari pengalaman brand cafe modern seperti Arah Coffee, client side Nineties Coffee kami hadirkan sebagai ruang digital yang hangat, visual, dan mudah dijelajahi.
                    </p>
                    <div class="mt-8 grid gap-4">
                        <div class="flex items-center gap-4 rounded-2xl bg-brand-cream p-4">
                            <span class="material-symbols-outlined text-brand-gold">check_circle</span>
                            <p class="text-sm text-brand-coffee">Visual warm, airy, dan brand-driven untuk pengunjung</p>
                        </div>
                        <div class="flex items-center gap-4 rounded-2xl bg-brand-cream p-4">
                            <span class="material-symbols-outlined text-brand-gold">check_circle</span>
                            <p class="text-sm text-brand-coffee">Akses cepat ke menu, store, kontak, dan pemesanan meja</p>
                        </div>
                        <div class="flex items-center gap-4 rounded-2xl bg-brand-cream p-4">
                            <span class="material-symbols-outlined text-brand-gold">check_circle</span>
                            <p class="text-sm text-brand-coffee">Terhubung langsung ke flow QR order dan POS internal</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
