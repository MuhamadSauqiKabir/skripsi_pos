<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';

defineProps<{ form: any }>();
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2">
        <InputFields v-model="form.name" label="Nama Table / Nama Meja" placeholder="A1" />
        <InputFields v-model="form.capacity" label="Capacity / Kapasitas" type="number" placeholder="4" />
        <InputFields v-model="form.coordinate_x" label="Coordinate X / Koordinat X" type="number" placeholder="12" />
        <InputFields v-model="form.coordinate_y" label="Coordinate Y / Koordinat Y" type="number" placeholder="20" />
    </div>
</template>
