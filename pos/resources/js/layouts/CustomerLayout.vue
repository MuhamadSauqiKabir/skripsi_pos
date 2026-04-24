<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage<{
    flash: { success?: string; error?: string };
}>();
</script>

<template>
    <div class="customer-shell">
        <header class="customer-topbar">
            <div>
                <p class="eyebrow">Nineties Coffee</p>
                <h1>Table Ordering</h1>
            </div>
        </header>

        <div v-if="page.props.flash.success" class="flash-success">
            {{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash.error" class="flash-error">
            {{ page.props.flash.error }}
        </div>

        <slot />
    </div>
</template>
