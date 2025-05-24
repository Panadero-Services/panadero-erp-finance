<script setup>
import {computed, ref, onMounted} from 'vue';
//import { useForm } from "@inertiajs/vue3";
import { ChevronRightIcon } from '@heroicons/vue/20/solid';
import {  XMarkIcon, WrenchIcon, Bars3Icon, LockClosedIcon, WalletIcon } from '@heroicons/vue/24/outline';

import TheButton from "@/panadero/components/TheButton.vue";
import { Switch } from '@headlessui/vue'

const props = defineProps({
    form: Object, 
    owner: String,
    self: String, 
    selfAuth: Boolean,
    selfAdminAuth: Boolean
});
const _file = props.form.file;

const extendedMode = ref(false);
const safeMode = ref(true);
const emit = defineEmits(['shoot'])

const submit = async () => {
    console.log('section update submitted');
    await props.form.put(route("sections.update", props.form.id));
    emit('shoot',props.form.id);
};

const insertSection = async () => {
    await props.form.post(route("sections.store", props.form.id));
    console.log('section inserted', props.form.file);
};

const _input = "bg-indigo-100 focus:bg-white dark:bg-gray-900 dark:focus:bg-black focus:ring-1 text-gray-700 dark:text-gray-400 dark:focus:text-gray-300 block w-full rounded-md dark:border-gray-500 border-gray-300 shadow-sm focus:ring-indigo-700 sm:text-sm text-xs";
const _label = "block text-sm font-medium text-gray-700 dark:text-gray-200 mt-2 ";
const _button = "scale-90 rounded-md border border-indigo-400 py-2 px-4 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-4 disabled:opacity-25";
/* Breakpoint prefix Minimum width CSS
sm  640px @media (min-width: 640px) { ... }
//md  768px @media (min-width: 768px) { ... }
lg  1024px  @media (min-width: 1024px) { ... }
//xl  1280px  @media (min-width: 1280px) { ... }
*/
const _icon = "w-4 h-4 text-indigo-800 dark:text-indigo-400 ";

</script>

<template >

<!-- form -->
      <div class=" md:col-span-1 mt-1 mx-4 sm:mx-6 lg:mx-8 outline-2 border-2 border-gray-300 hover:border-gray-400 dark:hover:border-gray-700 dark:border-gray-800 p-2"  >
       
        <div class="grid grid-cols-2 text-left">
            <div class="text-xs -mt-2 -ml-4 scale-75 text-indigo-700 dark:text-yellow-500 lg:scale-90 uppercase">{{owner}}.form
              
            </div>
            <div @click="$emit('shoot',0)" class="text-end text-indigo-500 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-500 font-bold -mt-2">x
            </div>

        </div>

        <form @submit.prevent="submit" class="">
            <div class="divide-y divide-gray-300 -mt-2">
            </div>
            <div class="grid sm:grid-cols-8 gap-2">

<!-- form.id -->
            <div v-if="form.settings.includes('id')" class="mt-1 opacity-50">
                <label for="id" :class="_label">id</label>
                <div class="mt-1 ">
                    <input type="text" v-model="form.id" disabled="true"  :class="_input"  />
                </div>
            </div>

<!-- form.title -->
            <div v-if="form.settings.includes('title')" class="col-span-4 mt-1">
                <label for="title" :class="_label"> Title</label>
                <div class="mt-1 ">
                    <input type="text" v-model="form.title"  :class="_input" />
                </div>
            </div>

<!-- form.file -->
            <div v-if="form.settings.includes('file')" class="col-span-3 mt-1" :class="safeMode ? 'opacity-50' : ''">
                <label for="file" :class="_label"> File</label>
                <div class="mt-1 ">
                    <input type="text" :disabled="safeMode" v-model="form.file"  :class="_input" />
                </div>
            </div>

<!-- form.priority -->
            <div v-if="form.settings.includes('priority')" class="col-span-2 lg:col-span-1 mt-1">
                <label for="priority" :class="_label"> Priority</label>
                <div class="mt-1 ">
                    <input type="number" v-model="form.priority"  :class="_input" />
                </div>
            </div>


<!-- form.page_id -->
            <div v-if="extendedMode & form.settings.includes('page_id')" class="col-span-2 lg:col-span-1 mt-1" :class="safeMode ? 'opacity-50' : ''">
                <label for="page_id" :class="_label">page_id</label>
                <div class="mt-1">
                    <input type="number" :disabled="safeMode" v-model="form.page_id" :class="_input" >
                </div>
            </div>

<!-- form.icon -->
            <div v-if="form.settings.includes('icon')" class="col-span-4 lg:col-span-3 mt-1">
                <label for="icon" :class="_label">Icon</label>
                <div class="mt-1">
                    <select v-model="form.icon" :class="_input">
                        <option>-</option>
                        <option>Logo</option>
                        <option>ApplicationLogo</option>
                        <option>SilverLogo</option>
                        <option>Self1</option>
                        <option>Self2</option>
                        <option>Self3</option>
                        <option>I2</option>
                        <option>I2lte</option>
                    </select>
                </div>
            </div>



<!-- form.image -->
            <div v-if="form.settings.includes('image')" class="col-span-4 lg:col-span-3 mt-1">
                <label for="image" :class="_label">Image</label>
                <div class="mt-1">
                    <select v-model="form.image" :class="_input">
                        <option>-</option>
                        <option>hack.png</option>
                        <option>dashboard5.jpg</option>
                        <option>cloud5.png</option>
                    </select>
                </div>
            </div>


