<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { middlewareManager } from '@/components/middleware/MiddlewareManager';
//import DarkButton from '@/components/DarkButton.vue';
import Banner from '@/components/Banner.vue';

// layouts
//import Footer from '@/layouts/Footer.vue';
//import Header from '@/layouts/Header.vue';

const props = defineProps({
    set: Object,
    page: Object,
    contract: Object,
    auth: Object
});

const _mainLayout = props.page.max_width ? "" : "container";

// Get page instance
const page = usePage();

onMounted(() => {
  console.log('WelkomLayout mounted');
  
  // Initialize middleware system
  middlewareManager.init();
  
  // Check authentication using existing middleware
  const authCheck = middlewareManager.checkAuth();
  if (!authCheck.userValid) {
    console.log('User not authenticated - redirecting to login');
    window.location.href = '/login';
    return;
  }

  // Check if user has required properties
  if (!page.props.auth.user.current_team) {
    console.log('User missing current_team - redirecting to login');
    window.location.href = '/login';
    return;
  }

  // Handle intended destination
  const intendedDestination = sessionStorage.getItem('intendedDestination');
  if (intendedDestination) {
    sessionStorage.removeItem('intendedDestination');
    window.location.href = intendedDestination;
  }
});

onUnmounted(() => {
  // Cleanup middleware
  middlewareManager.cleanup();
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

            <!--:class="_mainLayout" -->
            <div >
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