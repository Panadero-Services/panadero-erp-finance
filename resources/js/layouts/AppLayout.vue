<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

//icons
import ApplicationMark from '@/components/ApplicationMark.vue';

// baseSections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';

const props = defineProps({
    title: String,
    baseSections: Object,
    bgDark: Boolean
});

</script>

<template>
  <html class="overscroll-none" :class="bgDark ? 'dark bg-black' : 'light bg-indigo-50'">
        <div class="bg-slate-50 text-black/50 dark:bg-black dark:text-white/50 h-screen font-roboto">
            <Head :title="title" />

            <!-- Header Content -->
            <div v-if="$slots.header">
                <Banner />
                <slot name="header"/>
                 <!--   0 Basic Header Sections .... -->
                 <div v-for="section in baseSections">
                    <HeaderSection v-if="section.file =='HeaderSection' && set.layout.header" :page="page" :set="set" :contract="contract" :section="section"/>
                    <SubHeaderSection v-if="section.file =='SubHeaderSection' && set.layout.subHeader" :page="page" :set="set" :contract="contract" :section="section"/>
                 </div>
            </div>

            <!-- Intro Content -->
            <div v-if="$slots.intro">
                <slot name="intro" />
            </div>

            <!-- Default Content -->
            <div class="bg-slate-50 dark:bg-black" v-if="$slots.default">
                <slot name="default" />
            </div>

            <!-- Footer Content -->
            <div v-if="$slots.footer">
                <slot name="footer" />
            </div>
        </div>

    </html>
</template>
