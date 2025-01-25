<script setup>
import { ref,  onMounted, computed } from 'vue'

import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserIcon } from '@heroicons/vue/24/outline'
import { Switch } from '@headlessui/vue'

import ActionMessage from '@/components/ActionMessage.vue';
import FormSection from '@/components/FormSection.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';


import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';
const _db = useDbStore();
const _set = useSettingsStore();

const props = defineProps({
    user: Object
});

const _loading = ref(false);
const _msg=ref({style: 'text-yellow-400', txt: ""});

const form = useForm({
    user: props.user,
    mode: _set.mode
});

//const updateProfileInformation = () => {
const _update = async () => {

  _loading.value = true;
  const _start = new Date().getTime();
  _msg.value = {style: 'text-yellow-600 dark:text-yellow-300', txt: "updateProfile requested..."};
  const _model = "User";
  _set.mode = form.mode;
  form.user.json = JSON.stringify(_set);
  const _payload = form.user;
  console.log(form.user);

  let _response = await _db.setUser(_model, _payload);

  const _elapsed = new Date().getTime() - _start;

  if(_set.mode.dev) _response += '--> _elapsed:(' + _elapsed + ' ms)' ;

  _msg.value = {style: 'text-green-500', txt: _response };

  //_set.banner= {active: true, msg:_msg.value, style:"success"};

  _loading.value = false;

}

const _cancel = async () => {
  open.value=false;
}

// DEPRECATED 22 jan 2025
/*
const _submit = async () => {
console.log(form.user);
    await form.put(route("users.update",form.user), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => msgMeThingsHaveChanged(),
    });
};
*/

// lifeCycle
onMounted(async ()=> {
});

const msgMeThingsHaveChanged = async () => {
  console.log('msgMeThingsHaveChanged', msgMeThingsHaveChanged);
}

//  'open' command
const open = ref(false)
defineExpose({ open });

// css 
const _userColor="indigo";

//const _shadowColor = 'shadow-'+_userColor+'-700 dark:shadow-'+_userColor+'-200';
const _shadowColor = 'shadow-indigo-600 ';
const _bgColor = 'bg-indigo-700';
const _successColor = 'bg-green-600';
const _hoverColor = 'hover:bg-indigo-500';
const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

const _theme = computed(() => {
return _set.dark ? "bg-indigo-950" : "bg-slate-100";
//return _shadowColor; 
});

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
                <div class="flex h-full flex-col overflow-y-scroll shadow-xl"  :class="_set.dark ? 'dark bg-slate-950 shadow-indigo-600' : 'light bg-indigo-50 shadow-indigo-200'">
                  <form @submit.prevent="submit" class="h-full border-l border-gray-400">

                <!-- Title -->
                  <div class="px-2 py-2 h-12 bg-gradient-to-r bg-gradient-to-l from-indigo-100 to-slate-200 dark:from-black dark:to-slate-900">
                    <div class="items-center justify-between text-slate-600 dark:text-slate-400 text-center">
                      <div class="flex"><user-icon class="w-12" />
                        <div class="text-xl m-1">User<span v-if="_set.mode.dev" class=" ml-2 text-lg text-cyan-600">dev-mode</span></div>

                          </div>
                        <div class="ml-1 flex h-7 items-center">
                        <div class="text-sm mt-2 text-right ">id:{{form.user.id}}</div>
                        <div v-if="!(form.user.email_verified_at==null)" class="text-sm ml-2 mt-2 text-right">verified</div>
                        <div v-if="(form.user.email_verified_at==null)" class="text-sm ml-2 mt-2 text-right text-red-600">not_verified</div>

                      </div>
                    </div>
                  </div>
                  
                  <div class="px-2 py-2 h-10  bg-gradient-to-r bg-gradient-to-r from-slate-100 to-indigo-100 dark:from-slate-950 dark:to-indigo-950">
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

                  <!-- Picture + name -->
                  <div class="mt-4 pb-1 sm:pb-1">



                      <div class="mt-4 px-3 sm:flex sm:items-end">
                        <div class="sm:flex-1">
                            <div class="text-center h-10">
                              <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-300 sm:text-3xl">{{form.user.name}} </h3>
                            </div>
                        </div>
                      </div>



                    <div class="grid grid-cols-2 mt-4">
                      <div class="relative h-48" :class="_bgColor">
                        <img class="absolute size-full object-cover object-fill" :class="_set.mode.dev? 'brightness-50 opacity-10 ' : 'brightness-75 opacity-25 ' " src="/storage/profile-photos/lieuwe.jpg" alt="" />
                      </div>
                      <div class="flex grid grid-cols-3 m-2">
                        <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                        <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                                                <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                       <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                        <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                                                <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                                               <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                        <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                                                <div class="relative h-12 w-12">
                          <img class="absolute size-full object-cover object-fill rounded-full"  src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                      </div>
                    </div>

                  </div>


                  <!-- Content -->
                  <div class="mt-4 divide-y space-y-2 text-lg divide-slate-300 dark:divide-gray-600 mx-3 text-gray-800 dark:text-slate-400">
                        <div class=" mb-2">
                            Profile Information
                          <!-- Name -->
                          <div class="flex col-span-6 sm:col-span-4 mt-2">
                              <InputLabel for="name" value="Name" class="mt-2 mr-2"/>
                              <TextInput id="name" v-model="form.user.name" type="text" class="mt-1 block w-full" required />
                              <InputError :message="form.errors.name" class="mt-2" />
                          </div>

                          <div class="flex col-span-6 sm:col-span-4 mt-2">
                              <InputLabel for="email" value="Email" class="mt-2 mr-2"/>
                              <TextInput id="email" v-model="form.user.email" type="email" class="mt-1 block w-full" required />
                              <InputError :message="form.errors.email" class="mt-2" />

                              <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                                  <p class="text-sm mt-2 dark:text-white">
                                      Your email address is unverified.
                                      <Link :href="route('verification.send')" method="post" as="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" @click.prevent="sendEmailVerification">
                                          Click here to re-send the verification email.
                                      </Link>
                                  </p>
                                  <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                      A new verification link has been sent to your email address.
                                  </div>
                              </div>
                          </div>

                    <!-- Button bar1 -->
                    <div class="grid grid-cols-5 space-x-1 place-items-end py-2">

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
                          -->
                          <div class="text-xs col-span-2" :class="_msg.style">{{_msg.txt}}</div>
                          <div></div>
                          <div>
                            <button type="update" :disabled="_loading" @click="_update" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Save</button>
                          </div>
                          <div>
                            <button type="cancel" :disabled="_loading" @click="_cancel" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Cancel</button>
                          </div>
                    </div>
                        </div>


                       <!-- SubCategory Other Information -->
                        <div v-if="_set.mode.dev" class="pt-3 mb-4">
                            Other Information

                           <!-- email_verified_at -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>email_verified_at:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.email_verified_at.substr(0,19).replace('T',' ' )}}</div>
                          </div>

                           <!-- two_factor_confirmed_at -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>two_factor_confirmed_at:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.two_factor_confirmed_at}}</div>
                          </div>

                           <!-- current_team_id -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>current_team_id:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.current_team_id}}</div>
                          </div>

                           <!-- current_team -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>current_team:</div>

                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> 

                                  <span v-if="form.user.current_team.personal_team" class="mr-2 inline-block size-2 shrink-0 rounded-full" :class="_successColor" />

                                {{form.user.current_team.name}}</div>
                          </div>

                           <!-- created_at -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>created_at:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.created_at.substr(0,19).replace('T',' ' )}}</div>
                          </div>

                           <!-- updated_at -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>updated_at:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.updated_at.substr(0,19).replace('T',' ' )}}</div>
                          </div>

                       <!-- profile_photo_path -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>profile_photo_path:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.profile_photo_path.substr(0,36)}} ....</div>
                          </div>

                       <!-- profile_photo_url" -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>profile_photo_url":</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.profile_photo_url.substr(0,36)}} ....</div>
                          </div>


                       <!-- profile_photo" -->
                          <div class="grid grid-cols-3 text-xs mx-4 text-slate-600 dark:text-slate-400"> <div>profile_photo:</div>
                            <div class="col-span-2 text-slate-800 dark:text-slate-300 text-right text-xxs mt-1"> {{form.user.profile_photo_url.substr(44)}} </div>
                          </div>
                      </div>

