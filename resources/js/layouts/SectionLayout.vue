<script setup>
import {computed, ref, onMounted} from 'vue'

import { useForm } from "@inertiajs/vue3";
import SectionForm from "@/sections/SectionForm.vue";

// logo standard
import { PencilSquareIcon } from '@heroicons/vue/24/outline';
//import { ChevronRightIcon } from '@heroicons/vue/20/solid';
//import logo from "@/logos/panaderos-logo64.svg";
//import silverLogo from "@/logos/panaderos-logo64-silver.svg";
//import ApplicationLogo from '@/components/ApplicationLogoSilver.vue';

// logo optional
//import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon, PlusIcon, CheckIcon } from '@heroicons/vue/24/outline';
//import self1 from "@/logos/self1.png";
//import self2 from "@/logos/self2.png";

let editMode = ref(false);

const props = defineProps({
    set: Object,
    section: Object, 
    layout: Object, 
    title: String
});

const form = useForm({
    id: props.section.id,
    title: props.section.title,
    file: props.section.file,
    subtitle: props.section.subtitle,
    icon: props.section.icon,
    image: props.section.image,
    slogan: props.section.slogan,
    html: props.section.html,
    css: props.section.css,
    features: props.section.features,
    settings: props.section.settings,
    self: props.section.self,
    self_admin: props.section.self_admin,
    priority: props.section.priority,
    page_id : props.section.page_id,
    is_active: props.section.is_active==1,
    self_auth: props.section.self_auth==1,
    animate: props.section.animate==1
});

const _shoot = async (_nr) => {
    props.section.self_admin = form.self_admin;
    console.log('shoot', _nr);
    if(_nr==0) editMode.value = false;
}

const _logo = computed(()=>{ if (props.set.dark)return silverLogo; return logo; }) ;
const _adminIcon = computed(()=>{ if (props.set.dark)return silverLogo; return logo; }) ;
const _selfIcon = computed(()=>{ if (props.set.dark)return self1; return self2; }) ;

const _selfAuth = computed(() => {
  return form.self.includes(props.set.self) || form.self.includes('public' )|| true ;
});

const _selfSectionAdminAuth = computed(() => {
  return form.self_admin.includes(props.set.self);
});

const _walletConnected = computed(()=>{
  return  !(props.set.wallet=='0x0');
});

const features_ = computed(()=>{
    try {
        let b = JSON.parse(form.features);
        return b;
    } catch (e) {
        return ['json no valid'];
    }
});

// css
const _icon = "w-4 h-4 text-indigo-800 dark:text-indigo-400 ";
const _hoverDevIcon = "w-3 h-3 text-purple-600 dark:text-purple-400 transition hover:-translate-y-1 hover:scale-[3] duration-300";
const _feature = "h-5 w-5 sm:h-6 lg:w-6 text-white";
const _app_logo = "m-1 w-10 h-10 sm:w-16 sm:h-16 lg:w-24 lg:h-24";
//const _app_logo = "m-1 w-8 h-8 md:w-12 md:h-12 lg:w-16 lg:h-16 xl:w-24 xl:h-24";
const _main = computed(  () => { if(editMode.value) return "lg:grid lg:grid-cols-10 border-4 border-indigo-500"; return "";});
const _form_field = "bg-indigo-100 focus:bg-white dark:bg-indigo-900 dark:focus:bg-indigo-700 dark:text-white block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs";
</script>

<template>

    <div :class="_main">
        <div class="relative" :class="editMode ? 'lg:col-span-7' : ''" >
            <div :class="layout.main">

            <PencilSquareIcon v-if="set.isSelfAdmin || _selfSectionAdminAuth" @click="editMode=!editMode" class="absolute left-32 top-1" :class="_hoverDevIcon"  /> 

                <!-- <header >-->
                <div :class="layout.header">
                    <slot name="header" :title="form.title" :subtitle="form.subtitle" :slogan="form.slogan" :features="features_"></slot>
                </div>

                <!-- left  -->
                <div :class="layout.left">
                    <slot name="left" :features="features_"></slot>
                </div>

                <!-- <right >-->
                <div :class="layout.right">
                    <slot name="right" :html="form.html" :editMode="editMode"></slot>
                </div>

                <!-- <footer >-->
                <div :class="layout.footer">
                    <slot name="footer" :html="form.html" :features="features_" :editMode="editMode"></slot>
                </div>

            </div>
        </div>

        <!-- form -->
        <div v-if="editMode" class="lg:col-span-3">
             <section-form :owner="title" :form="form" :self="set.self" :selfAuth="_selfAuth" :selfAdminAuth="_selfSectionAdminAuth" @shoot="_shoot"  />
        </div>

    </div>

</template>

<style>
    /*
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

.web3-text {
  color:#a400b3;
}
*/
</style>