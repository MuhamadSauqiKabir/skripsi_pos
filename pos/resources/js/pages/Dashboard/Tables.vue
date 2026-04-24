<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import FormTabel from '@/components/FormTabel.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    tables: Array<any>;
    tableEfficiency: Array<any>;
}>();

const tableModal = ref(false);
const tableForm = useForm({
    name: '',
    capacity: 2,
    coordinate_x: 0,
    coordinate_y: 0,
});

const createTable = () => tableForm.post('/management/tables', { preserveScroll: true, onSuccess: () => (tableModal.value = false) });
</script>

<template>
    <div class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-2xl font-bold">Table Control / Kontrol Meja</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kelola QR table dan pantau efisiensi meja aktif.</p>
            <button type="button" class="mt-6 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="tableModal = true">
                Add Table / Tambah Meja
            </button>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-2xl font-bold">Tables / Meja</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <article v-for="table in tables" :key="table.id" class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/70">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold">{{ table.name }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Capacity {{ table.capacity }} | ({{ table.coordinate_x }}, {{ table.coordinate_y }})</p>
                        </div>
                        <a :href="`/qr/${table.public_token}`" target="_blank" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white dark:bg-slate-100 dark:text-slate-900">
                            Client
                        </a>
                    </div>
                </article>
            </div>

            <div class="mt-6 rounded-3xl bg-slate-50 p-5 dark:bg-slate-800/70">
                <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-400">Table Efficiency / Efisiensi Meja</h3>
                <div class="mt-4 space-y-3">
                    <div v-for="table in tableEfficiency" :key="table.name" class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900/60">
                        <strong>{{ table.name }}</strong>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ table.order_count }} order | {{ table.efficiency }}%</span>
                    </div>
                </div>
            </div>
        </section>

        <FormModal :open="tableModal" title="FormTabel.vue" subtitle="Add Table / Tambah Meja" @close="tableModal = false">
            <FormTabel :form="tableForm" />
            <FormModalActions>
                <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="tableModal = false">Cancel</button>
                <button type="button" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="createTable">Save</button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
