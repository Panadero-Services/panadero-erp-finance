<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { middlewareManager } from '@/components/middleware/MiddlewareManager';
//import DarkButton from '@/components/DarkButton.vue';
import Banner from '@/components/Banner.vue';

import { useSessionStore } from '@/stores/session'

// layouts
//import Footer from '@/layouts/Footer.vue';
//import Header from '@/layouts/Header.vue';

const sessionStore = useSessionStore();

// Add computed properties for session timer
const progressPercentage = computed(() => sessionStore.progressPercentage);
const progressBarColor = computed(() => sessionStore.progressBarColor);
const progressTextColor = computed(() => sessionStore.progressTextColor);
const _tooltip = computed(() => sessionStore.tooltip);

const props = defineProps({
    set: Object,
    page: Object,
    contract: Object,
    auth: Object
});

const _mainLayout = props.page?.max_width ? "" : "container";

// Get page instance
const page = usePage();

onMounted(() => {
  console.log('Component WelkomLayout mounted');
  sessionStore.updateSessionLifetime();
});

onUnmounted(() => {
  // Cleanup middleware
});

</script>

<template>
  <html  :class="set.dark ? 'dark' : 'light'">

    <div class="h-screen font-roboto">

        <!--  <Navigation v-if="page.sidebar" :pages="filteredPages('sidebar')" /> -->
        <Head :title="page.title" /> 

        <div class="">

           <!--
            <Header v-if="page.header" :set="set" :page="page" :contract="contract" :auth="auth" />
             <Navigation v-if="page.sidebar" :pages="filteredPages('sidebar')" /> -->

            <Banner v-if="set.banner" />

            <!-- Add session timer -->
            <div v-if="sessionStore.remainingTime !== null" class="justify-=bg-gray-100 dark:bg-black flex items-center gap-2 text-xs text-gray-500">
              <div class="ml-6 -mt-1 w-16 h-0.5 bg-gray-200 rounded-full overflow-hidden">
                <div :title="_tooltip"
                  :class="[progressBarColor, 'h-full transition-all duration-500 ease-out']"
                  :style="{ width: `${progressPercentage}%`, minWidth: '2px' }"
                ></div>
              </div>
              <span v-if="sessionStore.remainingTime < 4000" class="-mt-1 text-xxs font-bold" :class="['h-full transition-all duration-500 ease-out', progressTextColor]">{{_tooltip}}</span>
            </div>

            <!--:class="_mainLayout" -->
            <div >
              {{sessionStore}}
                <slot />
            </div>

            <!-- wrenchMode DeveloperSections            class="bg-gray-900 dark:bg-gray-100 text-xs text-indigo-50 dark:text-indigo-900" -->
              <div v-if="set.wrenchMode && set.isSelfAdmin">
              </div>

             <!-- <Footer v-if="page.footer" :pages="filteredPages('footer')" />  
                <Footer v-if="page.footer" :set="set" :page="page" :contract="contract" :auth="auth" />
              -->
            </div>
        </div>
    </html>
</template>

<style>
.bg-dots{
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

.bg-dots-dark {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

.bg-dots-light {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
}

.security {
  background-color:#FF0000;
}

.web2 {
  background-color:#0066cc;
}

.web3 {
  background-color:#a400b3;
}

.indigo {
  background-color:#6016fd;
}

.p-indigo-text {
  color:#6016fd;
}

.web3-text {
  color:#a400b3;
}

.indigo-text-dark {
  color:white;
}

.web3, .web2, .indigo, .security {
  color:white;
}

.web3:hover, .web2:hover, .indigo:hover, .security:hover {
  color:black;
}

.pan-txt-indigo-500:hover  {
  color:#5c0aff;
}

.pan-txt-red-500:hover  {
  color:#FF0000;
}

.pan-txt-blue-500:hover  {
  color:#025ff5;
}

.pan-txt-purple-500:hover  {
  color:#ec0fff;
}


</style>