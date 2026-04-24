<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';

defineProps<{ form: any }>();
</script>

<template>
    <div class="grid gap-4">
        <InputFields v-model="form.name" label="Category Name / Nama Kategori" placeholder="Coffee" />
        <InputFields v-model="form.description" label="Description / Deskripsi" placeholder="Kategori minuman kopi" />
    </div>
</template>