<div class="pt-3 mb-4">Modes

<div class="flex gap-x-8 mt-1 text-xs ml-2 text-center">
  <div></div>
  <div>
    <label for="first.first" class="block text-gray-700 mb-2 dark:text-slate-300">First</label>
      <Switch v-model="form.mode.first" :disabled='false' :class="[form.mode.first ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
        <span class="sr-only">form.mode.first</span>
        <span aria-hidden="true"  :class="[form.mode.first ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
      </Switch>
    </div>
  <div>
    <label for="mode.noob" class="block text-gray-700 mb-2 dark:text-slate-300">Noob</label>
      <Switch v-model="form.mode.noob" :disabled='false' :class="[form.mode.noob ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
        <span class="sr-only">form.mode.noob</span>
        <span aria-hidden="true"  :class="[form.mode.noob ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
      </Switch>
  </div>
  <div>
    <label for="mode.full" class="block text-gray-700 mb-2 dark:text-slate-300">Full</label>
      <Switch v-model="form.mode.full" :disabled='false' :class="[form.mode.full ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
        <span class="sr-only">form.mode.full</span>
        <span aria-hidden="true"  :class="[form.mode.full ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
      </Switch>
  </div>
  <div>
    <label for="mode.advanced" class="block text-gray-700 mb-2 dark:text-slate-300">Advanced</label>
      <Switch v-model="form.mode.advanced" :disabled='false' :class="[form.mode.advanced ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
        <span class="sr-only">form.mode.advanced</span>
        <span aria-hidden="true"  :class="[form.mode.advanced ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
      </Switch>
  </div>
  <div>
    <label for="mode.dev" class="block text-gray-700 mb-2 dark:text-slate-300">Dev</label>
      <Switch v-model="form.mode.dev" :disabled='false' :class="[form.mode.dev ? 'bg-green-600 dark:bg-green-800' : 'bg-gray-200 dark:bg-slate-500', 'relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
        <span class="sr-only">form.mode.dev</span>
        <span aria-hidden="true"  :class="[form.mode.dev ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white dark:bg-slate-400 shadow ring-0 transition duration-200 ease-in-out']" />
      </Switch>
  </div>
</div>



                       





                    <!-- Button bar2 -->
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
                          -->
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


                        </div>



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