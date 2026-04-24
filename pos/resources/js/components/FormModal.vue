<script setup lang="ts">
defineProps<{
    open: boolean;
    title: string;
    subtitle?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4">
        <div class="w-full max-w-2xl rounded-3xl bg-white p-6 dark:bg-slate-900">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h3 class="font-serif text-2xl font-bold">{{ title }}</h3>
                    <p v-if="subtitle" class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ subtitle }}</p>
                </div>
                <button type="button" class="rounded-xl bg-slate-100 p-2 dark:bg-slate-800" @click="emit('close')">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <slot />
        </div>
    </div>
</template>
