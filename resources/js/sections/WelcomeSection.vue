<script setup>
// defaults
import {computed, ref, onMounted} from 'vue';
import { useForm, Link } from "@inertiajs/vue3";
import SectionForm from "@/sections/SectionForm.vue";

// optional
import TemplateBgEffect from "@/layouts/effects/TemplateBgEffect.vue";  

// logo standard
import ApplicationLogo from '@/components/logos/ApplicationLogoSilver.vue';
import logo from "@/layouts/logos/panaderos-logo64.svg";
import silverLogo from "@/layouts/logos/panaderos-logo64-silver.svg";
import { PencilSquareIcon } from '@heroicons/vue/24/outline';

// logo optional
import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon, NewspaperIcon } from '@heroicons/vue/24/outline';
//import { ChevronRightIcon } from '@heroicons/vue/20/solid';
//import DarkButton from '@/components/DarkButton.vue';
import self1 from "@/layouts/logos/self1.png";
import self2 from "@/layouts/logos/self2.png";
import self3 from "@/layouts/logos/self3.png";

//import TableSection from "@/pages/sections/TableSection.vue";

let editMode = ref(false);

const props = defineProps({
    page: Object,
    set: Object,
    section: Object,
    pulse: Boolean
//    menuItems: Object
});

const _section = ref(props.section);
const extendedMode = ref(false);

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
    console.log('shoot', _nr);
    if(_nr==0) editMode.value = false;
}

const _logo = computed(()=>{ if (props.set.dark)return silverLogo; return logo; }) ;
const _icon = "w-4 h-4 text-indigo-800 dark:text-indigo-400";
const _hoverIcon = "transition hover:-translate-y-1 hover:scale-[5] duration-300 h-3 w-3";
const _feature = "h-5 w-5 sm:h-6 lg:w-6 text-white";
const _app_logo = "m-1 w-10 h-10 sm:w-16 sm:h-16 lg:w-24 lg:h-24";
//const _app_logo = "m-1 w-8 h-8 md:w-12 md:h-12 lg:w-16 lg:h-16 xl:w-24 xl:h-24";
const _main = computed(  () => { if(editMode.value) return "grid grid-cols-2"; return "";});
const _form_field = "bg-indigo-100 focus:bg-white dark:bg-indigo-900 dark:focus:bg-indigo-700 dark:text-white block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs";
const _adminIcon = computed(()=>{ if (props.set.dark)return silverLogo; return logo; }) ;
const _selfIcon = computed(()=>{ if (props.set.dark)return self1; return self2; }) ;


const features_ = computed(()=>{
//    let a =  JSON.stringify(features);
    let b = JSON.parse(form.features);
//    let c = JSON.parse(a);
    return b;
});

/* Breakpoint prefix Minimum width CSS
sm  640px @media (min-width: 640px) { ... }
//md  768px @media (min-width: 768px) { ... }
lg  1024px  @media (min-width: 1024px) { ... }
//xl  1280px  @media (min-width: 1280px) { ... }
*/

const _basic = " text-sm font-normal leading-tight hover:text-blue-600 hover:font-semibold dark:hover:text-yellow-400";

const _wallet = computed(() => {
    if (!props.set.isMetaMask) return " text-red-500"+_basic;      // no compatible browser
    if (props.set.wallet=='0x0')return " text-gray-500"+_basic;
    return " text-blue-600"+_basic;     // ethereum
    //return " text-indigo-500"+_basic;   // 
    //return " text-green-600"+_basic;    // other chain
    //return " text-purple-500"+_basic;   // polygon
    //return " text-yellow-600"+_basic;   // bsc
    //return " text-red-500"+_basic;      // no compatible browser
});

const _connect = async () => {
    console.log('start init');
    if (!props.set.isMetaMask){
        props.set.banner = {msg :'MetaMask not installed, check your configuration! ', style : 'danger', active : true }; 
        return;
    } 
    await props.set.initialize();
    props.set.banner = {msg :'wallet connected: ... click on address for disconnect', style : 'success', active : true }; 
}

const _disconnect = async () => {
    console.log('start reset');
    await props.set.reset();
    props.set.banner = {msg :'wallet disconnected: ', style : 'danger', active : true }; 
}

