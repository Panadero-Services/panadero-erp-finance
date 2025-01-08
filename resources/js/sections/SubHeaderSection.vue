<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3';

import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import NavLink from '@/components/NavLink.vue';
import ResponsiveNavLink from '@/components/ResponsiveNavLink.vue';
import Pulse from '@/panaderos/shared/tools/Pulse.vue';

import { HomeIcon, Bars3Icon, EllipsisVerticalIcon, WrenchIcon, H1Icon, H2Icon, BarsArrowDownIcon } from '@heroicons/vue/24/outline'


const props = defineProps({
    set: Object
});
const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

// buttons ?
const menu = [];

let _animation= ref(true);

//const _pulse = ref(false);
const _pulse = defineModel('pulse')

const setProjectId = (_title) => {
    if(!( usePage().props.auth.user == null)) {
        // toDo !! retrieve from db
        props.set.project.id = _title=='none' ? 0 : 1;
        props.set.project.title = _title;
        props.set.project.environment = "default";
        props.set.project.validEnvironments = ["default","test"];
        props.set.project.category = "primera";
    }
}

const _subHeader=ref(true);

// buttons
const _buttons = ['primera', 'segundo', 'tercera'];
const _changeCat = async (_cat) => { props.set.project.category = _cat; }

// css
const _button ="rounded px-2 py-1 text-xs font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-indigo-300 dark:ring-gray-600 ";
const _hover = "hover:bg-indigo-400 dark:hover:bg-indigo-600";
const _bg = "bg-white dark:bg-black";
const _bgSelected = "bg-indigo-200 dark:bg-indigo-800";
const _hoverAdd = "hover:bg-green-400 dark:hover:bg-green-600";
const _hoverDelete = "hover:bg-red-400 dark:hover:bg-red-600";
const _indigo = " text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-yellow-400 ";





// server Section
const _counter = ref(0);
const loading = ref(false)
const _rnd1 = ref(Math.floor(Math.random() * 500));
const _rnd2 = ref(Math.floor(Math.random() * 500));
const _col1 = ref("bg-blue-500");
const _col2 = ref("bg-blue-500");


const _rnd =  (_mx=800) => {return Math.floor(Math.random() * _mx);};

watch(_pulse, async (_bool) => {
    if (_bool) {
        loading.value = true
    try {
        _counter.value++;
        if(_counter.value > 3){
            _counter.value = 0;
            _rnd1.value = _rnd();
            _rnd2.value = _rnd();
            _col1.value = _rnd1.value < 500 ? 'bg-lime-500' : _rnd1.value < 700 ? 'bg-yellow-500' : 'bg-red-500';
            _col2.value = _rnd2.value < 500 ? 'bg-lime-500' : _rnd2.value < 700 ? 'bg-yellow-500' : 'bg-red-500';
        }
    } catch (error) {
    } finally {
      loading.value = false
    }
  }
})

</script>

