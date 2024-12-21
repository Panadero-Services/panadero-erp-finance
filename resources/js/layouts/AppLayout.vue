<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '@/components/Banner.vue';

import ApplicationMark from '@/Components/ApplicationMark.vue';

const props = defineProps({
    title: String,
    set: Object
});

</script>

<template>
  <html class="overscroll-none" :class="set.dark ? 'dark bg-black' : 'light bg-indigo-50'">
        <div class="bg-slate-50 text-black/50 dark:bg-slate-900 dark:text-white/50  font-roboto">
            <Head :title="title" />

            <!-- Header Content -->
            <div v-if="$slots.header">
                <slot name="header" />
            </div>

            <!-- Intro Content -->
            <div v-if="$slots.intro">
                <slot name="intro" />
            </div>

            <!-- Default Content -->
            <div class="bg-slate-50 dark:bg-slate-900" v-if="$slots.default">
                <slot name="default" />
            </div>

            <!-- Footer Content -->
            <div v-if="$slots.footer" class="">
                <slot name="footer" />
            </div>
        </div>
    </html>
</template>