onMounted(async ()=> {
    await props.set.initMM();
//    if(form.animate) _loopTimer();
})

const _selfAuth = computed(() => {
  return form.self.includes(props.set.self) || form.self.includes('public') ;
});

const _selfAdminAuth = computed(() => {
  return form.self_admin.includes(props.set.self) 
});

const _walletConnected = computed(()=>{
  return  !(props.set.wallet=='0x0');
});



const _editClass= computed(()=>{
  return  editMode.value ? "grid grid-cols-2 " : "" ;
});

// css-section : stores/tailwind
    import { useTailwindStore } from '@/stores/tailwind';
    const _css = useTailwindStore();

    const _txt = _css.txt+_css.gray;
    const _txt_light = _css.txt_sm+_css.gray_light;

    const _menu_sm = _css.txt_sm + _css.indigo + _css.hover ;

    const _sectionTitle = _css.gray + _css.sectionTitle;
    const _sectionSubtitle = _css.gray + _css.sectionSubtitle ;
    const _slogan = _css.slogan + _css.indigo;

</script>

<template >
  <div class="">
    <div :class="_editClass" >

      <div class="relative isolate overflow-hidden col-span-2 md:col-span-1">
        <div class=" mx-auto max-w-screen pt-10 pb-24 lg:gap-x-8 lg:py-12 lg:px-8 ">
          <div class="px-6 lg:px-0 lg:pt-4">
            <div class="mx-auto max-w-2xl lg:max-w-4xl">
              <div class=" max-w-2xl lg:max-w-4xl lg:mt-6">


                <!-- Logo -->
                <div class="-ml-2 dark:opacity-100">
                  
                    <!-- Animation Light 
                      <span v-if="_walletConnected && set.wrenchMode">  
                        <span v-if="set.animate" @click="set.animate = false" class="absolute flex h-3 w-3 -ml-1">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 dark:bg-yellow-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-sky-500 dark:bg-yellow-500"></span>
                        </span>
                        <span v-else class="absolute flex h-3 w-3 -ml-1 " @click="set.animate = true">
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-200 dark:bg-gray-800"></span>
                        </span>
                      </span>
                    -->   <!-- -->

                    <div v-if="!set.isSelfAdmin">
                      <ApplicationLogo v-if="set.dark && form.icon=='ApplicationLogo'" :green="220" :class="_app_logo"/>
                      <ApplicationLogo v-if="!set.dark && form.icon=='ApplicationLogo'" :green="55" :class="_app_logo "/>
                    </div>


                    <div v-if="set.isSelfAdmin">
                      <ApplicationLogo @click="set.wrenchMode = !set.wrenchMode" v-if="set.dark && form.icon=='ApplicationLogo'" class="hover:rotate-2" :green="220" :class="_app_logo"/>
                      <ApplicationLogo @click="set.wrenchMode = !set.wrenchMode" v-if="!set.dark && form.icon=='ApplicationLogo'" class="hover:rotate-3" :green="55" :class="_app_logo "/>
                    </div>

                    <span v-if="!set.dark && form.icon=='Logo'" ><img class="m-1" style="width:96px; height:96px;" :src="logo"/> </span>
                    <span v-if="set.dark && form.icon=='Logo'" ><img class="m-1" style="width:96px; height:96px;" :src="logo"/> </span>

                    <span v-if="form.icon=='Self1'" ><img class="m-1" style="width:96px; height:96px;" :src="self1"/> </span>
                    <span v-if="form.icon=='Self2'" ><img class="m-1" style="width:96px; height:96px;" :src="self2"/> </span>
                    <span v-if="form.icon=='Self3'" ><img class="m-1" style="width:96px; height:96px;" :src="self3"/> </span>

                    <span v-if="!set.dark && form.icon=='SilverLogo'" ><img class="m-1" style="width:96px; height:96px;" :src="silverLogo"/> </span>
                    <span v-if="set.dark && form.icon=='SilverLogo'" ><img class="m-1" style="width:96px; height:96px;" :src="silverLogo"/> </span>

                </div>

                <!-- section.title -->
                <h1 class="-mt-12 sm:-mt-16 lg:-mt-24 ml-6 " :class="_sectionTitle">
                  <span @click="set.wrenchMode = !set.wrenchMode" v-html="form.title"></span>  
                </h1>

                <!-- section.slogan 
                -->

                <div v-if="_selfAuth" class="-mt-2 md:-mt-1 lg:mt-1 " :class= "_slogan">
                  {{form.slogan}}
                </div>
                <div v-else class="ml-48">
                  <div v-if="_walletConnected && set.wrenchMode">                              
                    <img :class="_hoverIcon" :src="_selfIcon" :title="'form.slogan : '+form.self"/>
                  </div>
                </div>

                <!-- section.subtitle 
                -->

                <div  class="my-2 my-4 sm:my-10 text-xs sm:text-sm lg:text-xl text-gray-700 dark:text-gray-300">
                  <div v-if="_selfAuth">
                    <div v-if="_selfAdminAuth && set.wrenchMode" @click="editMode=!editMode" title="edit this section">
                      <PencilSquareIcon :class="_hoverIcon"  />
                    </div>
                    <div v-else class="ml-2">
                      <div v-if="_walletConnected && set.wrenchMode">                              
                        <img :class="_hoverIcon" :src="_adminIcon" :title="'editMode : '+form.self_admin"/>
                      </div>
                    </div>
                    <span v-html="form.subtitle" ></span>
                  </div>
                  <div v-else class="mt-16 lg:mt-24"></div>
                </div>

                <!-- section.features -->
                <dl class="grid max-w-xl grid-cols-1 gap-y-6 sm:gap-y-8 lg:gap-y-12 gap-x-8 lg:max-w-none sm:grid-cols-2 lg:grid-cols-3 lg:mb-16">
                  <div v-for="feature in features_" :key="feature.name" class="relative pl-12 sm:pl-16 ">
                    <dt class=" " :class="_sectionSubtitle">
                      <div class="absolute top-0 left-0 flex h-8 w-8 lg:h-10 lg:w-10 items-center justify-center rounded-lg " :class="'bg-'+feature.color+'-600'">

                           <!-- error created on aws -->
                          <CloudArrowUpIcon v-if="'CloudArrowUpIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <ArrowPathIcon v-if="'ArrowPathIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <FingerPrintIcon v-if="'FingerPrintIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <Bars3Icon v-if="'Bars3Icon'==feature.icon" :class="_feature" aria-hidden="true" />                     
                          <LockClosedIcon v-if="'LockClosedIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <WalletIcon v-if="'WalletIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <NewspaperIcon v-if="'ProjectIcon'==feature.icon" :class="_feature" aria-hidden="true" />
                          <!-- error created on aws <ChevronRightIcon v-if="'ChevronRightIcon'==feature.icon" :class="_feature" aria-hidden="true" />-->

                      </div>
                      <Link v-if="feature.route"  :href="feature.route" :class="'pan-txt-'+feature.color+'-500'" >
                        {{ feature.name }}
                      </Link>
                      <span v-else>
                        {{ feature.name }}
                      </span>
                           <!-- -->
                    </dt>
                    <dd class="mt-1 sm:mt-2 " :class="_txt" >{{ feature.description }}</dd>
                  </div>
                </dl>

                <!-- effect 
                  <template-bg-effect :effect="3" />
                -->
                <!-- section.html 
                -->

                <div class="mt-10 ml-12 text-xs text-indigo-700 dark:text-indigo-400 ">
                  <span v-if="_selfAdminAuth" v-html="form.html"></span>
                  <span v-else>
                    <div v-if="_walletConnected && set.wrenchMode">                              
                      <img :class="_hoverIcon" :src="_adminIcon" :title="'form.html : '+form.self_admin"/> 
                    </div>
                  </span>
                </div>


              </div>
            </div>
          </div>
            
        </div>

        <!-- bg color effect 
          <div class="absolute inset-x-0 bottom-0 -z-10 h-24 bg-gradient-to-t from-indigo-light-200 dark:from-indigo-dark-700 sm:h-80" />
        -->
      </div>

    <!-- form -->
    <div v-if="editMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
         <section-form owner="WelcomeSection" :form="form" :self="set.self" :selfAuth="_selfAuth" :selfAdminAuth="_selfAdminAuth" @shoot="_shoot"  />
    </div>

  </div>
</div>

</template>
