<template>
  <div class="bg-white dark:bg-gray-900 py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-4xl text-center">
        <h2 class="text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">Pricing</h2>
        <p class="mt-2 text-balance text-5xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-6xl">Pricing that grows with you</p>
      </div>
      <p class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg font-medium text-gray-600 dark:text-gray-300 sm:text-xl/8">Choose an affordable plan that's packed with the best features for engaging your audience, creating customer loyalty, and driving sales.</p>
      <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-y-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
        <div v-for="(tier, tierIdx) in tiers" :key="tier.id" :class="[tier.mostPopular ? 'lg:z-10 lg:rounded-b-none' : 'lg:mt-8', tierIdx === 0 ? 'lg:rounded-r-none' : '', tierIdx === tiers.length - 1 ? 'lg:rounded-l-none' : '', 'flex flex-col justify-between rounded-3xl bg-white dark:bg-gray-800 p-8 ring-1 ring-gray-200 dark:ring-gray-700 xl:p-10']">
          <div>
            <div class="flex items-center justify-between gap-x-4">
              <h3 :id="tier.id" :class="[tier.mostPopular ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-900 dark:text-white', 'text-lg/8 font-semibold']">{{ tier.name }}</h3>
              <p v-if="tier.mostPopular" class="rounded-full bg-indigo-600/10 dark:bg-indigo-400/10 px-2.5 py-1 text-xs/5 font-semibold text-indigo-600 dark:text-indigo-400">Most popular</p>
            </div>
            <p class="mt-4 text-sm/6 text-gray-600 dark:text-gray-300">{{ tier.description }}</p>
            <p class="mt-6 flex items-baseline gap-x-1">
              <span class="text-4xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ tier.priceMonthly }}</span>
              <span class="text-sm/6 font-semibold text-gray-600 dark:text-gray-300">/month</span>
            </p>
            <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 dark:text-gray-300">
              <li v-for="feature in tier.features" :key="feature" class="flex gap-x-3">
                <CheckIcon class="h-6 w-5 flex-none text-indigo-600 dark:text-indigo-400" aria-hidden="true" />
                <span>
                  <span v-if="typeof feature === 'string'">{{ feature }}</span>
                  <span v-else>
                    {{ feature.text }}
                    <span
                      class="ml-1 text-gray-400 dark:text-gray-500 cursor-help"
                      :title="feature.tooltip"
                    >&#9432;</span>
                  </span>
                </span>
              </li>
            </ul>
          </div>
          <a :href="tier.href" :aria-describedby="tier.id" :class="[tier.mostPopular ? 'bg-indigo-600 dark:bg-indigo-500 text-white shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400' : 'text-indigo-600 dark:text-indigo-400 ring-1 ring-inset ring-indigo-200 dark:ring-indigo-700 hover:ring-indigo-300 dark:hover:ring-indigo-600', 'mt-8 block rounded-md px-3 py-2 text-center text-sm/6 font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:focus-visible:outline-indigo-400']">{{ tier.ctaLabel }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

const props = defineProps(['plans'])

// Transform existing plans to new format
const tiers = computed(() => {
  return props.plans.map((plan, index) => ({
    id: `tier-${plan.title.toLowerCase()}`,
    name: plan.title,
    href: plan.cta.href,
    priceMonthly: plan.price,
    description: plan.description,
    features: plan.features,
    mostPopular: plan.popular,
    ctaLabel: plan.cta.label,
  }));
});
</script>

<style scoped>
/* Optional scoped styles */
</style>
