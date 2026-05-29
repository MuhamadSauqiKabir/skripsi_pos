<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { update } from '@/actions/App/Http/Controllers/Management/SettingController';

defineOptions({ layout: AppLayout });

const page = usePage<any>();
const currentSettings = page.props.settings || {};

const form = useForm({
    settings: [
        { key: 'shop_name', value: currentSettings.shop_name || 'Nineties' },
        { key: 'shop_tagline', value: currentSettings.shop_tagline || 'Sistem Kasir' },
        { key: 'tax_rate', value: currentSettings.tax_rate || '11' },
    ],
});

const submit = () => {
    form.patch(update().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="gap-5 xl:grid-cols-2 grid">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <header>
                <p class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase">
                    Konfigurasi Inti
                </p>
                <h2 class="mt-2 font-serif text-3xl font-bold">
                    Pengaturan Umum
                </h2>
            </header>

            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="col-span-full">
                        <InputFields
                            v-model="form.settings[0].value"
                            label="Nama Toko"
                            placeholder="Nineties Coffee"
                        />
                    </div>
                    <div class="col-span-full">
                        <InputFields
                            v-model="form.settings[1].value"
                            label="Slogan"
                            placeholder="Seni Meracik Kopi"
                        />
                    </div>
                    <div>
                        <InputFields
                            v-model="form.settings[2].value"
                            label="Pajak (%)"
                            type="number"
                            placeholder="11"
                        />
                    </div>
                </div>


                <div class="pt-6 flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 rounded-full bg-[#3d2b1f] text-[#f7f2e8] font-bold text-sm transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <header>
                <p class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase">
                    Lingkungan Sistem
                </p>
                <h2 class="mt-2 font-serif text-3xl font-bold">
                    Kesehatan Sistem
                </h2>
            </header>
            
            <div class="mt-8 space-y-4">
                <div class="p-4 rounded-lg bg-[#f1ece3] flex items-center justify-between">
                    <span>Koneksi Database</span>
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase">Terhubung</span>
                </div>
                <div class="p-4 rounded-lg bg-[#f1ece3] flex items-center justify-between">
                    <span>Penyimpanan</span>
                    <span class="font-mono text-sm text-[#7a6a58]">local</span>
                </div>
                <div class="p-4 rounded-lg bg-[#f1ece3] flex items-center justify-between">
                    <span>Mode Aplikasi</span>
                    <span class="font-mono text-sm text-[#7a6a58]">development</span>
                </div>
            </div>

            <div class="mt-8 p-5 rounded-lg bg-[#3d2b1f] text-[#f7f2e8]">
                <h4 class="font-serif text-lg font-bold mb-2">Transparansi Audit</h4>
                <p class="text-sm text-[#f7f2e8]/70 leading-relaxed">
                    Setiap perubahan konfigurasi dicatat dalam sistem audit log untuk keamanan dan transparansi operasional.
                </p>
            </div>
        </section>
    </div>
</template>
