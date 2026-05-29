<script setup lang="ts">
import ImageUploadEditor from '@/components/ImageUploadEditor.vue';
import InputFields from '@/components/InputFields.vue';

defineProps<{
    form: any;
    categories: Array<any>;
    existingImageUrl?: string | null;
}>();
</script>

<template>
    <div class="gap-4 grid">
        <ImageUploadEditor
            v-model="form.image"
            label="Gambar Produk"
            :preview-url="existingImageUrl"
            :aspect-ratio="16 / 10"
            :output-width="1200"
        />

        <label class="gap-2 grid">
            <span
                class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]"
                >Kategori</span
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
            label="Nama Produk"
            placeholder="Espresso"
        />
        <InputFields
            v-model="form.price"
            label="Harga"
            type="number"
            placeholder="25000"
        />
        <InputFields
            v-model="form.cost_of_goods"
            label="HPP"
            type="number"
            placeholder="10000"
        />
        <InputFields
            v-model="form.prep_time_minutes"
            label="Waktu Racik"
            type="number"
            placeholder="5"
        />
        <label class="gap-2 grid">
            <span
                class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]"
                >Deskripsi</span
            >
            <textarea
                v-model="form.description"
                rows="4"
                class="rounded-lg px-4 py-3 bg-[#f1ece3] text-[#211b16] dark:bg-[#28322e] dark:text-[#f7f1e8]"
            ></textarea>
        </label>
    </div>
</template>
