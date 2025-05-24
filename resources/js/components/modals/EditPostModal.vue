<script setup>
import { useForm } from "@inertiajs/vue3";
import TheButton from "@/components/buttons/TheButton.vue";
import { Switch } from '@headlessui/vue'

const props = defineProps({
    page: Object,
    post: Object,
    lng: String,
});
const form = useForm({
    id: props.page.id,
    title: props.post.title,
    file: props.page.file,
    icon: props.page.icon,
    image: props.page.image,
    slogan: props.page.slogan,
    type: props.page.type,
    settings: props.page.settings,
    
    is_active: props.page.is_active==1,
    header: props.page.header==1,
    sidebar: props.page.sidebar==1,
    footer: props.page.footer==1,
    animate: props.page.animate==1,
    public: props.page.public==1,
    max_width: props.page.max_width==1
});


const emit = defineEmits(['close'])
const submit = async () => {
    console.log('page update submitted');
   // await form.put(route("pages.update", props.page.id));
    emit('close');
};

const hide = () => {
    console.log('click registered');
}

const _content ={'title':'newPage','type':'content'};
const _grid ={'title':'newPage','type':'grid'};
const _dashboard ={'title':'newPage','type':'dashboard'};

</script>

<!-- template-->
<template>
    <div class="">
        <div class="h-full w-full fixed top-0 left-0 z-20 bg-black opacity-50 dark:opacity-75" v-on:click="hide"></div>

        <div class="scale-75 dark:text-gray-800 px-4 lg:px-8 bg-gray-50 dark:bg-indigo-900 rounded-lg text-lg pt-8 shadow-lg w-full lg:w-2/3 max-w-4xl fixed top-1/2 left-1/2 h-3/8 transform -translate-x-1/2 -translate-y-1/2 z-30 opacity-95  ">
            <div class="pb-4 text-left">
                <h3 class="text-xl mb-2 font-semibold dark:text-white ">Edit Page</h3>
            </div>


<!--
            <form @submit.prevent="submit">
                <p class="text-xs text-indigo-500 dark:text-indigo-700  ">
                    <textarea v-model="form.title" rows="2" cols="50"></textarea>
                </p>
                <p class="text-xs text-indigo-500 dark:text-indigo-700">
                    <textarea v-model="form.icon"  rows="1" cols="50">></textarea>
                </p>  
                <p class="text-xs text-indigo-500 dark:text-indigo-700">
                    <textarea v-model="form.image"  rows="1" cols="50">></textarea>
                </p>  
                <p class="text-xs text-indigo-500 ç">
                    <textarea v-model="form.slogan"  rows="1" cols="50">></textarea>
                </p>  
                <p class="text-xs text-indigo-500 dark:text-indigo-700">
                    <textarea v-model="form.type"  rows="1" cols="50">></textarea>
                </p> 
                <p class="text-xs text-indigo-500 dark:text-indigo-700">
                    <textarea v-model="form.settings"  rows="1" cols="50">></textarea>
                </p> 
                          <p class="text-xs text-indigo-500 dark:text-indigo-700">
                    <textarea v-model="form.is_active"  rows="1" cols="50">></textarea>
                </p>   


                <label class="text-zinc-700"> page_id:</label>
                <input v-model="form.page_id" size="5" class="ml-2 text-sm text-indigo-500 dark:text-indigo-700">
                <the-button v-if="page.id>0" type="1" :busy="form.processing" preserveState="true" @clicked="submit" en="SAVE" nl="BEWAAR" :lng="language" />     
            </form>
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
-->

<!-- form -->
            <form @submit.prevent="submit" class=" divide-y divide-gray-300">
                <div class="divide-y divide-gray-300 -mt-2">
                </div>
                <div class="grid grid-cols-3 gap-y-6 gap-x-4 sm:grid-cols-6">

<!-- form.title -->
                    <div class="col-span-3 sm:col-span-3">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">Title</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.title"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>

<!-- form.file -->
                    <div class="col-span-3 sm:col-span-3">
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">File</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.file"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>


