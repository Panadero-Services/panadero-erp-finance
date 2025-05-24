<script setup>
    import {ref} from 'vue'

    const props = defineProps({
        set: Object,
        contract: Object,
        page: Object,
        use_page: Object
    });

    const _showSettings = ref(false);
    const _showContract = ref(false);
    const _showPage = ref(false);
    const _showUsePage= ref(false);

// css-section : stores/tailwind
    import { useTailwindStore } from '@/stores/tailwind';
    const _css = useTailwindStore();

    //    const _settings = _css.wrench1;
    //    const _contract = _css.wrench2;
    // temporary.... cash error live version
    const _settings = "bg-violet-700 dark:bg-violet-400 text-xs text-white dark:text-black ";
    const _contract = "bg-purple-700 dark:bg-purple-400 text-xs text-white dark:text-black ";

    const _hover = _css.hover;

</script>

<template>

    <div v-if="_showSettings" :class="_settings">
        <p @click="_showSettings = !_showSettings" :class="_hover">SETTINGS</p>
        company: {{set.company}},
        app: {{set.app}},
        language: {{set.language}},
        pageDetail: {{set.pageDetail}},
        web3On: {{set.web3On}},
        game3On: {{set.game3On}},
        wrenchMode:{{set.wrenchMode}},
        self:{{set.self}},
        wallet:{{set.wallet}}
    </div>
    <div v-else :class="_settings">
        <p @click="_showSettings = !_showSettings" :class="_hover">SETTINGS</p>
    </div>

    <div v-if="_showContract" :class="_contract">
        <p @click="_showContract = !_showContract" :class="_hover">CONTRACT</p>
        contractName: {{contract.contractName}},
        resolvedName: {{contract.resolvedName}},
        resolvedNames: {{contract.resolvedNames}},
        publicApr: {{contract.publicApr}},
        presaleApr: {{contract.presaleApr}},
        priceSelf: {{contract.priceSelf}},
        priceJeffrey: {{contract.priceJeffrey}},
        nftTotalSupply: {{contract.nftTotalSupply}},
        chainNr: {{contract.chainNr}},
        retrieveOn: {{contract.retrieveOn}},
    </div>
    <div v-else :class="_contract">
        <p @click="_showContract = !_showContract" :class="_hover">CONTRACT</p>
    </div>

    <div v-if="_showPage" :class="_contract">
        <p @click="_showPage = !_showPage" :class="_hover">PAGE</p>
        id: {{page.id}},
        title: {{page.title}},
    </div>
    <div v-else :class="_contract">
        <p @click="_showPage = !_showPage" :class="_hover">PAGE</p>
    </div>

    <div v-if="_showUsePage" :class="_settings">
        <p @click="_showUsePage = !_showUsePage" :class="_hover">USE_PAGE {{_showUsePage}}</p>
         props: {{use_page}},
    </div>
    <div v-else :class="_settings">
        <p @click="_showUsePage = !_showUsePage" :class="_hover">USE_PAGE {{_showUsePage}}</p>
    </div>







</template>