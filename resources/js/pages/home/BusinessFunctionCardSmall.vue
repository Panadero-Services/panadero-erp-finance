<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';
import { EnvelopeIcon, ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon, NewspaperIcon, RectangleGroupIcon, PencilSquareIcon, BarsArrowUpIcon, BuildingOfficeIcon, ClipboardDocumentCheckIcon, CubeTransparentIcon, CreditCardIcon, RocketLaunchIcon, LanguageIcon, DevicePhoneMobileIcon, TruckIcon, PuzzlePieceIcon, EllipsisVerticalIcon } from '@heroicons/vue/24/outline';

import { usePage } from '@inertiajs/vue3';
import NavLink from '@/components/NavLink.vue';


const props = defineProps({
    set: Object, 
    f: Object, 
    menu: Object, 
    featured: Boolean, 
    progress: Number
});

const emit = defineEmits(['edit']);

const handleEdit = (event) => {
  event.preventDefault();
  event.stopPropagation();
  showPopup.value = false;
  emit('edit', props.f);
};

const _navigate = () =>  {
    router.push(props.f.url); 
}

const showPopup = ref(false);

const togglePopup = () => {
  showPopup.value = !showPopup.value;
};

const navigateTo = (url) => {
  const page = usePage();
  if (page.props.auth.user) {
    window.location.href = url;
  } else {
    window.location.href = '/login';
  }
};

// css
const _basic = " text-xxs lg:text-xs font-normal leading-tight ";
const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400";
const _menu = " flex items-center  "+ _indigo + _basic;
const _icon = " w-5 h-5  ";
const _feature = "h-5 w-5 m-1.5 ";

const _feature2 = "h-5 w-5 m-1.5 text-green-500 dark:text-white";

const _color = computed(()=> { 
  
  if (props.f.path.includes('feature')) 
    return props.f.color ?  'text-'+ props.f.color + '-400 border border-'+ props.f.color +'-400' :   "text-slate-500";
    return props.f.color ?  'text-white ' +'bg-'+ props.f.color +'-700' :   "bg-slate-500";

    //const _color2 = props.f.color  ?  'border-2 border-'+ props.f.color +'-700' :   "border-2 border-red-500";
});



</script>

<template>
  <div class="relative group">
    <div class="h-20 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
      <div class="grid grid-cols-4">
              <div class="m-3 h-6 w-6 lg:h-8 lg:w-8 items-center justify-center rounded-lg " :class="_color">
                    <RectangleGroupIcon v-if="'I3FrameworkIcon'==f.icon" :class="_feature" aria-hidden="true" />
                    <PencilSquareIcon v-if="'ContentManagementIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <BarsArrowUpIcon v-if="'ResourcePlanningIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <BuildingOfficeIcon v-if="'LogisticsIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <ClipboardDocumentCheckIcon v-if="'ProjectIcon'==f.icon" :class="_feature" aria-hidden="true" />
                    <CubeTransparentIcon v-if="'DesignIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <CreditCardIcon v-if="'EcommerceIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <RocketLaunchIcon v-if="'Web3Icon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <LanguageIcon v-if="'AiIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <DevicePhoneMobileIcon v-if="'SocialMediaIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <PuzzlePieceIcon v-if="'PuzzlePieceIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <TruckIcon v-if="'TruckIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <CloudArrowUpIcon v-if="'CloudArrowUpIcon'==f.icon"  :class="_feature" aria-hidden="true" />
                    <ArrowPathIcon v-if="'ArrowPathIcon'==f.icon" :class="_feature" aria-hidden="true" />
                    <FingerPrintIcon v-if="'FingerPrintIcon'==f.icon" :class="_feature" aria-hidden="true" />
                    <Bars3Icon v-if="'Bars3Icon'==f.icon" :class="_feature" aria-hidden="true" />                     
                    <LockClosedIcon v-if="'LockClosedIcon'==f.icon" :class="_feature" aria-hidden="true" />
                    <WalletIcon v-if="'WalletIcon'==f.icon" :class="_feature" aria-hidden="true" />
                <p class="mt-2 text-center text-xxxs col-span-3 text-gray-600 dark:text-white ">{{ f.version }}</p>
            </div>
            <div class="col-span-3 mt-3">
                <p class="text-sm col-span-3 text-gray-800 dark:text-white ">{{ f.title }}</p>
            </div>
      </div>
    </div>
    <div class="absolute top-2 right-2">
      <button @click="togglePopup" class="text-gray-500 hover:text-gray-700 dark:text-gray-200 hover:scale-125">
        <EllipsisVerticalIcon class="h-4 w-4" />
      </button>

      <div v-if="showPopup" @mouseleave="showPopup = false" class="absolute right-0 mt-2 w-48 shadow-lg rounded-md z-10 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
        <div v-for="option in f.options" :key="option.name" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
          <a @click.prevent="navigateTo(option.url)" class="text-sm text-gray-700 dark:text-gray-300 h-10 cursor-pointer flex items-center hover:text-indigo-600 dark:hover:text-indigo-400">
            {{ option.name }}
          </a>
        </div>
        <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-100 dark:border-gray-700">
          <a @click="handleEdit" class="text-sm text-gray-700 dark:text-gray-300 h-10 cursor-pointer flex items-center gap-2 hover:text-indigo-600 dark:hover:text-indigo-400">
            <PencilSquareIcon class="h-4 w-4" />
            Edit Card
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.group:hover .shadow-lg {
  transform: translateY(-5px);
}
</style>