<script setup>
import { computed, ref, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/components/NavLink.vue';

// layout
import SectionLayout from '@/layouts/SectionLayout.vue';

//icons
import logo from "@/layouts/logos/self2.png";
import { WrenchIcon, WalletIcon } from '@heroicons/vue/24/outline'

//props
const props = defineProps({
    page: Object,
    set: Object,
    contract: Object,
    section: Object
});

// title
const _title = 'headerSection';

// wallet-connect
const _getNextSelf = async () => {
    let _self = await props.contract.getNextSelfName(props.set.wallet);
    await props.set.setSelf(_self);
    console.log(_self);
}
const _connect = async () => { await props.set._connect(props.contract); }
const _disconnect = async () => { await props.set._disconnect(); }

// lifecycle
onMounted(async ()=> {
    // check user not  logged in and still project state active...
    if( usePage().props.auth.user == null) if(props.set.projectId > 0) await props.set.setResetProject();
});

let menu = [];

//if (props.set.domainFunction === 'home') 
menu.push( { name: 'Home',url: route('home/dashboard'),route: 'home/dashboard', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'home') menu.push( { name: 'Welcome',url: route('home/welkom'),route: 'home/welkom', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'home') menu.push( { name: 'Tiers',url: route('home/tiers'),route: 'home/tiers', when:() => usePage().props.auth.user });

if (props.set.domainFunction === 'erp') menu.push( { name: 'ERP',url: route('erp/dashboard'),route: 'erp/dashboard', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'erp') menu.push( { name: 'Resources',url: route('erp/resources'),route: 'erp/resources', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'erp') menu.push( { name: 'Mood',url: route('erp/mood'),route: 'erp/mood', when:() => usePage().props.auth.user });
//if (props.set.domainFunction === 'erp') menu.push( { name: 'Sand',url: route('erp/sand'),route: 'erp/sand', when:() => usePage().props.auth.user });


if (props.set.domainFunction === 'project') menu.push( { name: 'Project',url: route('erp/dashboard'),route: 'erp/dashboard', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Mind',url: route('project/mind'),route: 'project/mind', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Pert',url: route('project/pert'),route: 'project/pert', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Lane',url: route('project/lane'),route: 'project/lane', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Plan',url: route('project/plan'),route: 'project/plan', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Work',url: route('project/work'),route: 'project/work', when:() => usePage().props.auth.user });
if (props.set.domainFunction === 'project') menu.push( { name: 'Fund',url: route('project/fund'),route: 'project/fund', when:() => usePage().props.auth.user });

const menuz = [
    //{ name: 'Tiers',    url: route('tiers'),    route: 'tiers' },
    { name: 'Posts',    url: route('posts'),    route: 'posts',     when:() => usePage().props.auth.user },
    { name: 'Bento',    url: route('bento'),    route: 'bento',     when:() => usePage().props.auth.user },
    { name: 'Bots',     url: route('bots'),     route: 'bots',      when:() => usePage().props.auth.user },
   // { name: 'Planning', url: route('planning'), route: 'planning', when:() => usePage().props.auth.user },
    { name: 'Project',url: route('project'),route: 'project', when:() => usePage().props.auth.user },
    { name: 'Resources',url: route('erp/resources'),route: 'erp/resources', when:() => usePage().props.auth.user },
    { name: 'Web3',     url: route('web3'),     route: 'web3',      when:() => usePage().props.auth.user },
    { name: 'Grid',     url: route('grid'),     route: 'grid',      when:() => usePage().props.auth.user },
    { name: 'Mood',     url: route('erp/mood'),     route: 'erp/mood',      when:() => usePage().props.auth.user },
    { name: 'Table',    url: route('table'),    route: 'table',     when:() => usePage().props.auth.user },
    { name: 'Config',   url: route('config'),   route: 'config',    when:() => usePage().props.auth.user }
];

// css
const _basic = " text-xxs lg:text-xs font-normal leading-tight ";
const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400";
const _menu = " flex items-center  "+ _indigo + _basic;
const _icon = " w-4 h-4 mr-2 ";

const _wallet = computed(() => {
    if (!props.set.isMetaMask) return _indigo.replace('indigo','red'); 
    if (props.set.wallet=='0x0') return _indigo.replace('indigo','gray'); 
    return _indigo; 
});

//const delay = (s) =>  { return new Promise(resolve => setTimeout(resolve, s*1000)); }
//const _user = "  text-sm font-normal leading-tight " +_indigo;
//const _user2 =" inline-flex items-center px-2 py-1.5 text-xs font-medium leading-4 text-gray-700 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-500 active:bg-gray-500 ";
//const _menuClosed = " text-gray-700 text-sm font-normal leading-tight  ";
//const _menu= "text-xs lg:text-sm font-normal text-gray-700 hover:text-black hover:font-semibold dark:text-gray-300 dark:hover:text-yellow-400 ";

const _layout = {
  main   : "grid grid-cols-8 bg-gray-100 dark:bg-black h-18 md:h-12 border-b border-slate-100 dark:border-black shadow-sm shadow-gray-50 dark:shadow-slate-900 text-xs max-w-11xl" ,  //  max-w-7xl
  header : "col-span-8" , 
  left   : "col-span-8 md:col-span-4 mt-2 lg:mt-1 text-left text-xs uppercase", 
  right  : "col-span-8 md:col-span-4 mt-2 lg:mt-1", 
  footer : "col-span-8 bg-blue-700"
};

</script>

<template>
    <SectionLayout :set="set" :section="section" :title="_title" :layout="_layout">

        <!--header slot -->
        <template #header="headerProps">
        </template>

        <!--left slot -->
        <template #left="leftProps">     
            <div> 
                <div class="flex items-center space-x-1 md:space-x-2 p-3">
                    <Link href="/">
                        <span><img class="m-0.5 hover:rotate-45 dark:brightness-200" :class="_icon" :src="logo"/></span>
                    </Link>

                    <!-- Navigation Links -->
                    <template v-for="item in menu" :key="item.name">
                        <NavLink v-if="item.when ? item.when() : true" :href="item.url" :active="route().current(item.route)" :class="_menu">
                            {{item.name}}
                        </NavLink>
                    </template>
                </div>
            </div>
        </template>

       <!--right slot -->
        <template #right="rightProps">
            <!-- SELF Section -->
            <div class=" "> 
                <div class="flex items-end text-right justify-end space-x-1 md:space-x-2 md:p-3">
                    <span v-if="(!(set.wallet=='0x0') && set.isMetaMask && set.self=='nope')" @click="_getNextSelf"><img class="hover:scale-150 hover:rotate-45 h-3 w-3 mb-1 dark:brightness-200" :src="logo"/> </span>
                    <span v-if="(!(set.wallet=='0x0') && set.isMetaMask && !(set.self=='nope'))" ><img class="hover:rotate-45 h-3 w-3 mb-1 dark:brightness-200" :src="logo"/> </span>
                    <!-- SELF Connect -->
                    <p v-if="(!(set.wallet=='0x0'))" class="relative mt-0.5 ml-2" :class="_menu">
                        <a  v-if="!(set.self=='nope')" @click="_getNextSelf" class="text-xs lg:text-xs" :class="_wallet">
                            <span>{{set.self}}</span>
                        </a>
                    </p>
                    <p v-if="1" class="text-xs lg:text-sm">
                        <a @click="_connect">
                            <WalletIcon class="w-3.5 h-3.5 mb-1" :class="_wallet"/> 
                        </a>
                    </p>
                    <!-- Wallet Connect -->
                    <p v-if="(!(set.wallet=='0x0'))"  :class="_menu">
                        <a @click="_disconnect" :class="_wallet">
                            <span class="flex text-xs lg:text-xs">{{set.wallet.substr(-4)}}</span>
                        </a>
                    </p>
                </div>
            </div>
        </template>

    <!-- footer slot -->
    <template #footer>
    </template>

</SectionLayout>
</template>