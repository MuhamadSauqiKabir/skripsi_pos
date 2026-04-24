<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';

defineProps<{
    form: any;
    categories: Array<any>;
}>();
</script>

<template>
    <div class="grid gap-4">
        <label class="grid gap-2">
            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Category / Kategori</span>
            <select v-model="form.menu_category_id" class="rounded-2xl border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                <option disabled value="">Pilih kategori</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
        </label>
        <InputFields v-model="form.name" label="Product Name / Nama Produk" placeholder="Espresso" />
        <InputFields v-model="form.price" label="Price / Harga" type="number" placeholder="25000" />
        <InputFields v-model="form.cost_of_goods" label="COGS / HPP" type="number" placeholder="10000" />
        <InputFields v-model="form.prep_time_minutes" label="Prep Time / Waktu Racik" type="number" placeholder="5" />
        <label class="grid gap-2">
            <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Description / Deskripsi</span>
            <textarea v-model="form.description" rows="4" class="rounded-2xl border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800"></textarea>
        </label>
    </div>
</template>
