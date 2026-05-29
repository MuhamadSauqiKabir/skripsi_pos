<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';
import FormModalActions from '@/components/FormModalActions.vue';
import ImageUploadEditor from '@/components/ImageUploadEditor.vue';
import InputFields from '@/components/InputFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { store as storeUser, update as updateUser, destroy as destroyUser } from '@/actions/App/Http/Controllers/Management/UserController';

defineOptions({ layout: AppLayout });

type User = {
    id: number;
    name: string;
    email: string;
    role: string;
    phone?: string;
    address?: string;
    gender?: string;
    bio?: string;
    avatar?: string;
};

type RoleOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    users: {
        data: User[];
        links: any[];
    };
    roles: RoleOption[];
}>();

const isEditing = ref(false);
const editingUserId = ref<number | null>(null);
const userModal = ref(false);
const existingAvatarUrl = ref<string | null>(null);

const form = useForm({
    name: '',
    email: '',
    role: 'employee',
    phone: '',
    address: '',
    gender: 'Male',
    bio: '',
    avatar: null as any,
    password: '',
    password_confirmation: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    editingUserId.value = null;
    existingAvatarUrl.value = null;
    form.reset();
    form.clearErrors();
    userModal.value = true;
};

const openEditModal = (user: User) => {
    isEditing.value = true;
    editingUserId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.phone = user.phone || '';
    form.address = user.address || '';
    form.gender = user.gender || 'Male';
    form.bio = user.bio || '';
    form.avatar = null;
    existingAvatarUrl.value = user.avatar ? `/storage/${user.avatar}` : null;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    userModal.value = true;
};

const submit = () => {
    if (isEditing.value && editingUserId.value) {
        // Use post with _method patch for file uploads
        form.transform((data) => ({
            ...data,
            _method: 'PATCH',
        })).post(updateUser(editingUserId.value).url, {
            onSuccess: () => {
                form.reset('password', 'password_confirmation');
                existingAvatarUrl.value = null;
                userModal.value = false;
            },
        });
    } else {
        form.post(storeUser().url, {
            onSuccess: () => {
                form.reset();
                existingAvatarUrl.value = null;
                userModal.value = false;
            },
        });
    }
};

const deleteUser = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus staf ini?')) {
        router.delete(destroyUser(id).url);
    }
};

const getRoleLabel = (roleValue: string) => {
    return props.roles.find(r => r.value === roleValue)?.label || roleValue;
};
</script>

