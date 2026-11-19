<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="t('about.title')" narrow>
        <div class="bg-white border border-[#D9D4CE] rounded-3xl p-6 space-y-4 text-[#3E5660] leading-relaxed">
            <p>{{ t('about.p1') }}</p>
            <p>{{ t('about.p2') }}</p>
            <p>{{ t('about.p3') }}</p>
        </div>
    </PageShell>
</template>
