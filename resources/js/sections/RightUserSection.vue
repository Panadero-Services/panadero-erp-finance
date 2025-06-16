<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { UserIcon } from '@heroicons/vue/24/outline';

import UpdateProfileInformationFormAlternative from '@/pages/Profile/Partials/UpdateProfileInformationFormAlternative.vue';

// Sections
import UserProfileSection from '@/sections/user-section/UserProfileSection.vue';
import UserOtherSection from '@/sections/user-section/UserOtherSection.vue';
import UserModeSection from '@/sections/user-section/UserModeSection.vue';

// Stores
import { useSettingsStore } from '@/stores/settings';
import { useDbStore } from '@/stores/db';

// Reactive stores
const settings = useSettingsStore();
const db = useDbStore();
const page = usePage();

// Props
const props = defineProps({
  user: Object
});

// Form
const form = useForm({
  user: props.user,
  mode: settings.mode
});

// State
const open = ref(false);
const shadowColor = ref('indigo');

// Expose
defineExpose({ open });

// Computed
const themeClass = computed(() =>
  settings.dark ? 'bg-indigo-950' : 'bg-slate-100'
);

const buttonClass = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

const actualPicture = computed(() => page.props.auth.user.profile_photo_url);

const formattedUser = computed(() =>
  JSON.stringify(page.props.auth.user.profile_photo_path, null, 2)
);

// Cancel Handler
const cancel = () => {
  console.log("cancellled")
  open.value = false;
};


const baseButton = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset";
const buttonStyles = {
  default: `${baseButton} bg-white dark:bg-black min-w-16 text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 dark:hover:ring-indigo-400`,
  disabled: `${baseButton} text-gray-300 ring-gray-300 dark:text-gray-600 dark:ring-gray-600`,
};

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10" @close="cancel">
      <div class="fixed inset-0 bg-black bg-opacity-25" />

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-md">
                <div
                  class="flex h-full flex-col overflow-y-scroll shadow-2xl"
                  :class="settings.dark
                    ? 'dark bg-slate-950 shadow-' + shadowColor + '-600'
                    : 'light bg-gray-100 shadow-' + shadowColor + '-200'"
                >
                  <form @submit.prevent class="h-full border-l border-gray-300 dark:border-gray-600">

                    <!-- Header -->
                    <div class="px-2 py-2 h-12 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-black dark:to-slate-900">
                      <div class="flex items-center text-slate-600 dark:text-slate-400">
                        <UserIcon class="w-12" />
                        <div class="text-xl ml-2">
                          User
                          <span v-if="settings.mode.dev" class="ml-2 text-lg text-cyan-600">dev-mode</span>
                        </div>
                      </div>
                      <div class="ml-1 flex h-7 items-center text-sm mt-2">
                        <span>id: {{ form.user.id }}</span>
                        <span class="ml-2" :class="form.user.email_verified_at ? 'text-green-600' : 'text-red-600'">
                          {{ form.user.email_verified_at ? 'verified' : 'not_verified' }}
                        </span>
                      </div>
                    </div>

                    <!-- Updated -->
                    <div class="px-2 py-2 h-10 bg-gradient-to-r from-slate-50 to-gray-100 dark:from-slate-950 dark:to-indigo-950">
                      <div class="text-right text-sm text-slate-600 dark:text-slate-400">
                        updated: {{ form.user.updated_at?.substr(0, 10).replace('T', '-') }}
                      </div>
                    </div>

                    <!-- Photo -->
                    <div class="mx-3 mt-4 text-lg text-slate-800 dark:text-slate-400 text-sm">Photo</div>
                    <div class="grid grid-cols-6 mt-1 mx-3">
                      <div class="h-48 col-span-3 " v-if="$page.props.jetstream.canUpdateProfileInformation">
                        <UpdateProfileInformationFormAlternative :user="$page.props.auth.user" />
                      </div>
                      <div class="relative h-48 w-48 col-span-3 ml-3 rounded-lg" :class="themeClass">
                        <img
                          class="absolute rounded-lg w-full h-full object-cover"
                          :class="settings.mode.dev ? 'brightness-25 opacity-25' : 'brightness-25 opacity-50'"
                          :src="actualPicture"
                          alt="User photo"
                        />
                      </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="mt-8 mx-3 divide-y space-y-2 text-lg text-gray-800 dark:text-slate-400 divide-slate-300 dark:divide-gray-600">
     <!-- 
                      <div class="text-center h-10">
                        <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-300 sm:text-3xl">
                          {{ form.user.name }}
                        </h3>
                      </div>
-->
                      <!-- Subsections -->
                      <UserProfileSection v-if="settings.mode.full && !settings.mode.noob" :db="db" :set="settings" :user="user" />
                      <UserOtherSection v-if="settings.mode.advanced && !settings.mode.noob" :form="form" />
                      <UserModeSection :set="settings" />
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-5 gap-2 mt-4 px-3">
                      <button type="submit" :class="buttonStyles.default">Save</button>
                      <button type="button" @click="cancel" :class="buttonStyles.default" @cancel="cancel">Cancel</button>
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
