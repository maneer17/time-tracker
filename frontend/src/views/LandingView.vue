<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import step1Img from '@/assets/landing/step-1.webp'
import step2Img from '@/assets/landing/step-2.webp'
import step3Img from '@/assets/landing/step-3.webp'
import deskImg from '@/assets/landing/desk.webp'

const { t } = useI18n()

const steps = [
  { title: 'landing.step1_title', text: 'landing.step1_text', img: step1Img },
  { title: 'landing.step2_title', text: 'landing.step2_text', img: step2Img },
  { title: 'landing.step3_title', text: 'landing.step3_text', img: step3Img },
]

// Fade-and-rise each .rise element the first time it scrolls into view.
let observer
onMounted(() => {
  observer = new IntersectionObserver((items) => {
    for (const item of items) {
      if (item.isIntersecting) {
        item.target.classList.add('in')
        observer.unobserve(item.target)
      }
    }
  }, { threshold: 0.15 })
  document.querySelectorAll('.rise, .fade-in').forEach((el) => observer.observe(el))
})
onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
  <div class="min-h-screen flex flex-col bg-[#FAF9F7] text-[#083A4F]">

    <!-- Hero band -->
    <div class="bg-[#083A4F] text-white">
      <header class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-2">
        <router-link to="/" class="flex items-center gap-2">
          <span class="w-9 h-9 rounded-lg bg-[#A58D66] text-[#102029] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z" />
            </svg>
          </span>
          <span class="font-semibold text-base whitespace-nowrap">Time Tracker</span>
        </router-link>

        <!-- Links and buttons live together on one side so nothing floats in the middle -->
        <div class="flex items-center gap-1 sm:gap-2">
          <a href="#how" class="hidden md:inline-flex px-3.5 py-2 text-sm font-medium text-[#C0D5D6] hover:text-white transition-colors">{{ t('landing.nav_how') }}</a>
          <router-link to="/login" class="px-2 sm:px-3.5 py-2 text-xs sm:text-sm font-medium whitespace-nowrap text-[#C0D5D6] hover:text-white transition-colors">
            {{ t('landing.sign_in') }}
          </router-link>
          <router-link to="/register" class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full whitespace-nowrap bg-[#A58D66] text-[#102029] text-xs sm:text-sm font-medium hover:bg-[#C0D5D6] transition-colors">
            {{ t('landing.get_started') }}
          </router-link>
        </div>
      </header>

      <!-- Hero body: text on one side, the illustration bleeding in from the other -->
      <div class="relative overflow-hidden flex flex-col">
        <div class="hero-art fade-in order-2 lg:order-none lg:absolute lg:inset-y-0 lg:end-0 lg:w-[72%]" style="--d:150ms">
          <img :src="deskImg" alt="" width="1536" height="1024" fetchpriority="high" decoding="async"
               class="hero-img w-full h-full object-cover object-right" />
        </div>

        <section class="order-1 lg:order-none relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 pt-4 pb-8 sm:pt-8 lg:py-20 lg:min-h-[560px] flex items-center">
          <div class="lg:w-[44%] flex flex-col items-start gap-6">
            <h1 class="rise text-[28px] leading-[34px] sm:text-4xl md:text-5xl font-bold tracking-tight leading-tight text-balance">
              {{ t('landing.hero_title') }}
            </h1>
            <p class="rise text-base text-[#C0D5D6] max-w-prose text-pretty" style="--d:120ms">{{ t('landing.hero_text') }}</p>
            <div class="rise grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-4 w-full sm:w-auto" style="--d:240ms">
              <router-link to="/register" class="text-center px-6 py-3 rounded-full bg-[#A58D66] text-[#102029] text-sm font-medium hover:bg-[#C0D5D6] transition-colors">
                {{ t('landing.hero_cta') }}
              </router-link>
              <router-link to="/login" class="text-center px-6 py-3 rounded-full bg-[#FAF9F7] text-[#083A4F] text-sm font-medium hover:bg-[#C0D5D6] transition-colors sm:bg-transparent sm:text-[#C0D5D6] sm:hover:bg-transparent sm:hover:underline sm:px-2 sm:py-1">
                {{ t('landing.hero_signin') }}
              </router-link>
            </div>
          </div>
        </section>
      </div>
    </div>

    <main class="flex-1 bg-gradient-to-b from-[#E5E1DD] to-[#FAF9F7]">
      <!-- Steps -->
      <section id="how" class="py-16">
        <div class="max-w-6xl mx-auto px-6 flex flex-col gap-6">
          <div class="rise max-w-xl">
            <h2 class="text-2xl font-semibold tracking-tight">{{ t('landing.steps_title') }}</h2>
            <p class="text-sm text-[#3E5660] mt-1">{{ t('landing.steps_text') }}</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <article v-for="(s, i) in steps" :key="s.title" :style="{ '--d': `${i * 120}ms` }" class="rise flex flex-col gap-4">
              <img :src="s.img" alt="" width="1024" height="572" loading="lazy" decoding="async"
                   class="blend-art w-full aspect-[512/286] object-cover" />
              <div class="flex flex-col gap-1.5">
                <h3 class="text-lg font-semibold tracking-tight">{{ t(s.title) }}</h3>
                <p class="text-sm text-[#3E5660] text-pretty">{{ t(s.text) }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- Closing call to action -->
      <section class="py-16">
        <div class="rise max-w-4xl mx-auto px-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <h2 class="text-2xl font-semibold tracking-tight">{{ t('landing.cta_title') }}</h2>
            <p class="text-sm text-[#3E5660] mt-1">{{ t('landing.cta_text') }}</p>
          </div>
          <router-link to="/register" class="shrink-0 px-6 py-3 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors">
            {{ t('landing.cta_button') }}
          </router-link>
        </div>
      </section>
    </main>

    <footer class="border-t border-[#D9D4CE]">
      <div class="max-w-6xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#3E5660]">
        <span>Time Tracker · {{ t('landing.footer_languages') }}</span>
        <span>© {{ new Date().getFullYear() }} Time Tracker</span>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.rise {
  opacity: 0;
  transform: translateY(18px);
  transition: opacity 0.6s ease, transform 0.6s ease, translate 0.2s ease, box-shadow 0.2s ease;
  transition-delay: var(--d, 0ms);
}
.rise.in {
  opacity: 1;
  transform: none;
}
/* The hero picture fades in and dissolves into the navy band on the sides that touch the text */
.fade-in {
  opacity: 0;
  transition: opacity 0.9s ease;
  transition-delay: var(--d, 0ms);
}
.fade-in.in {
  opacity: 1;
}
.hero-art {
  height: 300px;
  -webkit-mask-image: linear-gradient(to bottom, transparent, rgba(0,0,0,.5) 20%, #000 55%);
  mask-image: linear-gradient(to bottom, transparent, rgba(0,0,0,.5) 20%, #000 55%);
}
@media (min-width: 1024px) {
  /* The picture already darkens to navy on its own text side; this just guarantees the very edge matches the page */
  .hero-art {
    height: auto;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,.35) 8%, rgba(0,0,0,.75) 16%, #000 26%), linear-gradient(to bottom, transparent 0%, rgba(0,0,0,.5) 8%, #000 18%);
    mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,.35) 8%, rgba(0,0,0,.75) 16%, #000 26%), linear-gradient(to bottom, transparent 0%, rgba(0,0,0,.5) 8%, #000 18%);
    -webkit-mask-composite: source-in;
    mask-composite: intersect;
  }
  [dir='rtl'] .hero-art {
    -webkit-mask-image: linear-gradient(to left, transparent 0%, rgba(0,0,0,.35) 8%, rgba(0,0,0,.75) 16%, #000 26%), linear-gradient(to bottom, transparent 0%, rgba(0,0,0,.5) 8%, #000 18%);
    mask-image: linear-gradient(to left, transparent 0%, rgba(0,0,0,.35) 8%, rgba(0,0,0,.75) 16%, #000 26%), linear-gradient(to bottom, transparent 0%, rgba(0,0,0,.5) 8%, #000 18%);
  }
}
/* A very slow drift on the picture: it only ever zooms in, so no edge is exposed */
.hero-img {
  animation: drift 22s ease-in-out infinite alternate;
  transform-origin: 70% 55%;
}
@keyframes drift {
  from { transform: scale(1); }
  to { transform: scale(1.06); }
}
@media (prefers-reduced-motion: reduce) {
  .hero-img { animation: none; }
  .rise { opacity: 1; transform: none; transition: none; }
  .fade-in { opacity: 1; transition: none; }
}
</style>
