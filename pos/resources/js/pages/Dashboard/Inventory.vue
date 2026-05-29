<script setup lang="ts">
import FormCategories from '@/components/FormCategories.vue';
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import FormProducts from '@/components/FormProducts.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import { storeCategory, store, toggleAvailability } from '@/actions/App/Http/Controllers/Management/MenuController';
import { adjust } from '@/actions/App/Http/Controllers/Management/InventoryController';

defineOptions({ layout: AppLayout });

defineProps<{
    categories: Array<any>;
    menuItems: Array<any>;
    ingredients: Array<any>;
    alerts: Array<any>;
}>();

const productModal = ref(false);
const categoryModal = ref(false);
const editingMenuId = ref<number | null>(null);

const menuForm = useForm({
    menu_category_id: '',
    name: '',
    description: '',
    price: '',
    cost_of_goods: '',
    prep_time_minutes: 7,
    image: null as any,
});

const categoryForm = useForm({
    name: '',
    description: '',
});

const createCategory = () =>
    categoryForm.post(storeCategory().url, {
        preserveScroll: true,
        onSuccess: () => (categoryModal.value = false),
    });

const editMenu = (item: any) => {
    editingMenuId.value = item.id;
    menuForm.menu_category_id = item.menu_category_id;
    menuForm.name = item.name;
    menuForm.description = item.description || '';
    menuForm.price = item.price;
    menuForm.cost_of_goods = item.cost_of_goods;
    menuForm.prep_time_minutes = item.prep_time_minutes;
    menuForm.image = null;
    productModal.value = true;
};