<!-- form.subtitle -->
            <div v-if="form.settings.includes('subtitle')" class="col-span-8">
                <label for="subtitle" :class="_label">Subtitle</label>
                <div class="mt-1">
                    <textarea type="text" rows="4" v-model="form.subtitle" :class="_input" />
                </div>
            </div>

<!-- form.slogan -->
            <div v-if="form.settings.includes('slogan')" class="col-span-8">
                <label for="slogan" :class="_label">Slogan</label>
                <div class="mt-1">
                    <textarea type="text" rows="1" v-model="form.slogan"  :class="_input" />
                </div>
            </div>

<!-- form.css -->
            <div v-if="form.settings.includes('css')" class="col-span-8" :class="safeMode ? 'opacity-50' : ''">
                <label for="css" :class="_label">CSS</label>
                <div class="mt-1">
                    <textarea type="text" rows="1" :disabled="safeMode" v-model="form.css"  :class="_input" />
                </div>
            </div>


<!-- form.self -->
            <div v-if="form.settings.includes('self')" class="col-span-8">

                <label for="self" :class="selfAuth ? _label+' text-green-600 font-bold' : _label">SELF</label>
                <div class="mt-1">
                    <textarea type="text" rows="1" v-model="form.self"  :class="_input" />
                </div>
            </div>

<!-- form.self_admin -->
            <div v-if="form.settings.includes('self_admin')" class="col-span-8">
                <label for="self_admin" :class="selfAdminAuth ? _label+' text-green-600 font-bold' : _label">SELF_ADMIN</label>
                <div class="mt-1">
                    <textarea type="text" rows="1" v-model="form.self_admin"  :class="_input" />
                </div>
            </div>

<!-- form.html -->
            <div v-if="form.settings.includes('html')" class="col-span-8">
                <label for="html" :class="_label">Html</label>
                <div class="mt-1">
                    <textarea type="text" rows="4" v-model="form.html"  :class="_input" />
                </div>
            </div>

<!-- form.features -->
            <div v-if="form.settings.includes('features')" class="col-span-8">
                <label for="features" :class="_label">Features</label>
                <div class="mt-1">
                    <textarea type="text" rows="4" v-model="form.features"  :class="_input" />
                </div>
            </div>

<!-- form.settings -->
            <div v-if="extendedMode" class="col-span-8" :class="safeMode ? 'opacity-50' : ''">
                <label for="settings" :class="_label">Settings</label>
                <div class="mt-1">
                    <textarea type="text" rows="1" :disabled='safeMode' v-model="form.settings"  :class="_input" />
                </div>
            </div>

            <div class="col-span-8 my-2">

                <div class=" text-center">

                    <!-- extendedMode -->
                    <div class="flex justify-start my-2 ">
                            <div class=" grid grid-cols-3 sm:grid-cols-6 lg:grid-cols-6 text-center ">

                                <!-- extendedMode -->
                                <div class=" ml-2">
                                    <label for="extendedMode" class="block text-xs font-medium text-gray-700 mb-2 dark:text-white">extendedMode</label>
                                    <Switch v-model="extendedMode" :class="[extendedMode ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span aria-hidden="true" :class="[extendedMode ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                  </Switch>
                                </div>

                                <!-- switch animate -->
                                <div class="ml-2" >
                                    <label for="animate" class="block text-xs font-medium text-gray-700 mb-2 dark:text-white">animate</label>
                                    <Switch v-model="form.animate"  :class="[form.animate ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span class="sr-only">form.animate</span>
                                        <span aria-hidden="true"  :class="[form.animate ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                  </Switch>
                                </div>

                                <!-- switch safeMode -->
                                <div class="ml-2">
                                    <label for="safeMode" class="block text-xs font-medium  mb-2" :class="safeMode ? 'text-gray-700  dark:text-white' : 'text-red-500'">safeMode</label>
                                    <Switch v-model="safeMode" :class="[safeMode ? 'bg-green-600' : 'bg-red-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span class="sr-only">safeMode</span>
                                        <span aria-hidden="true" :class="[safeMode ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                    </Switch>
                                </div>

                                <!-- switch self_auth -->
                                <div class="ml-2" :class="safeMode ? 'opacity-50' : ''">
                                    <label for="self_auth" class="block text-xs font-medium text-gray-700 mb-2 dark:text-white">selfAuth</label>
                                    <Switch v-model="form.self_auth"  :disabled='safeMode' :class="[form.self_auth ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span class="sr-only">form.self_auth</span>
                                        <span aria-hidden="true" :class="[form.self_auth ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                  </Switch>
                                </div>

                                <!-- switch is_active -->
                                <div class="ml-2" :class="safeMode ? 'opacity-50' : ''">
                                    <label for="is_active" class="block text-xs font-medium text-gray-700 mb-2 dark:text-white">isActive</label>
                                    <Switch v-model="form.is_active" :disabled='safeMode' :class="[form.is_active ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                        <span class="sr-only">form.is_active</span>
                                        <span aria-hidden="true"  :class="[form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                                  </Switch>
                                </div>

                            </div>
                        </div>

                        <!-- butttons: close save new  -->
                        <div class="flex justify-center sm:justify-end my-2 mr-12 sm:mr-2">
                            <button type="button" @click="$emit('shoot',0)" class="bg-white text-gray-500" :class="_button">Close</button>
                            <button type="submit" class="bg-indigo-600 text-white" :class="_button">Save</button>
                            <button  type="button" @click="insertSection" :disabled="form.file==_file" class="bg-indigo-600 text-white" :class="_button">New</button>
                        </div>
                    </div>
                </div>

          </div>
        </form>
    </div>

</template>
