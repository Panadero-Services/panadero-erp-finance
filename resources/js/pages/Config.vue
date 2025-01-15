<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';

// page
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/components/Banner.vue';
import FooterSection from "@/sections/FooterSection.vue"

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// modules
import {moduleName, moduleGit} from 'panadero-self';

// components
import SelfIntro from "@/pages/dashboard1/cards/SelfIntro.vue";
import WelcomeCard from "@/pages/config/cards/WelcomeCard.vue";
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

// hooks
onMounted(async ()=> {
  await _set.initMM();
  await _set.initialize();
  _tables.value = await _db.getTables("dataset");

});

const _tables = ref(["whatever"]);
const pulse = ref(false);

const _save = async ()=> {}
const _load = async ()=> {}

const myChild = ref(null);
const _header = ref(true);
const _subHeader = ref(true);

// buttons
const _buttons = ['d1', 's2', 't3'];

// css
const _button ="rounded px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 ";
const _hover = "hover:bg-indigo-400 dark:hover:bg-indigo-600";
const _bg = "bg-white dark:bg-black";
const _bgSelected = "bg-indigo-200 dark:bg-indigo-800";
const _hoverAdd = "hover:bg-green-400 dark:hover:bg-green-600";
const _hoverDelete = "hover:bg-red-400 dark:hover:bg-red-600";

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>

            <Banner />
            <HeaderSection v-if="_header" :set="_set" :contract="_contract"/>
            <SubHeaderSection v-if="_subHeader" :set="_set"/>
                    
            <div v-if="true" class="absolute space-x-2 z-40 " :class="[_header ? 'top-16' : 'top-1', _subHeader ? 'left-32' : 'left-80']" >
              <button v-if="_subHeader" v-for="b in _buttons"  @click="_set.projectTitle=b" type="button" :class="[_button, _hover, b==_set.projectTitle ? _bgSelected : _bg]">{{b}}</button>
              <button v-if="_subHeader" @click="myChild._add" type="button" :class="[_button, _bg, _hoverAdd]">Add</button>
              <button v-if="_subHeader" @click="myChild._delete" type="button" :class="[_button, _bg, _hoverDelete]">Delete</button>
              <button v-if="_subHeader"  @click="_header=!_header" type="button" :class="[_button, _bg, _hover]">Header</button>
              <button @click="_subHeader=!_subHeader" type="button"  :class="[_button, _bg, _hover]">subHeader</button>
            </div>

        </template>

        <template #default>
            <div class="mx-auto max-w-xl lg:max-w-9xl">
                
                <!--    <ApplicationLogo class="block w-80 h-80" /> -->
                <div class="flex p-2 gap-2 items-start">
                    <welcome-card :set="_set" table="pending" :id="1"/>
                    <welcome-card :set="_set" table="actual" :id="2"/>
                    <welcome-card :set="_set" table="finished" :id="3"/>
                    <welcome-card :set="_set" table="todo" :id="4"/>
                </div>
            </div>
        </template>

        <template #footer>

                <div class="grid grid-cols-12 text-xxs">
                    <p  class="m-1 bg-slate-50 border border-indigo-300" v-for="(value, key)  in _tables">
                        {{ value[Object.keys(value)] }}
                    </p>
                </div>
            <FooterSection  :set="_set" :contract="_contract"/>

        </template>
  
    </AppLayout>
</template>
