<script setup lang="ts">
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { update as updateProfile } from '@/actions/App/Http/Controllers/Management/ProfileController';

defineOptions({ layout: AppLayout });

const props = defineProps<{}>();

const page = usePage<{
    auth: { user?: { id: number; name?: string; role?: string; email?: string } };
}>();

const user = computed(() => page.props.auth.user);

// Cek apakah Admin atau Super Admin
const canManage = computed(() => {
    const role = user.value?.role || '';
    return ['super_admin', 'admin'].includes(role);
});

// Form untuk Profil Diri
const form = useForm({
    name: user.value?.name || '',
    avatar: null as any,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PATCH',
    })).post(updateProfile().url, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: reset file input or preview if needed
        },
    });
};

const initials = computed(() => {
    const name = user.value?.name || 'Admin';
    return name.split(' ').filter(Boolean).map((word) => word[0]).join('').slice(0, 2).toUpperCase();
});

const avatarPreview = ref<string | null>(null);
const onFileChange = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};
</script>

<template>
    <div class="gap-5 xl:grid-cols-[minmax(0,1fr)_380px] grid">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div
                class="gap-5 md:flex-row md:items-center md:justify-between flex flex-col"
            >
                <div>
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
                    >
                        Account
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold">
                        Profile / Profil
                    </h2>
                </div>
                <div
                    class="gap-3 rounded-lg px-4 py-3 flex items-center bg-[#f1ece3] dark:bg-[#28322e]"
                >
                    <div class="h-12 w-12 overflow-hidden rounded-full border border-white/20 bg-[#d6b35a]">
                        <img v-if="user?.avatar" :src="`/storage/${user.avatar}`" class="h-full w-full object-cover" />
                        <span v-else class="h-full w-full font-black flex items-center justify-center text-[#211b16]">
                            {{ initials }}
                        </span>
                    </div>
                    <div>
                        <strong class="block">{{
                            user?.name || 'Admin'
                        }}</strong>
                        <span class="text-sm text-[#6d6255] dark:text-[#c8bdaa]">{{
                            user?.role || 'staff'
                        }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-6">
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-lg p-5 bg-[#f1ece3] dark:bg-[#28322e]">
                        <p class="text-xs font-bold tracking-[0.18em] text-[#9b8a72] uppercase">
                            Full Name / Nama
                        </p>
                        <strong class="mt-3 text-xl block">{{ user?.name }}</strong>
                    </div>
                    <div class="rounded-lg p-5 bg-[#e5eddf] text-[#273528] dark:bg-[#24342b] dark:text-[#edf7e8]">
                        <p class="text-xs font-bold tracking-[0.18em] text-[#63745d] uppercase dark:text-[#c8d9c1]">
                            Email Address
                        </p>
                        <strong class="mt-3 text-xl block">{{ user?.email }}</strong>
                    </div>
                </div>

                <div class="rounded-lg border border-[#d6b35a]/20 p-5 bg-[#fdf6e3]/50 text-[#7a6a58]">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#d6b35a]">badge</span>
                        <div>
                            <strong class="block text-sm">Identity Note</strong>
                            <p class="mt-1 text-xs leading-relaxed">Nama di atas akan digunakan pada struk pesanan dan laporan aktivitas transaksi untuk keperluan audit internal.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesan untuk Pegawai -->
            <div v-if="!canManage" class="mt-8 rounded-lg border border-[#d6b35a]/20 p-5 bg-[#fdf6e3] text-[#7a6a58]">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#d6b35a]">info</span>
                    <div>
                        <strong class="block">Pemberitahuan</strong>
                        <p class="mt-1 text-sm leading-relaxed">Sesuai kebijakan sistem, pengelolaan data diri karyawan hanya dapat dilakukan oleh <strong>Admin</strong> atau <strong>Super Admin</strong>. Silakan hubungi atasan Anda jika terdapat kesalahan data.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Form Edit Khusus Admin & Super Admin -->
        <aside v-if="canManage" class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <h3 class="font-serif text-xl font-bold">Edit Profile / Data Diri</h3>
            <p class="mt-2 text-sm text-[#6d6255] dark:text-[#c8bdaa]">Perbarui informasi lengkap Anda di sini.</p>

            <form @submit.prevent="submit" class="mt-6 gap-5 grid">
                <div class="flex flex-col items-center gap-3">
                     <div class="h-24 w-24 overflow-hidden rounded-full border-2 border-[#3d2b1f]/10 bg-[#f1ece3]">
                        <img v-if="avatarPreview" :src="avatarPreview" class="h-full w-full object-cover" />
                        <img v-else-if="user?.avatar" :src="`/storage/${user.avatar}`" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                            <span class="material-symbols-outlined text-3xl">add_a_photo</span>
                        </div>
                    </div>
                    <input type="file" @change="onFileChange" class="hidden" ref="fileInput" accept="image/*" />
                    <button type="button" @click="($refs.fileInput as any).click()" class="text-xs font-bold text-[#3d2b1f] hover:underline">
                        Pilih Foto Baru
                    </button>
                </div>

                <InputFields
                    v-model="form.name"
                    label="Display Name / Nama Staf"
                    placeholder="Masukkan nama..."
                    :error="form.errors.name"
                />



                <button
                    type="submit"
                    class="mt-4 min-h-11 w-full font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8] disabled:opacity-50 transition-all hover:bg-[#2c1a0e]"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Saving...' : 'Save Data Diri' }}
                </button>
            </form>
        </aside>
    </div>
</template>
