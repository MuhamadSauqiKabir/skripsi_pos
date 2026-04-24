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
    <label class="grid gap-2">
        <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ label }}</span>
        <input
            :type="type || 'text'"
            :value="modelValue"
            :placeholder="placeholder"
            class="rounded-2xl border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800"
            @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        />
    </label>
</template>
