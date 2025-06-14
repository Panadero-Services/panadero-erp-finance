<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
    <div class="">
        <div class=" md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="p-2 py-6 bg-slate-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700"
                    :class="hasActions ? 'rounded-xl' : 'rounded-xl'"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form" />
                    </div>
                </div>

                <div v-if="hasActions" class="flex items-center justify-end px-4 py-3 bg-slate-100 dark:bg-gray-800 text-end sm:px-6 rounded-xl">
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>
