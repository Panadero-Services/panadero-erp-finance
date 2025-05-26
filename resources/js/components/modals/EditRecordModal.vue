<script setup>
import {computed, ref, onMounted} from 'vue'
import { useForm, usePage } from "@inertiajs/vue3";
import TheButton from "@/panadero/components/TheButton.vue";
import { Switch } from '@headlessui/vue'

const props = defineProps({
    //page: Object,
    lng: String,
    record: Object, 
    module: String,
    table: String,
    superSelfAdmin : Boolean, 
    db: Object
});

const fields = ref([]);
const readOnlyFields = ref(['id','created_at','updated_at']);
const boolFields = ref(['is_active', 'animate', 'sidebar', 'header', 'footer', 'public', 'max_width', 'featured', 'blog', 'smart', 'published', 'locked', 'self']);

const form = useForm(
    props.record
);

onMounted(async ()=> {
 Object.keys(props.record).forEach(key => { fields.value.push(key)  });
})

/*
const form = useForm({
    id: props.record.id,
    title: props.record.title,
    file: props.record.file,
    page_id: props.record.page_id,
    subtitle: props.record.subtitle,
    icon: props.record.icon,
    image: props.record.image,
    slogan: props.record.slogan,
    html: props.record.html,
    css: props.record.css,
    self: props.record.self,
    self_admin: props.record.self_admin,
    features: props.record.features,
    settings: props.record.settings,
    priority: props.record.priority,
    is_active: props.record.is_active,
    self_auth: props.record.self_auth,
    animate: props.record.animate
});
*/

const emit = defineEmits(['close'])

const submit = async () => {
    console.log('section update submitted');
    const _response = await form.put(route(props.table+".update", props.record.id));

    // logAction
    const _logData = {
        action: props.table+".update",
        user_id:  usePage().props.auth.user.id || 'no_uid',
        module: props.module, 
        node: 'none',
        team: usePage().props.auth.user.current_team.name || 'no_team', 
        project: 'none', 
        content: form.title || 'none',
        json: JSON.stringify(form),
        tags: 'content, posts',
    }
    await props.db.logAction( _logData );

    emit('close');
};

const hide = () => {
    console.log('click registered');
}

const _content ={'title':'newPage','type':'content'};
const _grid ={'title':'newPage','type':'grid'};
const _dashboard ={'title':'newPage','type':'dashboard'};

const _f = "form.settings";

</script>

<!-- template-->
<template>
    <div class="">
        <div class="z-30 h-full w-full fixed top-0 left-0 z-20 bg-black opacity-50 dark:opacity-75" v-on:click="hide"></div>

        <div class="scale-75 lg:scale-90 xl:scale-100 dark:text-gray-800 px-4 lg:px-8 bg-indigo-light-100 dark:bg-indigo-dark-600 shadow-lg shadow-green-400 dark:shadow-green-600 rounded-lg text-lg pt-8  w-full lg:w-2/3 max-w-4xl fixed top-1/2 left-1/2 h-3/8 transform -translate-x-1/2 -translate-y-1/2 z-30">
            <div class="pb-4 text-left grid grid-cols-3">
                <div>
                <h3 class="text-xl mb-2 font-semibold dark:text-white ">Edit Record Modal <span class="text-indigo-dark-300 dark:text-indigo-light-300 ">{{table}}</span></h3>
                </div>
            </div>

<!-- form -->
            <form @submit.prevent="submit" class=" divide-y divide-gray-300">
                <div class="divide-y divide-gray-300 -mt-2">
                </div>
            
                    <!-- innovative mode -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4  gap-3 my-3">
                        <div v-for="f in fields" class=""> 
                            <div class="mt-1 ">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">{{f}}</label>
                                
                                <!-- select field based on type and ReadOnly -->
        <!-- if boolean -->     <div v-if="boolFields.includes(f) && superSelfAdmin"> 
                                    <Switch v-model="form[f]" :class="[form[f]  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span class="sr-only">maxWidth</span>
                                        <span aria-hidden="true" :class="[form[f] ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                    </Switch>
                                </div>
                                <div v-else >
                                <div v-if="!readOnlyFields.includes(f) && superSelfAdmin">
        <!-- if string and not ReadOnly --><input v-if="typeof form[f]=='string' || form[f]==NULL" type="text" v-model="form[f]"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm " />
        <!-- if number and not ReadOnly --><input v-if="typeof form[f]=='number'" type="number" v-model="form[f]"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm " />
                                </div>
        <!-- if ReadOnly -->       <div v-else class="dark:text-indigo-light-400 text-indigo-light-700 text-xs line-clamp-2">
                                    {{form[f]}}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- form.buttons -->
                <div class="col-span-3 sm:col-span-6 border-t-2 mb-6  ">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-6"></label>

                    <div class="flex justify-end ">
                        <button type="button" @click="$emit('close')" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</button>
                        <button type="submit" v-if="superSelfAdmin" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                    </div>
                </div>

        </form>
<!-- end form-->

<!-- end temlate -->
        </div>
    </div>
</template>

<style scoped>
.bounce-enter-active {
    animation: bounce-in .5s ease-out both;
}

.bounce-leave-active {
    animation: bounce-in .5s reverse ease-in both;
}

@keyframes bounce-in {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.10);
    }
    100% {
        transform: scale(1);
    }
}
</style>
