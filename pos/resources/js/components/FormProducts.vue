<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';
import { ref, watch } from 'vue';

const props = defineProps<{
    form: any;
    categories: Array<any>;
}>();

const imagePreview = ref<string | null>(null);

const onFileChange = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        props.form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

watch(() => props.form.image, (newVal) => {
    if (!newVal) {
        imagePreview.value = null;
    }
});
</script>

<template>
    <div class="gap-4 grid">
        <!-- Photo Upload -->
        <label class="gap-2 grid">
            <span class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]">Product Image / Gambar Produk</span>
            <div class="flex flex-col items-center gap-3 p-4 border border-dashed border-[#d6b35a]/40 rounded-lg bg-[#fffaf2] dark:bg-[#1d2521]">
                <div class="w-full overflow-hidden rounded-lg bg-[#f1ece3] dark:bg-[#28322e] flex items-center justify-center relative" style="height: 180px;">
                    <img v-if="imagePreview" :src="imagePreview" class="w-full object-cover" style="height: 180px; width: 100%; object-fit: cover;" />
                    <div v-else class="flex flex-col items-center justify-center text-[#7a6a58]">
                        <span class="material-symbols-outlined text-4xl mb-1">add_photo_alternate</span>
                        <span class="text-xs">No Image / Belum ada gambar</span>
                    </div>
                </div>
                <input type="file" @change="onFileChange" class="hidden" ref="imageInput" accept="image/*" />
                <button type="button" @click="($refs.imageInput as any).click()" class="px-4 py-2 text-xs font-bold rounded-full bg-[#3d2b1f] text-[#f7f2e8] hover:opacity-90">
                    Pilih Gambar Produk
                </button>
            </div>
        </label>

        <label class="gap-2 grid">
            <span
                class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]"
                >Category / Kategori</span
            >
            <select
                v-model="form.menu_category_id"
                class="min-h-11 rounded-lg px-4 bg-[#f1ece3] text-[#211b16] dark:bg-[#28322e] dark:text-[#f7f1e8]"
            >
                <option disabled value="">Pilih kategori</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>
        </label>
        <InputFields
            v-model="form.name"
            label="Product Name / Nama Produk"
            placeholder="Espresso"
        />
        <InputFields
            v-model="form.price"
            label="Price / Harga"
            type="number"
            placeholder="25000"
        />
        <InputFields
            v-model="form.cost_of_goods"
            label="COGS / HPP"
            type="number"
            placeholder="10000"
        />
        <InputFields
            v-model="form.prep_time_minutes"
            label="Prep Time / Waktu Racik"
            type="number"
            placeholder="5"
        />
        <label class="gap-2 grid">
            <span
                class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]"
                >Description / Deskripsi</span
            >
            <textarea
                v-model="form.description"
                rows="4"
                class="rounded-lg px-4 py-3 bg-[#f1ece3] text-[#211b16] dark:bg-[#28322e] dark:text-[#f7f1e8]"
            ></textarea>
        </label>
    </div>
</template>
