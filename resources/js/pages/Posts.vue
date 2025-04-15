<script setup>
import { ref, provide } from 'vue';

// layout
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import PostCard from '@/layouts/cards/PostCard.vue';
import Pagination from "@/layouts/Pagination.vue"

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';

const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// components
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

const props = defineProps({
    page: Object,
    baseSections: Object,
    posts: Object
});

const _pulse = ref(false);
provide(/* key */ 'pulse', /* value */ _pulse);


</script>

<template>
   <AppToolbarLayout :title="page.title" :baseSections="baseSections" :set="_set" :contract="_contract" :page="page">

      <template #header>
         <pulse  v-model="_pulse" :animation="_set.animate"/>
      </template>

      <template #intro />

      <template #default>
         <div id="whatever" class="w-full ... min-h-4 min-w-full ">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
              <div v-for="post in posts.data" key="post.id" class="text-sm">
                  <PostCard :post="post" />
              </div>
            </div>
            <Pagination :meta="posts.meta" />
         </div>
      </template>

      <template #footer>
   </template>

   </AppToolbarLayout>
</template>



