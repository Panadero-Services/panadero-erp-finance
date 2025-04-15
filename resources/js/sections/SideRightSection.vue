<script setup>
import { ref,  onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { LinkIcon, PlusIcon, QuestionMarkCircleIcon, ClipboardDocumentCheckIcon} from '@heroicons/vue/20/solid'

import { useProjectStore } from '@/stores/ProjectStore';
const _project = useProjectStore();

const props = defineProps({
    set: Object
});


const _actualProject = ref();
const _userId = ref(0);


// lifeCycle
onMounted(async ()=> {
  _userId.value = usePage().props.auth.user.id;
  _actualProject.value = await _project.getProjectFromDb(1);
  console.log( _actualProject.value)
  // board.selectCard({ id: 3, groupMode: true });
});


const team = [
  {
    name: 'Tom Cook',
    email: 'tom.cook@example.com',
    href: '#',
    imageUrl:
    '/storage/profile-photos/lieuwe.jpg'
  },
  {
    name: 'Alf Francis',
    email: 'whitney.francis@example.com',
    href: '#',
    imageUrl:
    '/storage/profile-photos/alf.png'
  },
  {
    name: 'Rico Krasner',
    email: 'leonard.krasner@example.com',
    href: '#',
    imageUrl:
    '/storage/profile-photos/rico.png'
  },
  {
    name: 'Eric Floyd',
    email: 'floyd.miles@example.com',
    href: '#',
    imageUrl:'/storage/profile-photos/eric.png'
  },

  {
    name: 'Sadaf Selman',
    email: 'emily.selman@example.com',
    href: '#',
    imageUrl:
    '/storage/profile-photos/sadaf.png'
  },
]

const open = ref(false)


defineExpose({
 open
});




const setProjectId = (_title) => {
    if(!( usePage().props.auth.user == null)) {
        // toDo !! retrieve from db
        props.set.project.id = _title=='none' ? 0 : 1;
        props.set.project.title = _title;
        props.set.project.environment = "sandbox";
        props.set.project.validEnvironments = ["master", "alternative", "sandbox"];
        props.set.project.category = "primera";
    }
}

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

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10" @close="open = false">
      <div class="fixed inset-0" />
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-md pl-10 sm:pl-16">

            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-md">
                <form class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-2xl shadow-blue-700 dark:shadow-blue-200">
                  <div class="h-0 flex-1 overflow-y-auto">
                    <div class="bg-blue-700 pl-2 py-2 sm:pl-3">
                        <div class="flex"><clipboard-document-check-icon  class="w-12 text-blue-200" /><div class="text-2xl text-green-200 m-3">Project</div></div>

<div>
userId: {{_userId}}
</div>

                            <div class="text-center font-semibold text-white text-lg ">
                             {{set.project.title}}.{{set.project.environment}}.{{set.project.category}}
                            </div>
                        </div>

                    <div class="flex flex-1 flex-col justify-between">
                      <div class="divide-y divide-gray-200 px-4 sm:px-6">
                        <div class="space-y-6 pb-5 pt-6">
                          <div>

                            <label for="project-name" class="block text-sm/6 font-medium text-gray-900">Project</label>
                            <div class="m-1">
                              <button @click="setProjectId(title)" v-for="title in set.project.validTitles" type="button" class="mx-0.5" :class="[_button, _hover, title==set.project.title ? _bgSelected : _bg]">{{title}}</button> 
                            </div>
                            
                            <label for="project-name" class="block text-sm/6 font-medium text-gray-900 mt-4">Environment</label>
                            <div class="m-1">
                              <span v-if="set.project.id">
                                  <button v-for="env in set.project.validEnvironments" class="mx-0.5" @click="set.project.environment=env" type="button" :class="[_button, _hover, env==set.project.environment ? _bgSelected : _bg]">{{env}}</button>
                              </span>
                            </div>

                            <label for="project-name" class="block text-sm/6 font-medium text-gray-900 mt-4">Slot</label>
                            <div class="m-1">
                               <span v-if="set.project.id">
                                    <button v-for="(b, idx) in _buttons"  @click="_changeCat(b)" type="button" class="mx-0.5" :class="[_button, _hover, b==set.project.category ? _bgSelected : _bg]">slot{{idx+1}}</button>
                                </span>
                            </div>

                          </div>


                          <div>
                            <label for="project-description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                            <div class="mt-2">
                              <textarea v-model="_actualProject.description" rows="3" name="project-description" id="project-description" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                          </div>
                          <div>
                            <h3 class="text-sm/6 font-medium text-gray-900">SELF Members</h3>
                            <div class="mt-2">
                              <div class="flex space-x-2">
                                <a v-for="person in team" :key="person.email" :href="person.href" class="relative rounded-full hover:opacity-75">
                                  <img class="inline-block size-8 rounded-full" :src="person.imageUrl" :alt="person.name" />
                                </a>
                                <button type="button" class="relative inline-flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-dashed border-gray-200 bg-white text-gray-400 hover:border-gray-300 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                  <span class="absolute -inset-2" />
                                  <span class="sr-only">Add team member</span>
                                  <PlusIcon class="size-5" aria-hidden="true" />
                                </button>
                              </div>
                            </div>
                          </div>
                          <fieldset>
                            <legend class="text-sm/6 font-medium text-gray-900">Privacy</legend>
                            <div class="mt-2 space-y-4">
                              <div class="relative flex items-start">
                                <div class="absolute flex h-6 items-center">
                                  <input id="privacy-public" name="privacy" value="public" aria-describedby="privacy-public-description" type="radio" checked="" class="relative size-4 appearance-none rounded-full border border-gray-300 before:absolute before:inset-1 before:rounded-full before:bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden [&:not(:checked)]:before:hidden" />
                                </div>
                                <div class="pl-7 text-sm/6">
                                  <label for="privacy-public" class="font-medium text-gray-900">Public access</label>
                                  <p id="privacy-public-description" class="text-gray-500">Everyone with the link will see this project.</p>
                                </div>
                              </div>
                              <div>
                                <div class="relative flex items-start">
                                  <div class="absolute flex h-6 items-center">
                                    <input id="privacy-private-to-project" name="privacy" value="private-to-project" aria-describedby="privacy-private-to-project-description" type="radio" class="relative size-4 appearance-none rounded-full border border-gray-300 before:absolute before:inset-1 before:rounded-full before:bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden [&:not(:checked)]:before:hidden" />
                                  </div>
                                  <div class="pl-7 text-sm/6">
                                    <label for="privacy-private-to-project" class="font-medium text-gray-900">Private to SELF members</label>
                                    <p id="privacy-private-to-project-description" class="text-gray-500">Only selected SELF id's are able to access.</p>
                                  </div>
                                </div>
                              </div>
                              <div>
                                <div class="relative flex items-start">
                                  <div class="absolute flex h-6 items-center">
                                    <input id="privacy-private" name="privacy" value="private" aria-describedby="privacy-private-to-project-description" type="radio" class="relative size-4 appearance-none rounded-full border border-gray-300 before:absolute before:inset-1 before:rounded-full before:bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden [&:not(:checked)]:before:hidden" />
                                  </div>
                                  <div class="pl-7 text-sm/6">
                                    <label for="privacy-private" class="font-medium text-gray-900">Private to you</label>
                                    <p id="privacy-private-description" class="text-gray-500">You are the only one able to access this project.</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </div>
                        <div class="pb-6 pt-4">
                          <div class="flex text-sm">
                            <a href="#" class="group inline-flex items-center font-medium text-indigo-600 hover:text-indigo-900">
                              <LinkIcon class="size-5 text-indigo-500 group-hover:text-indigo-900" aria-hidden="true" />
                              <span class="ml-2">Copy link</span>
                            </a>
                          </div>
                          <div class="mt-4 flex text-sm">
                            <a href="#" class="group inline-flex items-center text-gray-500 hover:text-gray-900">
                              <QuestionMarkCircleIcon class="size-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                              <span class="ml-2">Learn more about sharing</span>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex shrink-0 justify-end px-4 py-4">
                    <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" @click="open = false">Cancel</button>
                    <button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>