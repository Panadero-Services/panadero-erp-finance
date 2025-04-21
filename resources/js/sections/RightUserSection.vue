<script setup>
import { ref,  onMounted, computed } from 'vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserIcon } from '@heroicons/vue/24/outline'
import { UsersIcon } from '@heroicons/vue/24/outline'

import ActionMessage from '@/components/ActionMessage.vue';
import FormSection from '@/components/FormSection.vue';

import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';

import UpdateProfileInformationFormAlternative from '@/pages/Profile/Partials/UpdateProfileInformationFormAlternative.vue';

// subsections
import UserProfileSection from '@/sections/user-section/UserProfileSection.vue';
import UserOtherSection from '@/sections/user-section/UserOtherSection.vue';
import UserModeSection from '@/sections/user-section/UserModeSection.vue';

// stores
import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';
const _db = useDbStore();
const _set = useSettingsStore();

const props = defineProps({
    user: Object
});

const form = useForm({
   user: props.user,
   mode: _set.mode 
});

const _shadowColor = ref('indigo');

const _cancel = async () => {
  open.value=false;
}


const msgMeThingsHaveChanged = async () => {
  console.log('msgMeThingsHaveChanged', msgMeThingsHaveChanged);
}

//  'open' command
const open = ref(false)
defineExpose({ open });

// css 
const _userColor="indigo";

//const _shadowColor = 'shadow-'+_userColor+'-700 dark:shadow-'+_userColor+'-200';
const _bgColor = 'bg-indigo-700';
const _hoverColor = 'hover:bg-indigo-500';

const _theme = computed(() => {
return _set.dark ? "bg-indigo-950" : "bg-slate-100";
//return _shadowColor; 
});

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

</script>

<template>
   <TransitionRoot as="template" :show="open">
      <Dialog class="relative z-10" @close="open=false">
         <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-hidden">
               <div class="absolute inset-0 overflow-hidden">
                  <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16 ">
                     <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                        <DialogPanel class="pointer-events-auto w-screen max-w-md">

                        <!-- This is the main div -->
                        <div class="flex h-full flex-col overflow-y-scroll shadow-2xl shadow-indigo-700 dark:shadow-indigo-200"  :class="_set.dark ? 'dark bg-slate-950 shadow-'+_shadowColor+'-600' : 'light bg-gray-100 shadow-'+_shadowColor+'-200'">
                           <form @submit.prevent="submit" class="h-full border-l border-gray-300 dark:border-gray-600">

                              <!-- Title header-->
                              <div class="px-2 py-2 h-12 bg-gradient-to-r bg-gradient-to-l from-gray-50 to-gray-100 dark:from-black dark:to-slate-900">
                                 <div class="items-center justify-between text-slate-600 dark:text-slate-400 text-center">
                                    <div class="flex">
                                       <user-icon class="w-12"/>
                                       <div class="text-xl m-1">
                                          User
                                          <span v-if="_set.mode.dev" class=" ml-2 text-lg text-cyan-600">dev-mode</span>
                                       </div>
                                    </div>

                                    <!-- id subHeader-->
                                    <div class="ml-1 flex h-7 items-center">
                                       <div class="text-sm mt-2 text-right ">
                                          id:{{form.user.id}}
                                       </div>
                                       <div v-if="!(form.user.email_verified_at==null)" class="text-sm ml-2 mt-2 text-right">
                                          verified
                                       </div>
                                       <div v-if="(form.user.email_verified_at==null)" class="text-sm ml-2 mt-2 text-right text-red-600">
                                          not_verified
                                       </div>
                                    </div>

                                 </div>
                              </div>

                              <!-- updated subHeader-->
                              <div class="px-2 py-2 h-10  bg-gradient-to-r bg-gradient-to-r from-slate-50 to-gray-100 dark:from-slate-950 dark:to-indigo-950">
                                 <div class="text-slate-600 dark:text-slate-400 text-right">
                                    <div class="">
                                       <div class="text-sm">updated:
                                          {{form.user.updated_at.substr(0,10).replace('T','-')}}
                                       </div>
                                   </div>
                                    <div class="ml-3 flex h-7 items-center">
                                    </div>
                                 </div>
                              </div>

                              <!-- Photo -->
                              <div class="text-lg divide-slate-300 dark:divide-gray-600 mx-3 text-slate-800 dark:text-slate-400">
                                 <div class="">
                                    Photo
                                 </div>
                              </div>

                              <!-- Picture + name -->
                              <div class="">

                                 <div class="grid grid-cols-6 mt-1 ">

                                    <div class="h-48 col-span-3 ml-3 " v-if="$page.props.jetstream.canUpdateProfileInformation">
                                         <UpdateProfileInformationFormAlternative :user="$page.props.auth.user" />
                                     </div>

                                    <!-- Small Photo Section 
                                       <div class="relative h-12 w-12">
                                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                                       </div>
                                    -->

                                    <div class="relative h-48 w-48 ml-3 col-span-3 rounded-md" :class="_bgColor">
                                       <img class="absolute rounded-md size-full object-cover object-fill" :class="_set.mode.dev? 'brightness-50 opacity-10 ' : 'brightness-75 opacity-25 ' " src="/storage/profile-photos/lieuwe.jpg" alt="" />
                                    </div>

                                 </div>
                              </div>


                              <!-- Profile -->
                              <div class="mt-16 divide-y space-y-2 text-lg divide-slate-300 dark:divide-gray-600 mx-3 text-gray-800 dark:text-slate-400">

            <!-- Title User -->
            <div class=" sm:flex sm:items-end">
               <div class="sm:flex-1">
                  <div class="text-center h-10">
                     <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-300 sm:text-3xl">{{form.user.name}} </h3>
                  </div>
               </div>
            </div>


   <user-profile-section  v-if="_set.mode.full && !_set.mode.noob" :db="_db" :set="_set" :user="user" />
   <user-other-section v-if="_set.mode.advanced && !_set.mode.noob" :form="form" />
   <user-mode-section :set="_set" />

<!-- Button bar2 
<div class="grid grid-cols-5 space-x-1 place-items-end pt-3 ">
<!-- Email 
-->
<!-- Profile Photo
<div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
  
<!-- Profile Photo File Input - ->
<input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">
<InputLabel for="photo" value="Photo" />
<!-- Current Profile Photo - ->
<div v-show="! photoPreview" class="mt-2">
    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
</div>

<!-- New Profile Photo Preview - ->
<div v-show="photoPreview" class="mt-2">
    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center" :style="'background-image: url(\'' + photoPreview + '\');'"/>
</div>
<SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
    Select A New Photo
</SecondaryButton>
<SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2" @click.prevent="deletePhoto">
    Remove Photo
</SecondaryButton>
<InputError :message="form.errors.photo" class="mt-2" />
</div>
<div></div>
<div></div>
<div></div>
<div>
<button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Save</button>
</div>
<div>
<button type="cancel" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Cancel</button>
</div>
</div>
-->





             </div>
          </form>
       </div>
    </DialogPanel>
 </TransitionChild>
</div>
</div>
</div>
</Dialog>
</TransitionRoot>
</template>