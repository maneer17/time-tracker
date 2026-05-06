<script setup>
import { computed, inject } from 'vue'
import { useI18n } from 'vue-i18n'

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
    <div class="max-w-3xl bg-white rounded-[3.5rem] p-10 shadow-[0_20px_60px_rgba(90,125,90,0.05)] border border-white">
        
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <div class="h-24 w-24 rounded-[2.2rem] bg-[#5A7D5A] flex-shrink-0 flex items-center justify-center text-white font-black text-4xl shadow-2xl shadow-[#5A7D5A]/20 uppercase">
                {{ channel.name.charAt(0) }}
            </div>

            <div class="flex-1 text-center md:text-left">
                <div class="mb-4">
                    <h1 class="text-4xl font-black text-[#4A4A4A] tracking-tight leading-none mb-2">
                        {{ channel.name }}
                    </h1>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#F9F7F2] rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A7D5A]"></span>
                        <p class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-widest">
                            {{ t('channel_overview.created') }} {{ formattedCreatedAt }}
                        </p>
                    </div>
                </div>

                <div class="relative">
                    <p v-if="channel.description" class="text-[#6B6B6B] text-lg leading-relaxed italic font-medium">
                        "{{ channel.description }}"
                    </p>
                    <p v-else class="text-[#A0A0A0] text-sm italic">
                        {{ t('channel_overview.no_description') }}
                    </p>
                </div>

                <div class="mt-8 pt-8 border-t border-[#F9F7F2] flex items-center justify-center md:justify-start gap-12">
                    <div class="group cursor-default">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-[#4A4A4A] group-hover:text-[#5A7D5A] transition-colors">
                                {{ channel.members_count }}
                            </span>
                            <span class="text-xs font-black text-[#D4A373] uppercase tracking-widest">
                                {{ t('channel_overview.members') }}
                            </span>
                        </div>
                        <div class="h-1 w-6 bg-[#D4E2D4] rounded-full mt-1 transition-all group-hover:w-full"></div>
                    </div>
                    
                    </div>
            </div>
        </div>
    </div>
</template>