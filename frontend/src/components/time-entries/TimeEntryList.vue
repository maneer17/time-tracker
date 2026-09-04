<script setup>
import SingleEntry from './SingleEntry.vue'
import ExportPanel from './ExportPanel.vue'
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'

const { t } = useI18n()
const props = defineProps(['entries'])

const showExportPanel = ref(false)
// Only the toggle state lives here.
// All export logic lives inside ExportPanel.
</script>

<template>
    <div class="flex flex-col gap-8 w-full">

        <!-- TOP BAR -->
        <div class="flex items-center gap-4 flex-wrap">

            <router-link :to="{ name: 'AddEntry' }"
                class="inline-flex items-center justify-center bg-[#5A7D5A] hover:bg-[#4a6b4a] 
                       text-white px-8 py-4 rounded-[1.5rem] transition-all duration-300 
                       shadow-sm hover:shadow-lg active:scale-95 text-sm font-bold tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                {{ t('home.add_new_entry') }}
            </router-link>

            <button
                @click="showExportPanel = !showExportPanel"
                class="inline-flex items-center gap-2 px-6 py-4 bg-white text-[#5A7D5A] 
                       border border-[#D4E2D4] rounded-[1.5rem] hover:bg-[#E8F0E8] 
                       transition-all duration-300 shadow-sm text-sm font-bold tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>

        </div>

        <!-- EXPORT PANEL — only mounts when showExportPanel is true -->
        <ExportPanel v-if="showExportPanel" />
        <!-- That's it. One line.
             All the form state, logic, and UI lives inside ExportPanel.
             TimeEntryList only decides WHEN to show it. -->

        <!-- ENTRIES LIST -->
        <div class="flex gap-8 w-full">
            <div class="flex-1 max-w-[800px] space-y-4">
                <div v-for="entry in entries" :key="entry.id"
                    class="transition-all duration-300 hover:translate-x-1">
                    <SingleEntry :entry="entry" />
                </div>
            </div>
        </div>

    </div>
</template>