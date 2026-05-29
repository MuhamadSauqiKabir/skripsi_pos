<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { updateContent } from '@/actions/App/Http/Controllers/Management/SettingController';

defineOptions({ layout: AppLayout });

const page = usePage<any>();
const currentSettings = page.props.contentSettings || {};

let defaultTimeline = [
    { year: '2018', title: 'Awal Perjalanan', description: 'Nineties Coffee lahir sebagai ruang hangat untuk kopi, cerita, dan kreativitas.' },
    { year: '2020', title: 'Mematangkan Rasa', description: 'Kami mematangkan karakter rasa, pelayanan, dan identitas visual yang lebih elegan.' },
    { year: '2026', title: 'Pengalaman Digital', description: 'Kini pengalaman kafe diperluas lewat POS, pesan QR meja, dan QRIS realtime.' },
];

let parsedTimeline = defaultTimeline;
if (currentSettings.about_story_timeline) {
    try {
        parsedTimeline = JSON.parse(currentSettings.about_story_timeline);
    } catch (e) {
        // use default
    }
}

const form = useForm({
    settings: [
        { key: 'landing_hero_title', value: currentSettings.landing_hero_title || 'Racikan Kopi Hangat untuk Setiap Cerita.' },
        { key: 'landing_hero_subtitle', value: currentSettings.landing_hero_subtitle || 'Rasakan harmoni antara biji kopi pilihan dan suasana estetik yang menyatukan setiap cerita.' },
        { key: 'shop_address', value: currentSettings.shop_address || 'Jl. Senopati No. 90, Jakarta Selatan' },
        { key: 'shop_phone', value: currentSettings.shop_phone || '0812-9000-1990' },
        { key: 'shop_email', value: currentSettings.shop_email || 'hello@ninetiescoffee.id' },
        { key: 'shop_hours', value: currentSettings.shop_hours || 'Setiap Hari 07:00 - 22:00' },
        { key: 'about_story_timeline', value: parsedTimeline },
    ],
});

const submit = () => {
    form.patch(updateContent().url, {
        preserveScroll: true,
    });
};

const addTimeline = () => {
    form.settings[6].value.push({ year: '', title: '', description: '' });
};

const removeTimeline = (index: number) => {
    form.settings[6].value.splice(index, 1);
};
</script>

<template>
    <div class="gap-5 xl:grid-cols-2 grid">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <header>
                <p class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase">
                    Halaman Utama
                </p>
                <h2 class="mt-2 font-serif text-3xl font-bold">
                    Teks Utama & Banner
                </h2>
            </header>

            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <div class="grid gap-4">
                    <InputFields
                        v-model="form.settings[0].value"
                        label="Judul Utama"
                        placeholder="Racikan kopi hangat..."
                    />
                    <div class="space-y-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Subjudul</label>
                        <textarea
                            v-model="form.settings[1].value"
                            class="w-full bg-[#f1ece3] dark:bg-[#2b3630] border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-[#9b8a72]"
                            rows="3"
                            placeholder="Deskripsi singkat..."
                        ></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#ece4d9]">
                    <h3 class="font-serif text-xl font-bold mb-4">Informasi Kontak & Footer</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="col-span-full">
                            <InputFields
                                v-model="form.settings[2].value"
                                label="Alamat Toko"
                                placeholder="Jl. Senopati No. 90..."
                            />
                        </div>
                        <InputFields
                            v-model="form.settings[3].value"
                            label="Telepon atau WhatsApp"
                            placeholder="0812-..."
                        />
                        <InputFields
                            v-model="form.settings[4].value"
                            label="Email Publik"
                            placeholder="hello@..."
                        />
                        <div class="col-span-full">
                            <InputFields
                                v-model="form.settings[5].value"
                                label="Jam Operasional"
                                placeholder="Setiap Hari 07:00 - 22:00"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 rounded-full bg-[#3d2b1f] text-[#f7f2e8] font-bold text-sm transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Konten' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <header class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase">
                        Halaman Tentang
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold">
                        Sejarah (Timeline)
                    </h2>
                </div>
                <button
                    @click="addTimeline"
                    type="button"
                    class="px-4 py-2 text-xs font-bold rounded-full bg-[#ece4d9] text-[#7a6a58] hover:bg-[#dcd1c1]"
                >
                    + Tambah Momen
                </button>
            </header>
            
            <div class="mt-8 space-y-6">
                <div v-for="(item, index) in form.settings[6].value" :key="index" class="p-4 rounded-lg bg-[#f1ece3] dark:bg-[#2b3630] border border-transparent hover:border-[#9b8a72] transition-colors relative group">
                    <button
                        @click="removeTimeline(index)"
                        type="button"
                        class="absolute top-4 right-4 text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                    
                    <div class="grid gap-3">
                        <div class="grid grid-cols-3 gap-3">
                            <InputFields
                                v-model="item.year"
                                label="Tahun"
                                placeholder="2026"
                                class="col-span-1"
                            />
                            <div class="col-span-2">
                                <InputFields
                                    v-model="item.title"
                                    label="Judul Momen"
                                    placeholder="Pembukaan Cabang..."
                                />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Cerita</label>
                            <textarea
                                v-model="item.description"
                                class="w-full bg-white dark:bg-[#1d2521] border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-[#9b8a72]"
                                rows="2"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button
                        @click="submit"
                        type="button"
                        :disabled="form.processing"
                        class="px-8 py-3 rounded-full bg-[#3d2b1f] text-[#f7f2e8] font-bold text-sm transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        Simpan Timeline
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>
