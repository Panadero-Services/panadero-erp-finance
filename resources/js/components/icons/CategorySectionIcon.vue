<script setup>
import {computed, ref, onMounted} from 'vue'
import { FireIcon, HeartIcon, TvIcon, UserIcon, ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, LockClosedIcon, PencilSquareIcon, WrenchIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  icon: String,
  activated: Boolean,
  error: Boolean
});

const _icon = computed(() => {
  let __icon = "transition hover:-translate-y-0.5 duration-300 h-3.5 w-3.5 hover:text-green-500 dark:hover:text-green-400 ";
  if(props.error) {
    __icon += "text-red-700 dark:text-red-400 ";
    return __icon;
  }
  __icon +=  props.activated ? "text-indigo-700 dark:text-indigo-400 " : "text-gray-300 dark:text-gray-600 ";
  return __icon;
});
</script>
<template>
      <div class="flex mb-2 gap-x-2 justify-end">
        <FingerPrintIcon v-if="icon=='FingerPrint'" :class="_icon" alt="you too here?" title="hey you there!"/>
        <WrenchIcon v-if="icon=='Wrench'" :class="_icon" />
        <ArrowPathIcon v-if="icon=='ArrowPath'" :class="_icon" />
        <CloudArrowUpIcon v-if="icon=='CloudArrowUp'" :class="_icon" />
        <LockClosedIcon v-if="icon=='LockClosed'" :class="_icon" />
        <UserIcon v-if="icon=='User'" :class="_icon" />
        <HeartIcon v-if="icon=='Heart'" :class="_icon" />
        <FireIcon v-if="icon=='Fire'" :class="_icon" />
        <TvIcon v-if="icon=='Tv'" :class="_icon" />
      </div>
</template>

<style type="text/css">

</style>
