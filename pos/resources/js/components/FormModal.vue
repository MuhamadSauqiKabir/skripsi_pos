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
    <div
        v-if="open"
        class="inset-0 bg-slate-950/40 px-4 fixed z-50 flex items-center justify-center"
    >
        <div
            class="max-w-2xl max-h-[90vh] overflow-y-auto rounded-lg p-6 w-full bg-[#fffaf2] dark:bg-[#1d2521]"
        >
            <div class="mb-5 gap-4 flex items-start justify-between">
                <div>
                    <h3 class="font-serif text-2xl font-bold">{{ title }}</h3>
                    <p
                        v-if="subtitle"
                        class="mt-1 text-sm text-[#6d6255] dark:text-[#c8bdaa]"
                    >
                        {{ subtitle }}
                    </p>
                </div>
                <button
                    type="button"
                    class="p-2 rounded-full bg-[#f1ece3] text-[#26312d] dark:bg-[#28322e] dark:text-[#f7f1e8]"
                    @click="emit('close')"
                >
                    <span class="material-symbols-outlined text-base"
                        >close</span
                    >
                </button>
            </div>
            <slot />
        </div>
    </div>
</template>
