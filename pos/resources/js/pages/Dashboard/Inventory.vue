<script setup lang="ts">
import FormCategories from '@/components/FormCategories.vue';
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import FormProducts from '@/components/FormProducts.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    categories: Array<any>;
    menuItems: Array<any>;
    ingredients: Array<any>;
    alerts: Array<any>;
}>();

const productModal = ref(false);
const categoryModal = ref(false);

const menuForm = useForm({
    menu_category_id: '',
    name: '',
    description: '',
    price: '',
    cost_of_goods: '',
    prep_time_minutes: 7,
});

const categoryForm = useForm({
    name: '',
    description: '',
});

const createMenu = () => menuForm.post('/management/menu-items', { preserveScroll: true, onSuccess: () => (productModal.value = false) });
const toggleMenu = (id: number) => router.patch(`/management/menu-items/${id}/availability`, {}, { preserveScroll: true });
const adjustInventory = (id: number, type: string) =>
    router.post(`/management/inventory/${id}/adjust`, { type, quantity: 10, notes: `Quick ${type}` }, { preserveScroll: true });
</script>

<template>
    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-2xl font-bold">Products / Produk</h2>
                <div class="flex gap-2">
                    <button type="button" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="productModal = true">Add Product</button>
                    <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="categoryModal = true">Add Category</button>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div v-for="item in menuItems" :key="item.id" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                    <div>
                        <strong class="block">{{ item.name }}</strong>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ item.category?.name }}</span>
                    </div>
                    <button class="rounded-2xl bg-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] dark:bg-slate-700" @click="toggleMenu(item.id)">
                        {{ item.is_available ? 'Disable / Nonaktif' : 'Enable / Aktif' }}
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 dark:bg-slate-900">
            <h2 class="font-serif text-2xl font-bold">Inventory / Stok</h2>
            <div class="mt-6 space-y-3">
                <div v-for="ingredient in ingredients" :key="ingredient.id" class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                    <div>
                        <strong class="block">{{ ingredient.name }}</strong>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ ingredient.stock }} {{ ingredient.unit }} | par {{ ingredient.par_stock }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="rounded-2xl bg-emerald-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white" @click="adjustInventory(ingredient.id, 'restock')">Restock</button>
                        <button class="rounded-2xl bg-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white" @click="adjustInventory(ingredient.id, 'waste')">Waste</button>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-3xl bg-rose-50 p-5 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300">
                <h3 class="text-sm font-bold uppercase tracking-[0.18em]">Low Stock / Stok Menipis</h3>
                <div class="mt-4 space-y-2">
                    <div v-for="alert in alerts" :key="alert.id" class="rounded-2xl bg-white/70 px-4 py-3 dark:bg-slate-900/30">
                        <strong>{{ alert.ingredient?.name }}</strong> - {{ alert.message }}
                    </div>
                </div>
            </div>
        </section>

        <FormModal :open="productModal" title="FormProducts.vue" subtitle="Add Product / Tambah Produk" @close="productModal = false">
            <FormProducts :form="menuForm" :categories="categories" />
            <FormModalActions>
                <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="productModal = false">Cancel</button>
                <button type="button" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-slate-100 dark:text-slate-900" @click="createMenu">Save</button>
            </FormModalActions>
        </FormModal>

        <FormModal :open="categoryModal" title="FormCategories.vue" subtitle="Add Category / Tambah Kategori" @close="categoryModal = false">
            <FormCategories :form="categoryForm" />
            <FormModalActions>
                <button type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold dark:bg-slate-800" @click="categoryModal = false">Close</button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
