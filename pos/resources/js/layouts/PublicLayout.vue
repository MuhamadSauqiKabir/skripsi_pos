<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage<{
    currentPage?: string;
    publicNav?: Array<{ label: string; href: string; key: string }>;
    auth: { user?: { name: string } };
}>();
</script>

<template>
    <Head title="Nineties Coffee" />

    <div class="min-h-screen bg-brand-mist text-brand-espresso">
        <header class="sticky top-0 z-40 border-b border-brand-gold/20 bg-brand-cream/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <Link href="/" class="font-serif text-2xl font-black tracking-wide text-brand-espresso">
                    Nineties Coffee
                </Link>

                <nav class="hidden items-center gap-8 text-xs font-semibold uppercase tracking-[0.18em] text-brand-bronze md:flex">
                    <Link
                        v-for="item in page.props.publicNav || []"
                        :key="item.key"
                        :href="item.href"
                        class="border-b-2 pb-1 transition"
                        :class="page.props.currentPage === item.key ? 'border-brand-gold text-brand-gold' : 'border-transparent hover:text-brand-gold'"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Link href="/login" class="rounded-xl border border-brand-gold/30 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand-coffee transition hover:bg-brand-gold hover:text-white">
                        Staff Login
                    </Link>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="bg-brand-espresso text-white">
            <div class="mx-auto grid max-w-7xl gap-10 px-6 py-16 md:grid-cols-4">
                <div class="md:col-span-2">
                    <h3 class="font-serif text-3xl font-bold">Nineties Coffee</h3>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-white/70">
                        Tempat pulang bagi penikmat kopi, percakapan hangat, dan pengalaman cafe yang kini terhubung dengan pemesanan QR dan sistem POS modern.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Explore</h4>
                    <div class="mt-4 grid gap-3 text-sm text-white/70">
                        <Link href="/">Beranda</Link>
                        <Link href="/about">About</Link>
                        <Link href="/store">Store</Link>
                        <Link href="/pesan-menu">Pesan/Menu</Link>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-gold">Contact</h4>
                    <div class="mt-4 grid gap-3 text-sm text-white/70">
                        <p>Jl. Senopati No. 90, Jakarta Selatan</p>
                        <p>hello@ninetiescoffee.id</p>
                        <p>0812-9000-1990</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
