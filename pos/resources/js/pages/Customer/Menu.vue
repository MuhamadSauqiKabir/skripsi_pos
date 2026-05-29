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
        <section>
            <!-- Compact Header -->
            <header class="mb-8 overflow-hidden rounded-2xl border border-brand-gold/10 bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">Pesan dari meja</p>
                        <h2 class="mt-1 font-serif text-3xl font-bold text-brand-espresso">{{ table.name }}</h2>
                        <p class="mt-1 text-xs text-brand-bronze/70">Silakan pilih menu favorit Anda di bawah ini.</p>
                    </div>
                    <div class="hidden h-16 w-16 items-center justify-center rounded-xl bg-brand-cream text-brand-gold shadow-inner md:flex">
                        <span class="material-symbols-outlined text-4xl">coffee</span>
                    </div>
                </div>
            </header>

            <!-- Menu Groups -->
            <div v-for="category in categories" :key="category.id" class="mb-10">
                <div class="mb-4 flex items-center gap-3">
                    <h3 class="font-serif text-xl font-bold text-brand-espresso">{{ category.name }}</h3>
                    <div class="h-px flex-1 bg-brand-gold/10"></div>
                </div>

                <div class="menu-grid">
                    <article v-for="menu in category.menu_items" :key="menu.id" class="client-menu-card group">
                        <div class="relative h-48 overflow-hidden">
                            <img v-if="menu.image_url" :src="menu.image_url" :alt="menu.name" class="menu-card-image" />
                            <div v-else class="flex h-full w-full items-center justify-center bg-brand-cream/50 text-brand-gold/30">
                                <span class="material-symbols-outlined text-6xl">image</span>
                            </div>
                            <!-- Price Tag on Image -->
                            <div class="absolute bottom-4 left-4 rounded-xl bg-white/90 px-3 py-2 text-sm font-bold text-brand-espresso backdrop-blur-sm shadow-sm">
                                Rp {{ Number(menu.price).toLocaleString('id-ID') }}
                            </div>
                        </div>

                        <div class="menu-card-content">
                            <div>
                                <h4 class="font-serif text-xl font-bold text-brand-espresso">{{ menu.name }}</h4>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-brand-bronze/70">{{ menu.description }}</p>
                            </div>
                            
                            <div class="menu-card-footer">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">
                                    {{ menu.is_featured ? 'Rekomendasi' : 'Disajikan Segar' }}
                                </span>
                                <div class="qty-box">
                                    <button type="button" @click="reduce(menu.id)" class="active:scale-90">-</button>
                                    <span>{{ qty(menu.id) }}</span>
                                    <button type="button" @click="upsert(menu)" class="active:scale-90">+</button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <aside class="customer-side-card rounded-2xl !p-6 shadow-md">
            <p class="text-[10px] font-bold uppercase tracking-widest text-brand-gold">Pesanan Anda</p>
            <h3 class="mt-1 font-serif text-xl font-bold text-brand-espresso">{{ totalItems() }} Item</h3>
            
            <div class="mt-6 space-y-3">
                <div v-for="item in cart" :key="item.menu_item_id" class="flex items-center justify-between border-b border-brand-gold/5 pb-2">
                    <p class="text-xs font-bold text-brand-espresso">{{ item.name }}</p>
                    <span class="text-[10px] font-bold text-brand-coffee">x{{ item.quantity }}</span>
                </div>
                
                <p v-if="cart.length === 0" class="py-8 text-center text-xs italic text-brand-bronze/40">
                    Belum ada menu...
                </p>
            </div>

            <div v-if="cart.length > 0" class="mt-8 space-y-3 border-t border-brand-gold/10 pt-4">
                <div class="flex items-center justify-between text-brand-espresso">
                    <span class="text-xs">Subtotal</span>
                    <strong class="text-base">Rp {{ Number(cart.reduce((s, i) => s + (i.price * i.quantity), 0)).toLocaleString('id-ID') }}</strong>
                </div>
                <Link class="primary-button w-full rounded-xl shadow-md transition-transform active:scale-95 !min-h-[42px] text-sm" :href="`/qr/${table.public_token}/checkout`">
                    Lanjut Bayar
                </Link>
            </div>
        </aside>

        <!-- Mobile Floating Cart -->
        <div v-if="totalItems() > 0" class="cart-floating-trigger">
            <Link :href="`/qr/${table.public_token}/checkout`" class="primary-button flex w-full items-center justify-between rounded-2xl p-6 shadow-2xl shadow-brand-gold/40">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-xs font-bold">{{ totalItems() }}</span>
                    <span class="text-sm font-bold uppercase tracking-wider">Lihat Keranjang</span>
                </div>
                <span class="font-serif text-lg font-bold">Rp {{ Number(cart.reduce((s, i) => s + (i.price * i.quantity), 0)).toLocaleString('id-ID') }}</span>
            </Link>
        </div>
    </div>
</template>
