<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from "@/layouts/AppLayout.vue"

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// date
import PostCard from '@/layouts/cards/PostCard.vue';

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import Pagination from "@/layouts/Pagination.vue"

const props = defineProps(['posts']);

// webhooks
onMounted(async ()=> {
   await _set.initMM();
   await _set.initialize();
})

</script>
<template>
      <AppLayout title="Posts" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>

          <div class="py-6 sm:py-8 lg:py-12 ">

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
              <div v-for="post in posts.data" key="post.id" class="p-4 text-sm ">
                  <PostCard :post="post" />
              </div>
            </div>
            </div>
      <Pagination :meta="posts.meta" />
    </template>
  </AppLayout>

</template>