const submitMenu = () => {
    if (editingMenuId.value) {
        menuForm.post(`/management/menu-items/${editingMenuId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                productModal.value = false;
                menuForm.reset();
                editingMenuId.value = null;
            },
        });
    } else {
        menuForm.post(store().url, {
            preserveScroll: true,
            onSuccess: () => {
                productModal.value = false;
                menuForm.reset();
            },
        });
    }
};
const toggleMenu = (id: number) =>
    router.patch(
        toggleAvailability(id).url,
        {},
        { preserveScroll: true },
    );
const adjustInventory = (id: number, type: string) =>
    router.post(
        adjust(id).url,
        { type, quantity: 10, notes: `Quick ${type}` },
        { preserveScroll: true },
    );
</script>

<template>
    <div class="gap-5 xl:grid-cols-2 grid">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div
                class="gap-4 md:flex-row md:items-end md:justify-between flex flex-col"
            >
                <div>
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
                    >
                        Menu Catalog
                    </p>
                    <h2 class="mt-2 font-serif text-2xl font-bold">
                        Products / Produk
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 text-[#6d6255] dark:text-[#c8bdaa]"
                    >
                        Kelola daftar menu dan kategori yang tampil di POS.
                    </p>
                </div>
                <div class="gap-2 flex flex-wrap">
                    <button
                        type="button"
                        class="min-h-11 gap-2 px-4 text-sm font-bold inline-flex items-center rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                        @click="() => { editingMenuId = null; menuForm.reset(); productModal = true; }"
                    >
                        <span class="material-symbols-outlined text-base"
                            >add</span
                        >
                        Product
                    </button>
                    <button
                        type="button"
                        class="min-h-11 gap-2 px-4 text-sm font-bold inline-flex items-center rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                        @click="categoryModal = true"
                    >
                        <span class="material-symbols-outlined text-base"
                            >category</span
                        >
                        Category
                    </button>
                </div>
            </div>

            <div class="mt-6 gap-3 grid">
                <div
                    v-for="item in menuItems"
                    :key="item.id"
                    class="gap-3 rounded-lg px-4 py-4 sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:items-center grid bg-[#f1ece3] dark:bg-[#28322e]"
                >
                    <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-[#fffaf2] dark:bg-[#1d2521] border border-[#d6b35a]/20">
                        <img v-if="item.image_url" :src="item.image_url" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center text-[10px] font-bold text-[#7a6a58]">
                            NO IMG
                        </div>
                    </div>
                    <div class="min-w-0">
                        <strong class="block truncate">{{ item.name }}</strong>
                        <span
                            class="text-sm text-[#6d6255] dark:text-[#c8bdaa]"
                            >{{ item.category?.name || 'Tanpa kategori' }}</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="px-4 py-2 text-xs font-bold rounded-full border border-[#d6b35a] tracking-[0.14em] text-[#3d2b1f] uppercase dark:text-[#f7f1e8] hover:bg-[#d6b35a] hover:text-[#fffaf2] transition-colors"
                            @click="editMenu(item)"
                        >
                            Edit
                        </button>
                        <button
                            class="px-4 py-2 text-xs font-bold rounded-full bg-[#fffaf2] tracking-[0.14em] text-[#3d2b1f] uppercase dark:bg-[#1d2521] dark:text-[#f7f1e8]"
                            @click="toggleMenu(item.id)"
                        >
                            {{ item.is_available ? 'Disable / Nonaktif' : 'Enable / Aktif' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div class="gap-4 flex items-start justify-between">
                <div>
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
                    >
                        Stock Room
                    </p>
                    <h2 class="mt-2 font-serif text-2xl font-bold">
                        Inventory / Stok
                    </h2>
                </div>
                <span
                    class="px-3 py-1 text-xs font-bold rounded-full bg-[#e5eddf] tracking-[0.16em] text-[#63745d] uppercase dark:bg-[#24342b] dark:text-[#c8d9c1]"
                >
                    {{ ingredients.length }} Item
                </span>
            </div>

            <div class="mt-6 gap-3 grid">
                <div
                    v-for="ingredient in ingredients"
                    :key="ingredient.id"
                    class="gap-3 rounded-lg px-4 py-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center grid bg-[#f1ece3] dark:bg-[#28322e]"
                >
                    <div>
                        <strong class="block">{{ ingredient.name }}</strong>
                        <span
                            class="text-sm text-[#6d6255] dark:text-[#c8bdaa]"
                        >
                            {{ ingredient.stock }} {{ ingredient.unit }} | par
                            {{ ingredient.par_stock }}
                        </span>
                    </div>
                    <div class="gap-2 flex flex-wrap">
                        <button
                            class="px-4 py-2 text-xs font-bold text-[#f7f2e8] rounded-full bg-[#3d2b1f] tracking-[0.14em] uppercase transition-opacity hover:opacity-90"
                            @click="adjustInventory(ingredient.id, 'restock')"
                        >
                            Restock
                        </button>
                        <button
                            class="px-4 py-2 text-xs font-bold text-[#3d2b1f] rounded-full bg-[#f1ece3] border border-[#3d2b1f] tracking-[0.14em] uppercase transition-colors hover:bg-[#3d2b1f] hover:text-[#f7f2e8]"
                            @click="adjustInventory(ingredient.id, 'waste')"
                        >
                            Waste
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="mt-6 rounded-lg p-5 bg-[#f7f2e8] border border-[#d6b35a]/30 text-[#8a6d3b]"
            >
                <h3 class="text-xs font-bold tracking-[0.18em] uppercase text-[#7a6a58]">
                    Low Stock / Stok Menipis
                </h3>
                <div class="mt-4 gap-2 grid">
                    <div
                        v-for="alert in alerts"
                        :key="alert.id"
                        class="rounded-lg px-4 py-3 bg-[#fffaf2] dark:bg-[#1d2521]"
                    >
                        <strong>{{ alert.ingredient?.name }}</strong> -
                        {{ alert.message }}
                    </div>
                </div>
            </div>
        </section>

        <FormModal
            :open="productModal"
            :title="editingMenuId ? 'Edit Product / Ubah Produk' : 'Add Product / Tambah Produk'"
            subtitle="Lengkapi detail menu POS"
            @close="productModal = false"
        >
            <FormProducts :form="menuForm" :categories="categories" />
            <FormModalActions>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                    @click="productModal = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                    @click="submitMenu"
                >
                    Save
                </button>
            </FormModalActions>
        </FormModal>

        <FormModal
            :open="categoryModal"
            title="Add Category / Tambah Kategori"
            subtitle="Kelompokkan menu agar mudah dicari"
            @close="categoryModal = false"
        >
            <FormCategories :form="categoryForm" />
            <FormModalActions>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                    @click="categoryModal = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8]"
                    @click="createCategory"
                >
                    Save
                </button>
            </FormModalActions>
        </FormModal>
    </div>
</template>