<template>
 <nav class="bg-slate-100 dark:bg-slate-950 border-b border-gray-200 dark:border-gray-800">

    <!--Sub Header Icons Navigation Menu -->
    <div class="max-w-9xl">
        <div class="flex justify-between h-10 md:h-10 ">
            <div class="flex bg-red-200">

                    <ul role="list" class="flex">
                        <ellipsis-vertical-icon @click="set.layout.sidebar = !set.layout.sidebar" class="w-10 px-2.5" :class="_indigo" title="toolbar" />
                        <h1-icon @click="set.layout.header = !set.layout.header" class="w-10 px-2.5" :class="_indigo" title="header"/>
                        <h2-icon @click="set.layout.subHeader = !set.layout.subHeader" class="w-10 px-2.5" :class="_indigo" title="subHeader" />
                        <bars-arrow-down-icon @click="set.layout.footer = !set.layout.footer" class="w-10 px-2.5" :class="_indigo" title="footer" />
        

                    <!-- Darkmode -->
                    <div class="w-10 px-2.5 pt-2.5" :class="_indigo" @click="set.darkToggle">
                        {{set.dark ? '🌙' : '☀️'}}
                    </div>

                    <div class="pt-2.5">
                        <!-- Pulse Control -->
                        <pulse @pulse="_pulse=$event" :animation="set.animate"/>
                        <p v-if="_animation">  
                          <span v-if="set.animate" @click="set.animate = false" class="w-10 px-2.5">

                              <span class="relative inline-flex rounded-full h-3 w-3 " :class="_col1"  ></span>
                              <span class="-ml-3.5 animate-ping inline-flex h-4 w-4 rounded-full opacity-75" :class="_col1"></span>
                            <span class="text-xxxs text-text-gray-800 dark:text-gray-200">{{_rnd1}} ms</span>
                          </span>
                          <span v-else class="h-3 w-3 px-2.5" @click="set.animate = true">
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-200 dark:bg-gray-800"></span>
                          </span>
                        </p>
                        <p v-if="set.animate"  class="text-xs w-4" @click="set.animate">
                         <!-- Animation Light -->
                         {{pulse && set.animate}} 
                        </p>
                    </div>


                    <div class="pt-2.5">
                        <!-- Pulse Control -->
                        <pulse @pulse="_pulse=$event" :animation="set.animate"/>
                        <p v-if="_animation">  
                          <span v-if="set.animate" @click="set.animate = false" class="w-10 px-2.5">
                              <span class="relative inline-flex rounded-full h-3 w-3 " :class="_col2"  ></span>
                              <span class="-ml-3.5 animate-ping inline-flex h-4 w-4 rounded-full opacity-75" :class="_col2"></span>
                            <span class="text-xxxs text-text-gray-800 dark:text-gray-200">{{_rnd2}} ms</span>
                          </span>
                          <span v-else class="h-3 w-3 px-2.5" @click="set.animate = true">
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-200 dark:bg-gray-800"></span>
                          </span>
                        </p>
                        <p v-if="set.animate"  class="text-xs w-4" @click="set.animate">
                         <!-- Animation Light -->
                         {{pulse && set.animate}} 
                        </p>
                    </div>


                    </ul>



                <!-- Navigation Links -->
                <template v-for="item in menu" :key="item.name" >
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    </div>
                </template>
            </div>

            <div class="bg-blue-200">abc
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 text-sm bg-green-200">
                <span v-if="set.project.id>0" @click="set.projectVisible = !set.projectVisible" class="mr-0.5 hover:text-black dark:hover:text-yellow-300" :title="set.project.title+'.'+set.project.environment+'.'+set.project.category">project[{{set.project.id}}] </span>
                <span class="mx-1 inline-flex items-center rounded-md bg-indigo-400/10 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-400/30">  {{_counter + _rnd(20)}}</span>

                <div v-if="$page.props.auth.user" class="ms-3 relative">
                    <!-- Teams Dropdown -->
                    <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                        <template #trigger>

                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.current_team.name }}
                                    <svg class="ms-2 -me-0.5 h-3 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </span>

                        </template>

                        <template #content>
                            <div class="w-60">
                                <!-- Team Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                    Team Settings
                                </DropdownLink>

                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                    Create New Team
                                </DropdownLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-200 dark:border-gray-600" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Switch Teams
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <DropdownLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>

                                                    <div >{{ team.name }}</div>
                                                </div>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <div v-else class="text-sm mr-3">
                    <Link :href="route('login')">
                        Log in
                    </Link>
                </div>


                <span class="mx-1 inline-flex items-center rounded-md bg-purple-400/10 px-2 py-1 text-xs font-medium text-purple-400 ring-1 ring-inset ring-indigo-400/30">  {{_counter + _rnd(20)}}</span>


                <!-- Settings Dropdown -->
                <div v-if="$page.props.auth.user" class="ms-2 relative">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-6 w-6 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>

                            <span v-else class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.name }}

                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Manage Account
                            </div>

                            <DropdownLink :href="route('profile.show')">
                                Profile
                            </DropdownLink>

                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                API Tokens
                            </DropdownLink>

                            <div class="border-t border-gray-200 dark:border-gray-600" />

                            <!-- Authentication -->
                            <form @submit.prevent="logout">
                                <DropdownLink as="button">
                                    Log Out
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>




            </div>

            <!-- Hamburger -->
            <div v-if="$page.props.auth.user"  class="-me-2 flex items-center sm:hidden">
                <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>






    <!-- Responsive Navigation Menu -->
    <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                Dashboard
            </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div v-if="$page.props.auth.user"  class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class=" items-center px-4">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                    <img class="h-12 w-12 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                </div>

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ $page.props.auth.user.email }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                    Profile
                </ResponsiveNavLink>

                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                    API Tokens
                </ResponsiveNavLink>

                <!-- Authentication -->
                <form method="POST" @submit.prevent="logout">
                    <ResponsiveNavLink as="button">
                        Log Out
                    </ResponsiveNavLink>
                </form>

                <!-- Team Management -->
                <template v-if="$page.props.jetstream.hasTeamFeatures">
                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Manage Team
                    </div>

                    <!-- Team Settings -->
                    <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                        Team Settings
                    </ResponsiveNavLink>

                    <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                        Create New Team
                    </ResponsiveNavLink>

                    <!-- Team Switcher -->
                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                        <div class="border-t border-gray-200 dark:border-gray-600" />

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            Switch Teams
                        </div>

                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                            <form @submit.prevent="switchToTeam(team)">
                                <ResponsiveNavLink as="button">
                                    <div class="flex items-center">
                                        <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>{{ team.name }}</div>
                                    </div>
                                </ResponsiveNavLink>
                            </form>
                        </template>
                    </template>
                </template>
            </div>
        </div>
    </div>
</nav>
        




</template>