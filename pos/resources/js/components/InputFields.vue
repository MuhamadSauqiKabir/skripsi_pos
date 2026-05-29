<script setup lang="ts">
defineProps<{
    modelValue: string | number;
    label: string;
    placeholder?: string;
    type?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string | number];
}>();
</script>

<template>
    <label class="gap-2 grid">
        <span
            class="text-sm font-semibold text-[#5f5549] dark:text-[#c8bdaa]"
            >{{ label }}</span
        >
        <input
            :type="type || 'text'"
            :value="modelValue"
            :placeholder="placeholder"
            class="min-h-11 rounded-lg px-4 bg-[#f1ece3] text-[#211b16] placeholder:text-[#9b8a72] dark:bg-[#28322e] dark:text-[#f7f1e8] dark:placeholder:text-[#9faea5]"
            @input="
                emit(
                    'update:modelValue',
                    ($event.target as HTMLInputElement).value,
                )
            "
        />
    </label>
</template>
