<script setup>
import {computed, ref, onMounted} from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/components/NavLink.vue';

import ApplicationMark from '@/components/ApplicationMark.vue';
//import ApplicationMark from '@/layouts/logos/self1.png';
import self1 from "@/layouts/logos/self1.png";
import self2 from "@/layouts/logos/self2.png";
import { HomeIcon, WrenchIcon, LockClosedIcon, LockOpenIcon, WalletIcon, CakeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    set: Object,
    contract: Object
});

const menu = [
    { name: 'Dashboard', url: route('dashboard'), route: 'dashboard', when:() => usePage().props.auth.user },
    { name: 'Tiers', url: route('tiers'), route: 'tiers' },
    { name: 'Posts', url: route('posts'), route: 'posts', when:() => usePage().props.auth.user },
    { name: 'Bento', url: route('bento'), route: 'bento', when:() => usePage().props.auth.user },
    { name: 'Bots', url: route('bots'), route: 'bots', when:() => usePage().props.auth.user },
   // { name: 'Planning', url: route('planning'), route: 'planning', when:() => usePage().props.auth.user },
    { name: 'Resources', url: route('resources'), route: 'resources', when:() => usePage().props.auth.user },
    { name: 'Web3', url: route('web3'), route: 'web3', when:() => usePage().props.auth.user },
    { name: 'Grid', url: route('grid'), route: 'grid', when:() => usePage().props.auth.user },
    { name: 'Mood', url: route('mood'), route: 'mood', when:() => usePage().props.auth.user }

    // READY DEV.. WAITING FOR DEPLOYMENT
    //{ name: 'cms.editor', url: route('cms'), route: 'cms', when:() => usePage().props.auth.user }, 
    //{ name: 'cms.articles', url: route('articles'), route: 'articles', when:() => usePage().props.auth.user }, 
    //{ name: 'cms.categories', url: route('categories'), route: 'categories', when:() => usePage().props.auth.user }, 
    //{ name: 'bots.burnBot'}
    //{ name: 'bots.tradeBot'}
    //{ name: 'self.Auction', url: route('auction'), route: 'auction', when:() => usePage().props.auth.user }, 
    //{ name: 'self.teams'}
    //{ name: 'self.apiKeys'}
    //{ name: '<data.records>'}
    //{ name: 'erp.paycheck'}
    //{ name: 'erp.rca', url: route('rca'), route: 'rca', when:() => usePage().props.auth.user }, 
    //{ name: 'fun.minigames', url: route('minigames'), route: 'minigames', when:() => usePage().props.auth.user }, 

    // DESIGN + DB_SCHEME + BACKEND 50% READY ---> 2025
    //{ name: 'erp.tickets', url: route('tickets'), route: 'tickets', when:() => usePage().props.auth.user }, 
    //{ name: 'self.Marketplace', url: route('marketplace'), route: 'marketplace', when:() => usePage().props.auth.user }, 
    //{ name: 'bots.chatBot'}
    //{ name: 'bots.bridgeBot'}
    //{ name: 'bots.musicBot'}
    //{ name: 'erp.shops'}
    //{ name: 'self.privDashboard1'}
    //{ name: 'self.privDashboard2'}
    //{ name: 'self.privDashboard3'}
    //{ name: 'i3.inventory'}
    //{ name: 'i3.orderhandling'}
    //{ name: 'i3.reporting'}
    //{ name: 'self.premium'}
    //{ name: 'web3.wallet'}
    //{ name: 'web3.pool'}
    //{ name: 'web3.farm'}
    //{ name: 'web3.dex'}
    //{ name: 'web3.chain'}
    //{ name: 'web3.vault'}
    //{ name: 'web3.swaps'}
    //{ name: 'fun.lottery'}
    //{ name: '<data.warehouse>'}
    //{ name: '<data.lake>'}
    //{ name: 'erp.socials'}
    //{ name: '<erp.apps>'}
    //{ name: '<openSourc>'}
    //{ name: '<githubContributors>'}
    //{ name: '<manyMore>'}
];

const _title = 'headerSection';

