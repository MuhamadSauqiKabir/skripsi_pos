<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import FormTabel from '@/components/FormTabel.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

defineProps<{
    tables: Array<any>;
    tableEfficiency: Array<any>;
}>();

const tableModal = ref(false);
const editingTable = ref<any>(null);

const tableForm = useForm({
    name: '',
    capacity: 2,
    floor: 1,
    coordinate_x: 0,
    coordinate_y: 0,
    is_active: true,
});

const openCreateModal = () => {
    editingTable.value = null;
    tableForm.reset();
    tableModal.value = true;
};

const openEditModal = (table: any) => {
    editingTable.value = table;
    tableForm.name = table.name;
    tableForm.capacity = table.capacity;
    tableForm.floor = table.floor || 1;
    tableForm.coordinate_x = table.coordinate_x;
    tableForm.coordinate_y = table.coordinate_y;
    tableForm.is_active = table.is_active;
    tableModal.value = true;
};

const submitTable = () => {
    if (editingTable.value) {
        tableForm.patch(`/management/tables/${editingTable.value.id}`, {
            preserveScroll: true,
            onSuccess: () => (tableModal.value = false),
        });
    } else {
        tableForm.post('/management/tables', {
            preserveScroll: true,
            onSuccess: () => (tableModal.value = false),
        });
    }
};

const deleteTable = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus meja ini?')) {
        useForm({}).delete(`/management/tables/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="gap-5 xl:grid-cols-[360px_minmax(0,1fr)] grid">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <p
                class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
            >
                Ruang Makan
            </p>
            <h2 class="mt-2 font-serif text-2xl font-bold">
                Kontrol Meja
            </h2>
            <p
                class="mt-3 text-sm leading-6 text-[#6d6255] dark:text-[#c8bdaa]"
            >
                Kelola QR meja, kapasitas, dan koordinat untuk pengalaman pesan
                mandiri.
            </p>
            <button
                type="button"
                class="mt-6 min-h-11 gap-2 px-5 text-sm font-bold inline-flex items-center rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                @click="openCreateModal"
            >
                <span class="material-symbols-outlined text-base">add</span>
                Tambah Meja
            </button>
        </section>

        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div class="gap-4 flex items-center justify-between">
                <h2 class="font-serif text-2xl font-bold">Meja</h2>
                <span
                    class="px-3 py-1 text-xs font-bold rounded-full bg-[#efe8da] tracking-[0.16em] text-[#6d6255] uppercase dark:bg-[#28322e] dark:text-[#c8bdaa]"
                >
                    {{ tables.length }} Meja
                </span>
            </div>
            <div class="mt-6 gap-3 md:grid-cols-2 grid">
                <article
                    v-for="table in tables"
                    :key="table.id"
                    class="group relative rounded-lg p-5 transition-all bg-[#f1ece3] hover:bg-[#efe8da] dark:bg-[#28322e] dark:hover:bg-[#2e3a35]"
                >
                    <div class="gap-4 flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold">{{ table.name }}</h3>
                                <span class="rounded-full bg-[#3d2b1f]/10 px-2 py-0.5 text-[10px] font-bold text-[#3d2b1f] dark:bg-[#f7f1e8]/10 dark:text-[#f7f1e8]">Lantai {{ table.floor || 1 }}</span>
                                <span v-if="!table.is_active" class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase text-red-600">Nonaktif</span>
                            </div>
                            <p
                                class="mt-1 text-sm text-[#6d6255] dark:text-[#c8bdaa]"
                            >
                                Kapasitas {{ table.capacity }} | ({{
                                    table.coordinate_x
                                }}, {{ table.coordinate_y }})
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                             <a
                                :href="`/qr/${table.public_token}`"
                                target="_blank"
                                class="h-9 w-9 flex items-center justify-center rounded-full bg-[#3d2b1f] text-[#f7f1e8] transition-transform hover:scale-110"
                                title="Buka QR"
                            >
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                            <button
                                class="h-9 w-9 flex items-center justify-center rounded-full bg-[#f1ece3] border border-[#3d2b1f]/20 text-[#3d2b1f] transition-transform hover:scale-110 dark:bg-[#1d2521] dark:text-[#f7f1e8]"
                                @click="openEditModal(table)"
                                title="Ubah meja"
                            >
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button
                                class="h-9 w-9 flex items-center justify-center rounded-full bg-red-50 border border-red-200 text-red-600 transition-transform hover:scale-110"
                                @click="deleteTable(table.id)"
                                title="Hapus meja"
                            >
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div class="mt-6 rounded-lg p-5 bg-[#e5eddf] dark:bg-[#24342b]">
                <h3
                    class="text-sm font-bold tracking-[0.18em] text-[#63745d] uppercase dark:text-[#c8d9c1]"
                >
                    Efisiensi Meja
                </h3>
                <div class="mt-4 gap-3 grid">
                    <div
                        v-for="table in tableEfficiency"
                        :key="table.name"
                        class="gap-1 rounded-lg px-4 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center grid bg-[#fffaf2] dark:bg-[#1d2521]"
                    >
                        <strong>{{ table.name }}</strong>
                        <span class="text-sm text-[#6d6255] dark:text-[#c8bdaa]"
                            >{{ table.order_count }} pesanan |
                            {{ table.efficiency }}%</span
                        >
                    </div>
                </div>
            </div>
        </section>

        <FormModal
            :open="tableModal"
            :title="editingTable ? 'Ubah Meja' : 'Tambah Meja'"
            :subtitle="editingTable ? 'Perbarui data meja yang sudah ada' : 'Buat QR dan data meja baru'"
            @close="tableModal = false"
        >
            <FormTabel :form="tableForm" :is-edit="!!editingTable" />
            <FormModalActions>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                    @click="tableModal = false"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                    @click="submitTable"
                >
                    {{ editingTable ? 'Perbarui' : 'Simpan' }}
                </button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
