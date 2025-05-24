<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue'
import { useForm } from "@inertiajs/vue3";
import EditPageModal from "@/pages/modals/EditPageModal.vue";
import EditRecordModal from "@/pages/modals/EditRecordModal.vue";
import PublicLayout from '@/layouts/PublicLayout.vue';

// sections
import HeaderSection from "@/pages/sections/HeaderSection.vue";
import ArticleSection from "@/pages/sections/ArticleSection.vue";
import FooterSection from "@/pages/sections/FooterSection.vue";
import Header2Section from "@/pages/sections/HeaderSection2.vue";
import RecordsSection from "@/pages/sections/RecordsSection.vue";

// icons
import { LockClosedIcon, WrenchIcon } from '@heroicons/vue/24/outline'
import { PlusIcon } from '@heroicons/vue/24/solid'

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// store.settings
import { useSettingsStore } from '@/stores/settings';
const _set = useSettingsStore();

// store.contract
import { useContractStore } from '@/stores/contracts';
let _contract = useContractStore();

// constants
const _hoverIcon = "transition hover:-translate-y-1 hover:scale-[5] duration-300 h-3 w-3";

// props
const props = defineProps({
    page: Object,
    baseSections: Object,
    table: String,
    pageNr: Number,
    nRecs: Number,
    filter: String
});


// variables
let _poolTimer; 
let _pulse = ref(false);
let editMode= ref(false);
let editRecordMode= ref(false);


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


const _toggleUpdate = ref(false);


const _close = async (_nr) => {
    editMode.value = false;
    editRecordMode.value = false;
    _toggleUpdate.value = !_toggleUpdate.value;
}



const _activeRecord = ref();
const _filter = ref(props.filter);

// webhooks
onMounted(async ()=> {
    if (props.page.animate) await _loopTimer();
})

onUnmounted(async ()=> {
    await clearInterval(_poolTimer); 
})

const _auth = computed(() => {
    // checkf if login
    let _user = _usePage.props.auth.user === null ? { id: 0, name: "none", role_id:0, avatar : "placeholder"} :  _usePage.props.auth.user ;
    // check avatar
    if (_user.avatar == 'placeholder') _user.avatar = "https://painrehabproducts.com/wp-content/uploads/2014/10/facebook-default-no-profile-pic.jpg"; 
    return _user;
});

const _activateRecordMode = async (_idx, _record) => {
    form.id = _record.id;
    _activeRecord.value = _record;
    editRecordMode.value = !editRecordMode.value;
}

const _superSelfAdmin = computed(() => {
  return _set.superSelfAdmins.includes(_set.self);
});

</script>

<template>
    <PublicLayout :set="_set" :page="page" :contract="_contract" :auth="_auth">

        <!-- editRecordModal -->
        <div v-if="editRecordMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
            <edit-record-modal :page="page" lng='en' :record="_activeRecord" :table="table" @close="_close" :superSelfAdmin="_superSelfAdmin==1" />
        </div>

        <!-- editPageModal + wrench -->
        <div v-if="_set.isSelfAdmin">
            <div v-if="editMode" class="col-span-2 md:col-span-1 mt-4 sm:mt-12 lg:mt-16 mx-4 sm:mx-6 lg:mx-8">
                <edit-page-modal :page="page" lng='en' @close="_close"  />
            </div>
            <div v-if="_set.wrenchMode" class="z-10 absolute top-1 left-2" :class="_icon+_hoverIcon" @click="editMode=!editMode" title="edit the page">
                <WrenchIcon :class="_hoverIcon"  />
            </div>
        </div>

        <div class="flex grid sm:grid-cols-2 lg:grid-cols-6"> 

            <!-- baseSections = general sections  .... page_id==0 ... before page specific sections -->
            <div v-for="section in baseSections" :class="section.css" >
               <!--   0 Basic Public Header .... -->
                <div v-if="page.header" class="">
                    <Header2Section v-if="section.file =='HeaderSection2.vue'" :section="section" :set="_set" :page="page" :contract="_contract" :auth="_auth" />
                </div>
            </div>
            <div v-for="section in page.sections" :class="section.css">
                <div v-if="section.is_active">
    
                    <!--  9 .. -->
                    <div v-if="_auth.id || _set.superSelfAdmins.includes(_set.self)">
                        <RecordsSection v-if="section.file =='RecordsSection.vue'" :page="page" :set="_set" :section="section" :table="table" :pageNr="pageNr" :nRecs="nRecs" @open="_activateRecordMode" :filter="_filter" :superSelfAdmin="_superSelfAdmin" :toggleUpdate="_toggleUpdate"/>
                    </div>
                    <div v-else>🔒</div>
                </div>
                <div v-else @click="section.is_active = true" class="text-red-600 text-xs text-right">
                    {{section.file}} inactive
                </div>

            </div>

            <!-- baseSections = general sections  .... page_id==0 ... after page specific sections -->
            <div v-for="section in baseSections" class="" :class="section.css" >
                <!--   15 Here Included FooterSection !! .... -->
                <div v-if="page.footer">
                    <FooterSection v-if="section.file =='FooterSection.vue'" :section="section" :set="_set" :page="page" :contract="_contract" />
                </div>
            </div>

        </div>

    </PublicLayout>

</template>

<style>
    
</style>
