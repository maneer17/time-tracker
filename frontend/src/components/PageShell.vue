<script setup>
// The standard page frame: a header bar with the title (and optional badge / actions) above a padded content area.
defineProps({ title: String, narrow: Boolean })
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-[#D9D4CE] px-4 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight truncate">{{ title }}</h1>
                <slot name="badge" />
            </div>
            <div class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </header>

        <main class="p-4 lg:p-8 flex flex-col gap-6 min-w-0 w-full" :class="{ 'max-w-3xl': narrow }">
            <slot />
        </main>
    </div>
</template>
