<template>
  <div class="grid gap-6 lg:grid-cols-3 p-4">
    <div
      v-for="(plan, index) in plans"
      :key="index"
      :class="[
        'rounded-2xl shadow-md transition hover:shadow-xl p-6 flex flex-col justify-between border',
        plan.popular ? 'border-blue-600 ring-2 ring-blue-400' : 'border-gray-200',
      ]"
    >
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold">{{ plan.title }}</h2>
          <span
            v-if="plan.popular"
            class="bg-blue-100 text-blue-600 text-xs font-semibold px-2 py-1 rounded-full"
          >
            Most Popular
          </span>
        </div>
        <p class="text-3xl font-bold">{{ plan.price }}</p>
        <p class="text-sm text-gray-500 mb-4">{{ plan.period }}</p>
        <p class="text-gray-600 mb-4">{{ plan.description }}</p>
        <ul class="space-y-2 text-sm">
          <li
            v-for="(feature, i) in plan.features"
            :key="i"
            class="flex items-start gap-2"
          >
            <span class="text-green-500">✓</span>
            <span>
              <span v-if="typeof feature === 'string'">{{ feature }}</span>
              <span v-else>
                {{ feature.text }}
                <span
                  class="ml-1 text-gray-400 cursor-help"
                  :title="feature.tooltip"
                >&#9432;</span>
              </span>
            </span>
          </li>
        </ul>
      </div>
      <a
        :href="plan.cta.href"
        class="mt-6 inline-block bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition"
      >
        {{ plan.cta.label }}
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps(['plans'])
</script>

<style scoped>
/* Optional scoped styles */
</style>
