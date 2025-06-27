<script setup>
import {computed, ref, onMounted} from 'vue'
import { 
    FingerPrintIcon,
    WrenchIcon,
    ArrowPathIcon,
    CloudArrowUpIcon,
    LockClosedIcon,
    LockOpenIcon,
    UserIcon,
    HeartIcon,
    FireIcon,
    TvIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';
// import { 
//     DocumentCheckIcon,
//     GlobeAltIcon,
//     StarIcon,
//     LockClosedIcon,
//     UserIcon,
//     LightBulbIcon,
//     CheckCircleIcon,
//     ArchiveBoxIcon,
//     QuestionMarkCircleIcon
// } from '@heroicons/vue/24/outline';

const props = defineProps({
  icon: String,
  activated: Boolean,
  error: Boolean
});

const _icon = computed(() => {
  let __icon = "transition hover:-translate-y-0.5 duration-300 h-3 w-3 hover:text-green-500 dark:hover:text-green-400 ";
  if(props.error) {
    __icon += "text-red-700 dark:text-red-400 ";
    return __icon;
  }
  __icon +=  props.activated ? "text-indigo-700 dark:text-indigo-400 " : "text-gray-300 dark:text-gray-600 ";
  return __icon;
});

const getIconForFlag = (flag) => {
    const iconMapping = {
        'is_active': 'LockOpen',
        'is_locked': 'LockClosed',
        'is_featured': 'Fire',
        'is_public': 'CloudArrowUp',
        'is_published': 'CloudArrowUp',
        'is_smart': 'FingerPrint',
        'is_self': 'User',
        'is_archived': 'ArrowPath',
        'default': 'User'
    };
    return iconMapping[flag] || iconMapping.default;
};
</script>
<template>
      <div class="flex mb-2 gap-x-2 justify-end">
        <FingerPrintIcon v-if="icon=='FingerPrint'" :class="_icon" alt="you too here?" title="hey you there!"/>
        <WrenchIcon v-if="icon=='Wrench'" :class="_icon" />
        <ArrowPathIcon v-if="icon=='ArrowPath'" :class="_icon" />
        <CloudArrowUpIcon v-if="icon=='CloudArrowUp'" :class="_icon" />
        <LockClosedIcon v-if="icon=='LockClosed'" :class="_icon" />
        <LockOpenIcon v-if="icon=='LockOpen'" :class="_icon" />
        <UserIcon v-if="icon=='User'" :class="_icon" />
        <HeartIcon v-if="icon=='Heart'" :class="_icon" />
        <FireIcon v-if="icon=='Fire'" :class="_icon" />
        <TvIcon v-if="icon=='Tv'" :class="_icon" />
        <EyeIcon v-if="icon=='Eye'" :class="_icon" />
        <PencilSquareIcon v-if="icon=='PencilSquare'" :class="_icon" />
        <TrashIcon v-if="icon=='Trash'" :class="_icon" />
      </div>
</template>

<style type="text/css">

</style>
