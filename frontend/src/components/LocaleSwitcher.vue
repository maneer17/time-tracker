<script setup>
import { useI18n } from 'vue-i18n'

defineProps({ flat: Boolean })

const { t, locale, availableLocales } = useI18n({ useScope: 'global' })
const labels = { en: 'EN', ar: 'عربي' }
</script>

<template>
  <div
    role="group"
    :aria-label="t('language.label')"
    class="flex items-center gap-1 p-1 rounded-full border"
    :class="flat ? 'bg-[#E5E1DD]/60 border-[#D9D4CE]' : 'bg-white/90 backdrop-blur shadow-lg border-black/5'"
  >
    <button
      v-for="l in availableLocales"
      :key="`locale-${l}`"
      type="button"
      :aria-pressed="locale === l"
      :lang="l"
      @click="locale = l"
      class="px-3 py-1 rounded-full text-xs font-bold transition-colors"
      :class="locale === l ? 'bg-[#083A4F] text-white' : 'text-[#3E5660] hover:bg-black/5'"
    >
      {{ labels[l] ?? l.toUpperCase() }}
    </button>
  </div>
</template>
