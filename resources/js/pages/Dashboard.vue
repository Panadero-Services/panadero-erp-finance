<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();
import {moduleName, moduleGit} from 'panadero-self';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/Components/Banner.vue';
import Welcome from '@/components/Welcome.vue';

// cards
import WelcomeCard from "@/pages/dashboard1/cards/WelcomeCard.vue";
import StakepoolCard from "@/pages/dashboard1/cards/StakepoolCard.vue";
import RegisterCard from "@/pages/dashboard1/cards/RegisterCard.vue";
import WalletCard from "@/pages/dashboard1/cards/WalletCard.vue";
import KleverCard from "@/pages/dashboard1/cards/KleverCard.vue";
import SelfCard from "@/pages/dashboard1/cards/SelfCard.vue";
import BridgeCard from "@/pages/dashboard1/cards/BridgeCard.vue";

import SelfRegisterCard from "@/pages/dashboard1/cards/SelfRegisterCard.vue";
import SelfNameResolvingCard from "@/pages/dashboard1/cards/SelfNameResolvingCard.vue";
import SelfAuctionCard from "@/pages/dashboard1/cards/SelfAuctionCard.vue";
import SelfRegisterLinkCard from "@/pages/dashboard1/cards/SelfRegisterLinkCard.vue";
import SelfStakeLinkCard from "@/pages/dashboard1/cards/SelfStakeLinkCard.vue";
import WhitepaperLinkCard from "@/pages/dashboard1/cards/WhitepaperLinkCard.vue";
import SelfIntro from "@/pages/dashboard1/cards/SelfIntro.vue";
import SelfGame1Card from "@/pages/dashboard1/cards/SelfGame1Card.vue";
import SelfGame1Overlay from "@/pages/dashboard1/cards/SelfGame1Overlay.vue";
import DashboardFooter from "@/pages/dashboard1/DashboardFooter.vue";

import Stars from '@/panaderos/Stars.vue';
import Game from '@/panaderos/Game.vue';
import ApplicationLogo from '@/Components/logoSelf.vue';

const selfVersion ="0.2.1";

const props =defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String
});

// webhooks
onMounted(async ()=> {
   await _set.initMM();
   await _set.initialize();
})

const url = {
    welcome: "https://selfcrypto.io",
    register: "https://dashboard.selfcrypto.io/",
    stakepool: "https://self-inky.vercel.app/staking",
    game: "https://test-dashboard.selfcrypto.io",
    auction: "https://self-auction.netlify.app/"
};

let validMM = computed(() => {
    return _set.isMetaMask;
});

let validWallet = computed(() => {
    return _set.wallet.length > 10;
});

let validNames = computed(() => {
    return _contract.resolvedNames.length > 2;
});

let validNick = computed(() => {
    return _contract.resolvedNames.includes(selfResolve.value);
});

var usdPrice=0.01;

let selfResolve=ref("SELF");


//const windowHeight = window.innerHeight;

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

    function disableScroll() {
        document.body.classList.
        add("stop-scrolling");
    }

    function enableScroll() {
        document.body.classList
        .remove("stop-scrolling");
    }

const _startGame = async () => {
    await _set.game3Toggle();
    if (_set.game3On) disableScroll();
    else enableScroll();
}


const keyUpResolve = async (_selfResolve) => {
    selfResolve.value = _selfResolve;
}

</script>

<template>

    <AppLayout title="Dashboard" :set="_set">

        <template #header>
            <self-game1-overlay v-if="_set.game3On && _set.language=='EN'"  :callSign="selfResolve" :w="1200" :h="1600" />

            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>

            <self-intro />

            <div class="mx-auto max-w-xl lg:max-w-7xl">
                <!--    <ApplicationLogo class="block w-80 h-80" /> -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 py-2 gap-4">
                    <welcome-card :on="_set.web3On" :url="url.welcome" :price="_db.usdPrice" />
                    <stakepool-card :on="_set.web3On" :url="url.stakepool" :apr1="_contract.presaleApr" :apr2="_contract.publicApr"/>
                    <register-card :on="_set.web3On" :url="url.register" :supply="_contract.nftTotalSupply"/>
                    <wallet-card :on="_set.web3On" :wallet="_set.wallet" :names="_contract.resolvedNames.split(',')"/>
                </div>
            </div>

            <div class="mx-auto mt-2 max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-6">

                    <whitepaper-link-card url="url.welcome" :usd1="_db.usdPrice" :usd2="usdPrice" />

                    <self-game1-card v-if="validWallet & validNames" 
                                    :ready="validWallet && validNick && validNick && !_set.game3On" 
                                    :gameOn="_set.game3On"
                                    :overlay="_set.game3On && _set.language=='NL'"
                                    :callSign="selfResolve"
                                    @start="_startGame" />

                    <self-stake-link-card v-if="!(validMM && validWallet)" url="url.stakepool" :presaleApr="parseFloat(_contract.presaleApr)/100" :publicApr="parseFloat(_contract.publicApr)/100" />
                    <self-register-link-card v-if="!(validMM && validNames)" register="register" :nftTotalSupply="_contract.nftTotalSupply" />
                    <self-name-resolving-card v-if="validMM && validWallet" :set="_set" :validNick="validNick" :validMM="validMM" :validWallet="validWallet" @keyUp="keyUpResolve"/>
                    <self-register-card v-if="validMM && validWallet" :set="_set" />
                    <self-auction-card v-if="!(validMM && validNames)" url="url.auction" :set="_set" />
                    <dashboard-footer :laravelVersion="laravelVersion" :phpVersion="phpVersion" :selfVersion="selfVersion" :moduleName="moduleName" :moduleGit="moduleGit" />

                </div>
            </div>

        </template>
    </AppLayout>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.scrollable-place {
    height: 1000px;
}

.stop-scrolling {
    height: 100%;
    overflow: hidden;
}

</style>