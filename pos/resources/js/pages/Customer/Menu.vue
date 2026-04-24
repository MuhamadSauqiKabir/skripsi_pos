<script setup lang="ts">
import { loadCart, saveCart } from '@/composables/useCustomerCart';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: CustomerLayout });

const props = defineProps<{
    table: any;
    categories: Array<any>;
}>();

const cart = ref(loadCart(props.table.public_token));

const upsert = (menu: any) => {
    const existing = cart.value.find((item) => item.menu_item_id === menu.id);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.value.push({
            menu_item_id: menu.id,
            name: menu.name,
            price: Number(menu.price),
            quantity: 1,
            notes: '',
        });
    }

    saveCart(props.table.public_token, cart.value);
};

const reduce = (menuId: number) => {
    const existing = cart.value.find((item) => item.menu_item_id === menuId);
    if (!existing) return;

    existing.quantity -= 1;
    if (existing.quantity <= 0) {
        cart.value = cart.value.filter((item) => item.menu_item_id !== menuId);
    }

    saveCart(props.table.public_token, cart.value);
};

const qty = (menuId: number) => cart.value.find((item) => item.menu_item_id === menuId)?.quantity || 0;
const totalItems = () => cart.value.reduce((sum, item) => sum + item.quantity, 0);
</script>

<template>
    <div class="customer-page-grid">
        <section class="customer-main-card">
            <p class="eyebrow">Menu Page</p>
            <h2>{{ table.name }}</h2>
            <p class="muted">Pilih menu terlebih dahulu, lalu lanjut ke halaman checkout.</p>

            <div v-for="category in categories" :key="category.id" class="menu-group">
                <div class="section-title">
                    <h3>{{ category.name }}</h3>
                    <span class="muted">{{ category.description }}</span>
                </div>

                <div class="menu-grid">
                    <article v-for="menu in category.menu_items" :key="menu.id" class="client-menu-card">
                        <div>
                            <strong>{{ menu.name }}</strong>
                            <p class="muted">{{ menu.description }}</p>
                        </div>
                        <div class="menu-card-footer">
                            <span>Rp {{ Number(menu.price).toLocaleString('id-ID') }}</span>
                            <div class="qty-box">
                                <button type="button" @click="reduce(menu.id)">-</button>
                                <span>{{ qty(menu.id) }}</span>
                                <button type="button" @click="upsert(menu)">+</button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <aside class="customer-side-card">
            <p class="eyebrow">Cart Summary</p>
            <h3>{{ totalItems() }} item dipilih</h3>
            <div class="list-block">
                <div v-for="item in cart" :key="item.menu_item_id" class="list-row">
                    <strong>{{ item.name }}</strong>
                    <span>x{{ item.quantity }}</span>
                </div>
                <p v-if="cart.length === 0" class="muted">Belum ada menu dipilih.</p>
            </div>
            <Link class="primary-button" :href="`/qr/${table.public_token}/checkout`">Lanjut Checkout</Link>
        </aside>
    </div>
</template>