const _indigo = " text-indigo-700 dark:text-indigo-300 hover:text-black  dark:hover:text-yellow-400 ";
const _user = "  text-sm font-normal leading-tight " +_indigo;
const _user2 =" inline-flex items-center px-2 py-1.5 text-xs font-medium leading-4 text-gray-700 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-500 active:bg-gray-500 ";
const _basic = " text-sm font-normal leading-tight ";
const _menu = " flex items-center  "+ _indigo + _basic;
const _menuClosed = " text-gray-700 text-sm font-normal leading-tight  ";
const _icon = " w-4 h-4 mr-1.5 ";

//const _menu= "text-xs lg:text-sm font-normal text-gray-700 hover:text-black hover:font-semibold dark:text-gray-300 dark:hover:text-yellow-400 ";

const _wallet = computed(() => {
    if (!props.set.isMetaMask) return _indigo.replace('indigo','red');      // no compatible browser
    if (props.set.wallet=='0x0')return _indigo.replace('indigo','gray'); 
    return _indigo; 
   // return " text-blue-600"+_basic;     // ethereum
   // return " text-green-600"+_basic;    // other chain
   // return " text-purple-500"+_basic;   // polygon
   // return " text-yellow-600"+_basic;   // bsc
   // return " text-red-500"+_basic;      // no compatible browser
});

const delay = (s) =>  {  return new Promise(resolve => setTimeout(resolve, s*1000));  }

const _getNextSelf = async () => {
    let _self = await props.contract.getNextSelfName(props.set.wallet);
    await props.set.setSelf(_self);
    console.log(_self);
}
const _connect = async () => { await props.set._connect(props.contract);}
const _disconnect = async () => { await props.set._disconnect(); }

const logout = () => {
    router.post(route('logout'));
};

const login = () => {
    router.get(route('login'));
};

onMounted(async ()=> {
    // check user not  logged in and still project state active...
    if( usePage().props.auth.user == null) if(props.set.projectId > 0) await props.set.setResetProject();
});

</script>


<template>
   <div class="bg-slate-100 dark:bg-black h-12 md:h-16 border-b border-indigo-50 dark:border-black shadow-sm dark:shadow-lg shadow-gray-200 dark:shadow-slate-600 grid grid-cols-3" >
      <div class=" uppercase col-span-2"> 
         <div class="flex items-center space-x-2 md:spac-x-4 p-3">

            <Link href="/">
               <span><img class="m-0.5 hover:rotate-45" :class="_icon" :src="self2"/></span>
            </Link>

            <!-- Navigation Links -->
             <template v-for="item in menu" :key="item.name" >
                     <NavLink v-if="item.when ? item.when() : true" :href="item.url" :active="route().current(item.route)" :class="_menu">
                         {{item.name}}
                     </NavLink>
             </template>
         </div>
        </div>

        <!-- SELF Section -->
        <div> 
            <div class="flex items-end text-right justify-end space-x-2 md:spac-x-4 lg:p-3 ">
                <span v-if="(!(set.wallet=='0x0') && set.isMetaMask && set.self=='nope')" @click="_getNextSelf"><img class="hover:scale-150 hover:rotate-45" style="width:16; height:16px;" :src="self2"/> </span>
                <span v-if="(!(set.wallet=='0x0') && set.isMetaMask && !(set.self=='nope'))" ><img class="hover:rotate-45" style="width:16; height:16px;" :src="self2"/> </span>
                <!-- SELF Connect -->
                <p v-if="(!(set.wallet=='0x0'))" class="relative mt-0.5 ml-2 " :class="_menu">
                    <a  v-if="!(set.self=='nope')" @click="_getNextSelf" :class="_wallet"  >
                        <span class="" :class="_indigo"> {{set.self}} </span>
                    </a>
                </p>
                <p v-if="1" class="relative text-xs mx-2">
                    <a @click="_connect" :class="_wallet" >
                        <WalletIcon class="w-4 h-4 mt-4" :class="_wallet" /> 
                    </a>
                </p>
            <!-- Wallet Connect -->
                <p v-if="(!(set.wallet=='0x0'))" class="relative text-sm mt-0.5  " :class="_menu">
                    <a @click="_disconnect" :class="_wallet"  >
                        <span class="flex"> {{set.wallet.substr(-4)}} </span>
                    </a>
                </p>
            </div>
      </div>
   </div>
</template>