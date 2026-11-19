<script setup>
import { computed, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import ExportChannel from './ExportChannel.vue'

const { t, locale } = useI18n()
const channel = inject('channel')

const formattedCreatedAt = computed(() => {
    // Using locale-aware formatting to match your AR/EN toggle
    return new Date(channel.value.created_at).toLocaleDateString(locale.value, {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    })
})
</script>

<template>
    <div class="space-y-4">
        <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 sm:p-6 space-y-4">
            <p v-if="channel.description" class="text-[#3E5660] leading-relaxed">{{ channel.description }}</p>
            <p v-else class="text-[#5F7480] text-sm">{{ t('channel_overview.no_description') }}</p>

            <div class="flex flex-wrap items-center gap-x-8 gap-y-2 pt-4 border-t border-[#D9D4CE] text-sm">
                <div>
                    <span class="text-2xl font-semibold tabular-nums">{{ channel.members_count }}</span>
                    <span class="text-[#5F7480] ms-1">{{ t('channel_overview.members') }}</span>
                </div>
                <div class="text-[#5F7480]">{{ t('channel_overview.created') }} {{ formattedCreatedAt }}</div>
            </div>
        </section>

        <ExportChannel :channel="channel" />
    </div>
</template>
