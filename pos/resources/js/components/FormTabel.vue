<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';

defineProps<{ 
    form: any;
    isEdit?: boolean;
}>();
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2">
        <InputFields v-model="form.name" label="Nama Table / Nama Meja" placeholder="A1" />
        <InputFields v-model="form.capacity" label="Capacity / Kapasitas" type="number" placeholder="4" />
        
        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#9b8a72]">Floor / Lantai</label>
            <select v-model="form.floor" class="w-full rounded-lg border border-[#3d2b1f]/20 bg-white p-3 text-sm focus:border-brand-gold focus:outline-none dark:bg-[#1d2521] dark:border-[#f7f1e8]/20 dark:text-[#f7f1e8]">
                <option :value="1">Lantai 1</option>
                <option :value="2">Lantai 2</option>
                <option :value="3">Lantai 3</option>
            </select>
        </div>

        <template v-if="!isEdit">
            <InputFields v-model="form.coordinate_x" label="Coordinate X / Koordinat X" type="number" placeholder="12" />
            <InputFields v-model="form.coordinate_y" label="Coordinate Y / Koordinat Y" type="number" placeholder="20" />
        </template>

        <div v-else class="col-span-2">
            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#9b8a72]">Status</label>
            <div class="flex items-center gap-4 rounded-lg bg-[#f1ece3] p-3 dark:bg-[#28322e]">
                <button 
                    type="button"
                    class="flex-1 rounded-md py-2 text-sm font-bold transition-all"
                    :class="form.is_active ? 'bg-[#3d2b1f] text-white' : 'text-[#3d2b1f]'"
                    @click="form.is_active = true"
                >
                    Active / Aktif
                </button>
                <button 
                    type="button"
                    class="flex-1 rounded-md py-2 text-sm font-bold transition-all"
                    :class="!form.is_active ? 'bg-red-600 text-white' : 'text-red-600'"
                    @click="form.is_active = false"
                >
                    Inactive / Nonaktif
                </button>
            </div>
        </div>
    </div>
</template>
