<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    data: { type: Object, required: true },
})
const emit = defineEmits(['page-change'])
const { t } = useI18n()

// Accepts a Laravel resource paginator ({ data, links, meta }) or a bare paginator.
const meta = computed(() => props.data.meta ?? props.data)
const current = computed(() => meta.value.current_page ?? 1)
const last = computed(() => meta.value.last_page ?? 1)

// 1 … 4 5 [6] 7 8 … 20
const pages = computed(() => {
    const total = last.value
    const shown = new Set([1, total, ...Array.from({ length: 5 }, (_, i) => current.value - 2 + i)])
    const sorted = [...shown].filter((p) => p >= 1 && p <= total).sort((a, b) => a - b)

    return sorted.flatMap((page, i) => (i > 0 && page - sorted[i - 1] > 1 ? ['…', page] : [page]))
})

const go = (page) => {
    if (page >= 1 && page <= last.value && page !== current.value) emit('page-change', page)
}

const edge = 'relative inline-flex items-center px-3 py-2 text-sm font-medium border border-[#D9D4CE] bg-white text-[#3E5660] hover:bg-[#EFECE8] disabled:opacity-40 disabled:cursor-not-allowed transition-colors'
</script>

<template>
    <nav v-if="last > 1" :aria-label="t('pagination.label')" class="flex justify-center">
        <ul class="isolate inline-flex -space-x-px rounded-xl overflow-hidden">
            <li>
                <button type="button" :class="[edge, 'rounded-s-xl']" :disabled="current <= 1" @click="go(current - 1)">
                    <span class="sr-only">{{ t('pagination.previous') }}</span>
                    <!-- chevron points toward "previous": left in LTR, right in RTL -->
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                </button>
            </li>

            <li v-for="(page, i) in pages" :key="`${page}-${i}`">
                <span v-if="page === '…'" :class="[edge, 'cursor-default hover:!bg-white']">…</span>
                <button
                    v-else
                    type="button"
                    :aria-current="page === current ? 'page' : undefined"
                    :class="[edge, page === current ? 'z-10 !border-[#083A4F] !bg-[#C0D5D6] !text-[#083A4F] hover:!bg-[#C0D5D6]' : '']"
                    @click="go(page)"
                >
                    {{ page }}
                </button>
            </li>

            <li>
                <button type="button" :class="[edge, 'rounded-e-xl']" :disabled="current >= last" @click="go(current + 1)">
                    <span class="sr-only">{{ t('pagination.next') }}</span>
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </button>
            </li>
        </ul>
    </nav>
</template>