<!-- form.icon -->
                    <div class="sm:col-span-2">
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">Icon</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.icon" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>  
<!-- form.type -->
                    <div class="sm:col-span-2">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">Type</label>
                        <div class="mt-1">
                            <select v-model="form.type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option>content</option>
                                <option>public</option>
                                <option>grid</option>
                                <option>dashboard1</option>
                                <option>dashboard2</option>
                                <option>dashboard3</option>
                                <option>dashboard4</option>
                                <option>dashboard5</option>
                                <option>private</option>
                                <option>settings</option>
                                <option>custom</option>
                            </select>
                        </div>
                    </div>

<!-- form.image -->
                    <div class="sm:col-span-2">
                        <label for="slogan" class="block text-sm font-medium text-gray-700 dark:text-white mt-2">Image</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.image"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>



<!-- form.slogan -->
                    <div class="col-span-3 sm:col-span-6">
                        <label for="slogan" class="block text-sm font-medium text-gray-700 dark:text-white">Slogan</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.slogan"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>



<!-- form.Settings -->
                    <div class="col-span-3 sm:col-span-6">
                        <label for="settings" class="block text-sm font-medium text-gray-700 dark:text-white">Settings</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.settings"  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>
                    </div>

<!-- form.is_active -->
                    <div class="sm:col-span-1">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">isActive</label>
                      <Switch v-model="form.is_active" :class="[form.is_active ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">isActive</span>
                        <span aria-hidden="true" :class="[form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="sidebar" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">SideBar</label>
                      <Switch v-model="form.sidebar" :class="[form.sidebar  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">SideBar</span>
                        <span aria-hidden="true" :class="[form.sidebar ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="header" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">Header</label>
                      <Switch v-model="form.header" :class="[form.header  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">Header</span>
                        <span aria-hidden="true" :class="[form.header ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="footer" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">Footer</label>
                      <Switch v-model="form.footer" :class="[form.footer  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">Footer</span>
                        <span aria-hidden="true" :class="[form.footer ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="public" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">Public</label>
                      <Switch v-model="form.public" :class="[form.public  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">Public</span>
                        <span aria-hidden="true" :class="[form.public ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="animate" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">Animate</label>
                      <Switch v-model="form.animate" :class="[form.animate  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">Animate</span>
                        <span aria-hidden="true" :class="[form.animate ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="max_width" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">MaxWidth</label>
                      <Switch v-model="form.max_width" :class="[form.max_width  ? 'bg-green-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">maxWidth</span>
                        <span aria-hidden="true" :class="[form.max_width ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>
                    <div class="sm:col-span-3">

                    </div>

<!-- form.buttons -->
                <div class="col-span-3 sm:col-span-6 border-t-2 mb-6">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-6"></label>

                    <div class="flex justify-end ">
                        <button type="button" @click="$emit('close')" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</button>
                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                    </div>
                </div>

<!-- end form-->
            </div>
        </form>

<!-- end temlate -->
        </div>
    </div>
</template>



