<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue'
import { useForm } from "@inertiajs/vue3";
import EditPageModal from "@/components/modals/EditPageModal.vue";
import WelkomLayout from '@/layouts/WelkomLayout.vue';

// sections
import HeaderSection from "@/sections/HeaderSection.vue";
import WelcomeSection from "@/sections/WelcomeSection.vue";
import FooterSection from "@/sections/FooterSection.vue";

import WrenchMode from "@/panaderos/WrenchMode.vue";

// icons
import { PencilSquareIcon, WrenchIcon } from '@heroicons/vue/24/outline';
import { PlusIcon } from '@heroicons/vue/24/solid'

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// constants
const _hoverDevIcon = "w-3 h-3 text-purple-600 dark:text-purple-400 transition hover:-translate-y-1 hover:scale-[4] duration-300";

// variables
let _poolTimer; 
let _pulse = ref(false);
let editMode= ref(false);

// props
const props = defineProps({
    page: Object,
    baseSections: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String
});

// form
const form = useForm({
    id: props.page.id,
    title: props.page.title,
    icon: props.page.icon,
    image: props.page.image,
    slogan: props.page.slogan,
    type: props.page.type,
    settings: props.page.settings,
    is_active: props.page.is_active,
    header: props.page.header,
    sidebar: props.page.sidebar,
    footer: props.page.footer,
    animate: props.page.animate,
    public: props.page.public,
    max_width: props.page.max_width
});

// functions
const _loopTimer = async () => {
    _poolTimer =
    setInterval( () => {
        _pulse.value = !_pulse.value;
    }, 1000)
}

const _close = async (_nr) => {
    editMode.value = false;
}

// webhooks
onMounted(async ()=> {
    if (props.page.animate) await _loopTimer();
    //router.get('welcome');
    //_set.dark = true;
})

onUnmounted(async ()=> {
    await clearInterval(_poolTimer); 
})

const _auth = computed(() => {
    // checkf if login
    let _user = _usePage.props.auth.user === null ? { id: 0, name: "none", role_id:0, avatar : "placeholder"} :  _usePage.props.auth.user ;
    // check avatar
    //if (_user.avatar == 'placeholder') _user.avatar = "https://painrehabproducts.com/wp-content/uploads/2014/10/facebook-default-no-profile-pic.jpg"; 
    return _user;
});


</script>

<template>
    <html>
        <WelkomLayout :set="_set" :page="page" :contract="_contract" :auth="_auth">
            <!-- editPageModal + wrench -->
            <div v-if="_set.isSelfAdmin">
                <div v-if="editMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
                    <edit-page-modal :page="page" lng='en' @close="_close"  />
                </div>
                <div v-if="_set.wrenchMode" class="z-40 absolute top-32 left-32 " @click="editMode=!editMode" title="edit the page">
                    <WrenchIcon :class="_hoverDevIcon"  />
                </div>
            </div>

            <div class="flex grid sm:grid-cols-2"> 
                <!-- baseSections = general sections  .... page_id==0 ... BEFORE page specific sections -->
                <div v-for="section in baseSections" class="" :class="section.css" >
                   <!--   0 Basic Public Header .... -->
                     <HeaderSection v-if="section.file =='HeaderSection'" :section="section" :set="_set" :page="page" :contract="_contract" />
                </div>

                <!-- page specific sections -->
                <div v-for="section in page.sections" class="" :class="section.css" >
                    <div>
                        <!--   1 Here You Are Finally !! .... -->
                        <WelcomeSection v-if="section.file =='WelcomeSection.vue'" :section="section" :set="_set" :page="page" :pulse="_pulse"/>
                    </div>
                </div>

                <!-- baseSections = general sections  .... page_id==0 ... after page specific sections -->
                <div v-for="section in baseSections" class="" :class="section.css" >
                    <!--   15 Here Included FooterSection !! .... -->
                     <FooterSection v-if="section.file =='FooterSection.vue' && page.footer" :section="section" :set="_set" :page="page" :contract="_contract" short="false" />            
                </div>

                <div v-if="_set.wrenchMode && _set.isSelfAdmin" class="col-span-2 grid lg:grid-cols-2">
                    <wrench-mode :set="_set" :contract="_contract"/>
                </div>

            </div>
        </WelkomLayout>

    </html>
</template>