<template>
    <div class="users-page space-y-6">
        <section class="rounded-lg bg-[#f7f2e8] p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#7a6a58]">
                        Manajemen Akses
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-[#3d2b1f]">
                        Profil Staf
                    </h2>
                </div>
                <button
                    @click="openCreateModal"
                    class="rounded-lg bg-[#3d2b1f] px-6 py-3 text-sm font-bold text-[#f7f2e8] transition-colors hover:bg-[#523a28]"
                >
                    Tambah Profil Staf Baru
                </button>
            </div>
        </section>

        <div>
            <!-- List Section -->
            <section class="rounded-lg bg-[#fffaf2] p-6">
                <h3 class="font-serif text-2xl font-bold text-[#3d2b1f]">
                    Daftar Profil Staf
                </h3>
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-[#ece4d9] text-[#7a6a58]">
                                <th class="pb-4 font-bold uppercase tracking-wider">Nama</th>
                                <th class="pb-4 font-bold uppercase tracking-wider">Email</th>
                                <th class="pb-4 font-bold uppercase tracking-wider">Jabatan</th>
                                <th class="pb-4 font-bold uppercase tracking-wider">No. Telepon</th>
                                <th class="pb-4 font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ece4d9]">
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="text-[#3d2b1f]"
                            >
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full bg-[#f1ece3]">
                                            <img v-if="user.avatar" :src="`/storage/${user.avatar}`" class="h-full w-full object-cover" />
                                            <div v-else class="flex h-full w-full items-center justify-center text-xs font-bold text-[#7a6a58]">
                                                {{ user.name.slice(0, 2).toUpperCase() }}
                                            </div>
                                        </div>
                                        <span class="font-medium">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 text-[#665d51]">{{ user.email }}</td>
                                <td class="py-4">
                                    <span class="inline-flex rounded-full bg-[#f1ece3] px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#7a6a58]">
                                        {{ getRoleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="py-4 text-[#665d51]">{{ user.phone || '-' }}</td>
                                <td class="py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button
                                            @click="openEditModal(user)"
                                            class="text-sm font-bold text-[#3d2b1f] hover:underline"
                                        >
                                            Ubah
                                        </button>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="text-sm font-bold text-red-600 hover:underline"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Simple Pagination -->
                <div class="mt-6 flex gap-2">
                    <component
                        :is="link.url ? 'Link' : 'span'"
                        v-for="link in users.links"
                        :key="link.label"
                        :href="link.url"
                        v-html="link.label"
                        class="px-3 py-1 rounded-md text-sm"
                        :class="{
                            'bg-[#3d2b1f] text-white': link.active,
                            'text-[#665d51]': !link.active && link.url,
                            'text-gray-300': !link.url
                        }"
                    />
                </div>
            </section>

        </div>

        <FormModal
            :open="userModal"
            :title="isEditing ? 'Ubah Profil Staf' : 'Tambah Staf Baru'"
            :subtitle="isEditing ? 'Perbarui data staf yang sudah terdaftar' : 'Buat akun staf baru untuk dashboard'"
            @close="userModal = false"
        >
            <form class="grid gap-5" @submit.prevent="submit">
                <ImageUploadEditor
                    v-model="form.avatar"
                    label="Foto Profil"
                    :preview-url="existingAvatarUrl"
                    :aspect-ratio="1"
                    :output-width="512"
                    :output-height="512"
                    rounded-preview
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <InputFields
                        v-model="form.name"
                        label="Nama Lengkap"
                        placeholder="Contoh: Ahmad"
                        :error="form.errors.name"
                    />
                    <InputFields
                        v-model="form.email"
                        label="Email"
                        type="email"
                        placeholder="staf@nineties.id"
                        :error="form.errors.email"
                    />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2">
                        <span class="text-sm font-semibold text-[#5f5549]">Jabatan</span>
                        <select
                            v-model="form.role"
                            class="min-h-11 rounded-lg bg-[#f1ece3] px-4 text-[#211b16]"
                        >
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>
                    </label>
                    <InputFields
                        v-model="form.phone"
                        label="No. Telepon"
                        placeholder="08..."
                        :error="form.errors.phone"
                    />
                </div>

                <div>
                    <span class="block text-sm font-semibold text-[#5f5549]">Jenis Kelamin</span>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.gender" type="radio" value="Male" class="text-[#3d2b1f]" />
                            Laki-laki
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.gender" type="radio" value="Female" class="text-[#3d2b1f]" />
                            Perempuan
                        </label>
                    </div>
                </div>

                <label class="grid gap-2">
                    <span class="text-sm font-semibold text-[#5f5549]">Alamat</span>
                    <textarea
                        v-model="form.address"
                        rows="2"
                        class="rounded-lg bg-[#f1ece3] px-4 py-3 text-[#211b16]"
                        placeholder="Alamat lengkap..."
                    ></textarea>
                </label>

                <label class="grid gap-2">
                    <span class="text-sm font-semibold text-[#5f5549]">Catatan</span>
                    <textarea
                        v-model="form.bio"
                        rows="2"
                        class="rounded-lg bg-[#f1ece3] px-4 py-3 text-[#211b16]"
                        placeholder="Keterangan tambahan..."
                    ></textarea>
                </label>

                <div class="space-y-4 border-t border-[#ece4d9] pt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#9b8a72]">
                        {{ isEditing ? 'Kosongkan jika tidak mengganti kata sandi' : 'Keamanan Akun' }}
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <InputFields
                            v-model="form.password"
                            label="Kata Sandi"
                            type="password"
                            :error="form.errors.password"
                        />
                        <InputFields
                            v-model="form.password_confirmation"
                            label="Konfirmasi"
                            type="password"
                        />
                    </div>
                </div>

                <FormModalActions>
                    <button
                        type="button"
                        class="px-4 py-3 text-sm font-bold rounded-full bg-[#f1ece3] text-[#3d2b1f]"
                        @click="userModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-3 text-sm font-bold rounded-full bg-[#3d2b1f] text-[#f7f2e8] disabled:opacity-50"
                    >
                        {{ isEditing ? 'Perbarui Profil Staf' : 'Simpan Data Staf' }}
                    </button>
                </FormModalActions>
            </form>
        </FormModal>
    </div>
</template>