<!--
   
        <div class="px-4 pb-4 text-left">
            <h3 class="text-lg font-medium leading-6 text-gray-900 ">Profile</h3>
            <p class="mt-1 text-sm text-gray-500">This information will be displayed publicly so be careful what you share.</p>
        </div>





        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <div class="mt-1 flex rounded-md shadow-sm">
              <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">workcation.com/</span>
              <input type="text" name="username" id="username" autocomplete="username" class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-6">
            <label for="about" class="block text-sm font-medium text-gray-700">About</label>
            <div class="mt-1">
              <textarea id="about" name="about" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
            <p class="mt-2 text-sm text-gray-500">Write a few sentences about yourself.</p>
          </div>

          <div class="sm:col-span-6">
            <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
            <div class="mt-1 flex items-center">
              <span class="h-12 w-12 overflow-hidden rounded-full bg-gray-100">
                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </span>
              <button type="button" class="ml-5 rounded-md border border-gray-300 bg-white py-2 px-3 text-sm font-medium leading-4 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Change</button>
            </div>
          </div>

          <div class="sm:col-span-6">
            <label for="cover-photo" class="block text-sm font-medium text-gray-700">Cover photo</label>
            <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6">
              <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                  <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                    <span>Upload a file</span>
                    <input id="file-upload" name="file-upload" type="file" class="sr-only" />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-8">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
          <p class="mt-1 text-sm text-gray-500">Use a permanent address where you can receive mail.</p>
        </div>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="first-name" class="block text-sm font-medium text-gray-700">First name</label>
            <div class="mt-1">
              <input type="text" name="first-name" id="first-name" autocomplete="given-name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="last-name" class="block text-sm font-medium text-gray-700">Last name</label>
            <div class="mt-1">
              <input type="text" name="last-name" id="last-name" autocomplete="family-name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
            <div class="mt-1">
              <select id="country" name="country" autocomplete="country-name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option>United States</option>
                <option>Canada</option>
                <option>Mexico</option>
              </select>
            </div>
          </div>

          <div class="sm:col-span-6">
            <label for="street-address" class="block text-sm font-medium text-gray-700">Street address</label>
            <div class="mt-1">
              <input type="text" name="street-address" id="street-address" autocomplete="street-address" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
            <div class="mt-1">
              <input type="text" name="city" id="city" autocomplete="address-level2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="region" class="block text-sm font-medium text-gray-700">State / Province</label>
            <div class="mt-1">
              <input type="text" name="region" id="region" autocomplete="address-level1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="postal-code" class="block text-sm font-medium text-gray-700">ZIP / Postal code</label>
            <div class="mt-1">
              <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>
        </div>
      </div>

      <div class="pt-8">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
          <p class="mt-1 text-sm text-gray-500">We'll always let you know about important changes, but you pick what else you want to hear about.</p>
        </div>
        <div class="mt-6">
          <fieldset>
            <legend class="sr-only">By Email</legend>
            <div class="text-base font-medium text-gray-900" aria-hidden="true">By Email</div>
            <div class="mt-4 space-y-4">
              <div class="relative flex items-start">
                <div class="flex h-5 items-center">
                  <input id="comments" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                </div>
                <div class="ml-3 text-sm">
                  <label for="comments" class="font-medium text-gray-700">Comments</label>
                  <p class="text-gray-500">Get notified when someones posts a comment on a posting.</p>
                </div>
              </div>
              <div class="relative flex items-start">
                <div class="flex h-5 items-center">
                  <input id="candidates" name="candidates" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                </div>
                <div class="ml-3 text-sm">
                  <label for="candidates" class="font-medium text-gray-700">Candidates</label>
                  <p class="text-gray-500">Get notified when a candidate applies for a job.</p>
                </div>
              </div>
              <div class="relative flex items-start">
                <div class="flex h-5 items-center">
                  <input id="offers" name="offers" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                </div>
                <div class="ml-3 text-sm">
                  <label for="offers" class="font-medium text-gray-700">Offers</label>
                  <p class="text-gray-500">Get notified when a candidate accepts or rejects an offer.</p>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset class="mt-6">
            <legend class="contents text-base font-medium text-gray-900">Push Notifications</legend>
            <p class="text-sm text-gray-500">These are delivered via SMS to your mobile phone.</p>
            <div class="mt-4 space-y-4">
              <div class="flex items-center">
                <input id="push-everything" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label for="push-everything" class="ml-3 block text-sm font-medium text-gray-700">Everything</label>
              </div>
              <div class="flex items-center">
                <input id="push-email" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label for="push-email" class="ml-3 block text-sm font-medium text-gray-700">Same as email</label>
              </div>
              <div class="flex items-center">
                <input id="push-nothing" name="push-notifications" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label for="push-nothing" class="ml-3 block text-sm font-medium text-gray-700">No push notifications</label>
              </div>
            </div>
          </fieldset>
        </div>
-->

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
