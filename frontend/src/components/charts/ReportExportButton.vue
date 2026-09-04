<script setup>
import { watch } from 'vue'
import useExport from '@/composables/useExport'
import exportService from '@/services/exportService'

const props = defineProps({
    from:           { type: String, required: true },
    to:             { type: String, required: true },
    captureCharts:  { type: Function, required: true },
    capturedCharts: { type: Object, default: null },
})
// defineProps declares what this component accepts from its parent.
// type: Function — yes, you can pass functions as props in Vue.
// This is how the child triggers something in the parent —
// parent passes a function, child calls it when needed.

const { loading, error, download } = useExport()

const handleExport = () => {
    props.captureCharts()
    // Call the function the parent gave us.
    // This runs captureCharts() in ReportsPage.vue,
    // which fills capturedCharts.value with the base64 strings.
}

watch(() => props.capturedCharts, (charts) => {
    // Watch for when capturedCharts changes in the parent.
    // When captureCharts() runs in the parent, it sets capturedCharts.value.
    // That change flows down to this component as a prop update.
    // This watch detects that update and triggers the actual download.

    if (!charts) return
    // Guard — if charts is still null, do nothing.

    download(
        exportService.exportReport,
        {
            from: props.from,
            to:   props.to,
            ...charts,
            // spread operator — expands the object into individual keys:
            // { total_time_chart: '...', most_used_chart: '...', avg_time_chart: '...' }
            // becomes three separate keys in the payload
        },
        'report'
        // filename — useExport will append '.pdf' via the ?? 'pdf' fallback
        // since we don't send file_extension in this payload
    )
})
// Why watch instead of calling download directly in handleExport?
// Because captureCharts() is async in nature — it updates the parent's ref.
// That ref change flows back down as a prop on the next Vue tick.
// We need to wait for that prop to arrive before we can send it.
// watch() does exactly that — it fires when the prop changes.
</script>

<template>
    <div>
        <button
            @click="handleExport"
            :disabled="loading"
            class="px-8 py-3 bg-white border-2 border-[#E07A5F] text-[#E07A5F] 
                   hover:bg-[#E07A5F] hover:text-white text-[13px] font-black 
                   uppercase tracking-widest rounded-2xl disabled:opacity-50 
                   transition shadow-sm active:scale-95">

            <span v-if="!loading">
                📄 Export PDF
            </span>

            <span v-else class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                Exporting...
            </span>

        </button>

        <p v-if="error" class="text-xs text-[#AF4E4E] mt-2">{{ error }}</p>
    </div>
</template>