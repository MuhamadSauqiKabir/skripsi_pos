<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import ImageUploadEditor from '@/components/ImageUploadEditor.vue';
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { update as updateProfile } from '@/actions/App/Http/Controllers/Management/ProfileController';

defineOptions({ layout: AppLayout });

const page = usePage<{
    auth: { user?: { id: number; name?: string; role?: string; email?: string; avatar?: string } };
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

const profileModal = ref(false);

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PATCH',
    })).post(updateProfile().url, {
        preserveScroll: true,
        onSuccess: () => {
            profileModal.value = false;
        },
    });
};

const initials = computed(() => {
    const name = user.value?.name || 'Admin';
    return name.split(' ').filter(Boolean).map((word) => word[0]).join('').slice(0, 2).toUpperCase();
});

const avatarPreviewUrl = computed(() =>
    user.value?.avatar ? `/storage/${user.value.avatar}` : null,
);
</script>

<template>
    <div class="space-y-5">
        <section class="rounded-lg p-5 sm:p-6 bg-[#fffaf2] dark:bg-[#1d2521]">
            <div
                class="gap-5 md:flex-row md:items-center md:justify-between flex flex-col"
            >
                <div>
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-[#9b8a72] uppercase"
                    >
                        Akun
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold">
                        Profil
                    </h2>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        v-if="canManage"
                        type="button"
                        class="inline-flex min-h-11 items-center gap-2 rounded-full bg-[#3d2b1f] px-5 text-sm font-bold text-[#f7f1e8]"
                        @click="profileModal = true"
                    >
                        <span class="material-symbols-outlined text-base">edit</span>
                        Ubah Profil
                    </button>
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
                                user?.role || 'staf'
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-6">
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-lg p-5 bg-[#f1ece3] dark:bg-[#28322e]">
                        <p class="text-xs font-bold tracking-[0.18em] text-[#9b8a72] uppercase">
                            Nama Lengkap
                        </p>
                        <strong class="mt-3 text-xl block">{{ user?.name }}</strong>
                    </div>
                    <div class="rounded-lg p-5 bg-[#e5eddf] text-[#273528] dark:bg-[#24342b] dark:text-[#edf7e8]">
                        <p class="text-xs font-bold tracking-[0.18em] text-[#63745d] uppercase dark:text-[#c8d9c1]">
                            Alamat Email
                        </p>
                        <strong class="mt-3 text-xl block">{{ user?.email }}</strong>
                    </div>
                </div>

                <div class="rounded-lg border border-[#d6b35a]/20 p-5 bg-[#fdf6e3]/50 text-[#7a6a58]">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#d6b35a]">badge</span>
                        <div>
                            <strong class="block text-sm">Catatan Identitas</strong>
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

        <FormModal
            :open="profileModal"
            title="Ubah Data Diri"
            subtitle="Perbarui informasi profil Anda"
            @close="profileModal = false"
        >
            <form class="grid gap-5" @submit.prevent="submit">
                <ImageUploadEditor
                    v-model="form.avatar"
                    label="Foto Profil"
                    :preview-url="avatarPreviewUrl"
                    :aspect-ratio="1"
                    :output-width="512"
                    :output-height="512"
                    rounded-preview
                />
                <InputFields
                    v-model="form.name"
                    label="Nama Staf"
                    placeholder="Masukkan nama..."
                    :error="form.errors.name"
                />

                <FormModalActions>
                    <button
                        type="button"
                        class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                        @click="profileModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f1e8] disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Data Diri' }}
                    </button>
                </FormModalActions>
            </form>
        </FormModal>
    </div>
</template>
