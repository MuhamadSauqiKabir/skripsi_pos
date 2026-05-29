<script setup lang="ts">
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
const avatarPreview = ref<string | null>(null);

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
    avatarPreview.value = null;
    form.reset();
    form.clearErrors();
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
    avatarPreview.value = user.avatar ? `/storage/${user.avatar}` : null;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
};

const onFileChange = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
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
                avatarPreview.value = null;
            },
        });
    } else {
        form.post(storeUser().url, {
            onSuccess: () => {
                form.reset();
                avatarPreview.value = null;
            },
        });
    }
};

const deleteUser = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
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
                        Access Management
                    </p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-[#3d2b1f]">
                        Staff Profile / Profil Staf
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

        <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
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
                                <th class="pb-4 font-bold uppercase tracking-wider">Role</th>
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
                                            Edit
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

            <!-- Form Section -->
            <section class="rounded-lg bg-[#fffaf2] p-6 h-fit sticky top-6">
                <h3 class="font-serif text-2xl font-bold text-[#3d2b1f]">
                    {{ isEditing ? 'Edit Profile Staf' : 'Tambah Staf Baru' }}
                </h3>
                <form @submit.prevent="submit" class="mt-6 space-y-5">
                    <!-- Photo Upload -->
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-24 w-24 overflow-hidden rounded-full border-2 border-[#3d2b1f]/10 bg-[#f1ece3]">
                            <img v-if="avatarPreview" :src="avatarPreview" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-3xl">add_a_photo</span>
                            </div>
                        </div>
                        <input type="file" @change="onFileChange" class="hidden" ref="fileInput" accept="image/*" />
                        <button type="button" @click="($refs.fileInput as any).click()" class="text-xs font-bold text-[#3d2b1f] hover:underline">
                            Pilih Foto Profil
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Nama Lengkap</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                                placeholder="Contoh: Ahmad"
                            />
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Role / Jabatan</label>
                            <select
                                v-model="form.role"
                                class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">{{ role.label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">No. Telepon</label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                                placeholder="08..."
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Jenis Kelamin</label>
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" v-model="form.gender" value="Male" class="text-[#3d2b1f]"> Laki-laki
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" v-model="form.gender" value="Female" class="text-[#3d2b1f]"> Perempuan
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Alamat</label>
                        <textarea
                            v-model="form.address"
                            rows="2"
                            class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                            placeholder="Alamat lengkap..."
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Bio / Catatan</label>
                        <textarea
                            v-model="form.bio"
                            rows="2"
                            class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                            placeholder="Keterangan tambahan..."
                        ></textarea>
                    </div>

                    <div class="space-y-4 pt-2 border-t border-[#ece4d9]">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#9b8a72]">
                            {{ isEditing ? 'Kosongkan jika tidak ganti password' : 'Keamanan Akun' }}
                        </p>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Password</label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#7a6a58]">Konfirmasi</label>
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-2 w-full rounded-lg border-[#ece4d9] bg-[#fdfaf5] p-3 text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-lg bg-[#3d2b1f] p-4 text-sm font-bold text-[#f7f2e8] transition-opacity hover:opacity-90 disabled:opacity-50"
                    >
                        {{ isEditing ? 'Update Profile Staf' : 'Simpan Data Staf' }}
                    </button>
                    
                    <button
                        v-if="isEditing"
                        @click="isEditing = false; form.reset()"
                        type="button"
                        class="w-full text-sm font-bold text-[#7a6a58] hover:underline"
                    >
                        Batal
                    </button>
                </form>
            </section>
        </div>
    </div>
</template>
