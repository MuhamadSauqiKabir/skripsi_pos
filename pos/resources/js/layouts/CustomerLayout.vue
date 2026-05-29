<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ActiveOrderWidget from '@/components/ActiveOrderWidget.vue';

const page = usePage<{
    flash: { success?: string; error?: string };
    midtrans_client_key: string;
}>();


const { language, setLanguage } = useLanguage();
const languageOpen = ref(false);

const selectLanguage = (value: 'IND' | 'EN') => {
    setLanguage(value);
    languageOpen.value = false;
};
</script>

<template>
    <div class="customer-shell bg-[#faf9f6]">
        <header class="mx-auto mb-6 flex max-w-7xl items-center justify-between border-b border-brand-gold/10 bg-white/50 px-6 py-4 backdrop-blur-md">
            <Link href="/" class="flex items-center gap-3 hover:opacity-80 transition">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gold text-white shadow-md">
                    <span class="material-symbols-outlined text-lg">coffee</span>
                </div>
                <div>
                    <h1 class="font-serif text-sm font-black tracking-tight text-brand-espresso">
                        Nineties Coffee <span class="mx-1 text-brand-gold">|</span> 
                        <span class="font-sans text-[10px] uppercase tracking-widest text-brand-bronze/60">
                            {{ language === 'EN' ? 'Digital Order' : 'Order Digital' }}
                        </span>
                    </h1>
                </div>
            </Link>

            <div class="nineties-language">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-full bg-brand-espresso px-3 py-1.5 text-brand-gold transition hover:brightness-110"
                    @click="languageOpen = !languageOpen"
                >
                    <span class="material-symbols-outlined text-xs">language</span>
                    <span class="text-[10px] font-bold">{{ language }}</span>
                </button>
                <div v-if="languageOpen" class="nineties-lang-menu !top-[110%] !right-0 !border !border-brand-gold/20 shadow-xl">
                    <button
                        type="button"
                        :class="{ active: language === 'IND' }"
                        @click="selectLanguage('IND')"
                        class="!px-5 !py-3"
                    >
                        Bahasa Indonesia <span v-if="language === 'IND'">✓</span>
                    </button>
                    <button
                        type="button"
                        :class="{ active: language === 'EN' }"
                        @click="selectLanguage('EN')"
                        class="!px-5 !py-3"
                    >
                        English <span v-if="language === 'EN'">✓</span>
                    </button>
                </div>
            </div>

            <Link href="/my-orders" class="ml-4 flex items-center gap-2 rounded-full border border-brand-gold/20 px-4 py-2 text-[10px] font-bold text-brand-gold transition hover:bg-brand-gold hover:text-white">
                <span class="material-symbols-outlined text-xs">history</span>
                Riwayat
            </Link>
        </header>

        <div v-if="page.props.flash.success" class="flash-success">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash.error" class="flash-error">
            {{ page.props.flash.error }}
        </div>

        <slot />
        <ActiveOrderWidget />
    </div>
</template>
