<script setup>
import { ref,  onMounted } from 'vue'

import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserIcon } from '@heroicons/vue/24/outline'

import ActionMessage from '@/components/ActionMessage.vue';
import FormSection from '@/components/FormSection.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';

const props = defineProps({
    user: Object
});

const _user = ref(props.user);

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

//const updateProfileInformation = () => {

const submit = async () => {
    await form.put(route("users.update", props.user, form.id), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => msgMeThingsHaveChanged(),
    });
};

// lifeCycle
onMounted(async ()=> {
});

const msgMeThingsHaveChanged = async () => {
  console.log('msgMeThingsHaveChanged', msgMeThingsHaveChanged);
}



// set 'open' command
const open = ref(false)
defineExpose({ open });

// css 
const _userColor="indigo";

//const _shadowColor = 'shadow-'+_userColor+'-700 dark:shadow-'+_userColor+'-200';
const _shadowColor = 'shadow-indigo-600';
const _bgColor = 'bg-'+_userColor+'-700';
const _bgTitle = 'bg-'+_userColor+'-800';
const _hoverColor = 'hover:bg-'+_userColor+'-500';
const _button = "scale-90 rounded-md border border-indigo-400 py-2 px-4 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-4 disabled:opacity-25";

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10" @close="open = false">
      <div class="fixed inset-0" />

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16 ">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-md">

                <!-- This is the main div -->
                <div class="flex h-full flex-col overflow-y-scroll bg-slate-100 shadow-2xl" :class="_shadowColor">

        <form @submit.prevent="submit" class="">


                <!-- Title -->
                  <div class="px-4 py-2 sm:px-6" :class="_bgTitle">
                    <div class="flex items-start justify-between text-black">
                      <div class="flex"><user-icon class="w-8 " />
                        <div class="text-xl m-1">Profile</div>
                          </div>
                        <div class="ml-3 flex h-7 items-center">
                      </div>
                    </div>
                  </div>
                  
                  <!-- Picture + name -->
                  <div class="pb-1 sm:pb-6">
                    <div>
                      <div class="relative h-96" :class="_bgColor">
                        <img class="absolute size-full object-cover brightness-75 opacity-50" src="/storage/profile-photos/lieuwe.jpg" alt="" />
                      </div>
                      <div class="mt-6 px-4 sm:mt-8 sm:flex sm:items-end sm:px-6">
                        <div class="sm:flex-1">
                            <div class="flex items-center">
                              <h3 class="text-xl font-bold text-indigo-600 sm:text-3xl">{{_user.name}} </h3>
                              <span class="ml-2.5 inline-block size-2 shrink-0 rounded-full" :class="_bgColor">
                                <span class="sr-only">Online</span>
                              </span>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Content -->
                  <div class="pb-1 sm:pb-6">
                    <div>
                      <div class="mt-1 px-4 sm:flex sm:items-end sm:px-6">
                        <div class="flex-1">

                          <div class="text-xl">
                            Profile Information
                          </div>

                          <!-- Name -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="name" value="Name" />
                              <TextInput id="name" v-model="_user.name" type="text" class="mt-1 block w-full" required autocomplete="name"/>
                              <InputError :message="form.errors.name" class="mt-2" />
                          </div>

                          <!-- Email 
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="email" value="Email" />
                              <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
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

                            <button type="submit" class="bg-indigo-600 text-white" :class="_button">Save</button>

                       
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