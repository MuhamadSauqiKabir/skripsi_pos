<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ActiveOrderWidget from '@/components/ActiveOrderWidget.vue';

const page = usePage<{
    currentPage?: string;
    publicNav?: Array<{ label: string; href: string; key: string }>;
    auth: { user?: { name: string } };
}>();

const { language, labels, setLanguage } = useLanguage();
const languageOpen = ref(false);

const navLabel = (key: string, fallback: string) => {
    const map: Record<string, string> = {
        home: labels.value.home,
        about: labels.value.about,
        store: labels.value.store,
        menu: labels.value.menu,
        contact: labels.value.contact,
    };

    return map[key] || fallback;
};

const selectLanguage = (value: 'IND' | 'EN') => {
    setLanguage(value);
    languageOpen.value = false;
};
</script>

<template>
    <Head title="Nineties Coffee" />

    <div class="nineties-public">
        <header class="nineties-public-nav">
            <Link href="/" class="nineties-public-logo">Nineties Coffee</Link>

            <nav class="nineties-public-links">
                <Link
                    v-for="item in page.props.publicNav || []"
                    :key="item.key"
                    :href="item.href"
                    class="nineties-public-link"
                    :class="{ active: page.props.currentPage === item.key }"
                >
                    {{ navLabel(item.key, item.label) }}
                </Link>
            </nav>

            <div class="gap-2 flex items-center">
                <div class="nineties-language">
                    <button
                        type="button"
                        class="nineties-lang-button"
                        @click="languageOpen = !languageOpen"
                    >
                        <span class="material-symbols-outlined text-base"
                            >language</span
                        >
                        {{ language }}
                        <span class="material-symbols-outlined text-base"
                            >expand_more</span
                        >
                    </button>
                    <div v-if="languageOpen" class="nineties-lang-menu">
                        <button
                            type="button"
                            :class="{ active: language === 'IND' }"
                            @click="selectLanguage('IND')"
                        >
                            Indonesia
                            <span v-if="language === 'IND'" class="material-symbols-outlined text-base">check</span>
                        </button>
                        <button
                            type="button"
                            :class="{ active: language === 'EN' }"
                            @click="selectLanguage('EN')"
                        >
                            English
                            <span v-if="language === 'EN'" class="material-symbols-outlined text-base">check</span>
                        </button>
                    </div>
                </div>

                <Link href="/login" class="nineties-pill-button">
                    {{ labels.staffLogin }}
                </Link>
            </div>
        </header>

        <main>
            <slot />
            <ActiveOrderWidget />
        </main>

        <footer class="text-white bg-[#3d2b1f]">
            <div
                class="max-w-7xl gap-10 px-6 py-16 md:grid-cols-4 mx-auto grid"
            >
                <div class="md:col-span-2">
                    <h3 class="font-serif text-3xl font-bold text-[#c9a84c]">
                        Nineties Coffee
                    </h3>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-white/70">
                        {{
                            language === 'EN'
                                ? 'A warm digital cafe experience connected to QR ordering, payment, and modern POS operations.'
                                : 'Tempat pulang bagi penikmat kopi, percakapan hangat, pemesanan QR, pembayaran, dan sistem kasir modern.'
                        }}
                    </p>
                </div>
                <div>
                    <h4
                        class="text-xs font-semibold tracking-[0.2em] text-[#c9a84c] uppercase"
                    >
                        {{ language === 'EN' ? 'Explore' : 'Jelajah' }}
                    </h4>
                    <div class="mt-4 gap-3 text-sm text-white/70 grid">
                        <Link href="/">{{ labels.home }}</Link>
                        <Link href="/about">{{ labels.about }}</Link>
                        <Link href="/contacts">{{ labels.contact }}</Link>
                        <Link href="/pesan-menu">{{ labels.menu }}</Link>
                    </div>
                </div>
                <div>
                    <h4
                        class="text-xs font-semibold tracking-[0.2em] text-[#c9a84c] uppercase"
                    >
                        {{ language === 'EN' ? 'Contact' : 'Kontak' }}
                    </h4>
                    <div class="mt-4 gap-3 text-sm text-white/70 grid">
                        <p>Jl. Senopati No. 90, Jakarta Selatan</p>
                        <p>hello@ninetiescoffee.id</p>
                        <p>0812-9000-1990</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
