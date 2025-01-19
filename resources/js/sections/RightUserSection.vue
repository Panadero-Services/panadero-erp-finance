<script setup>
import { ref,  onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3';

import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'


import { XMarkIcon } from '@heroicons/vue/24/outline'
import { LinkIcon, PlusIcon, QuestionMarkCircleIcon, ClipboardDocumentCheckIcon, EllipsisVerticalIcon} from '@heroicons/vue/20/solid'
import { UserIcon } from '@heroicons/vue/24/outline'


import { useProjectStore } from '@/stores/ProjectStore';
const _project = useProjectStore();

const props = defineProps({
    set: Object
});


const _actualProject = ref();
const _userId = ref(0);

const activeUser = usePage().props.auth.user;

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
        props.set.project.environment = "default";
        props.set.project.validEnvironments = ["default","test"];
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

// css 
const _userColor="green";

const _shadowColor = 'shadow-'+_userColor+'-700 dark:shadow-'+_userColor+'-200';
const _bgColor = 'bg-'+_userColor+'-700';
const _bgTitle = 'bg-'+_userColor+'-800';
const _hoverColor = 'hover:bg-'+_userColor+'-500';



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
                <div class="flex h-full flex-col overflow-y-scroll bg-slate-100 shadow-2xl " :class="_shadowColor">
                  <div class="px-4 py-2 sm:px-6" :class="_bgTitle">
                    <div class="flex items-start justify-between ">
                        <div class="flex"><user-icon class="w-10 text-indigo-200" /><div class="text-2xl text-indigo-200 m-1">Profile</div></div>
                      <div class="ml-3 flex h-7 items-center">
     
                      </div>
                    </div>
                  </div>
                  <!-- Main -->
                  <div>
                    <div class="pb-1 sm:pb-6">
                      <div>
                        <div class="relative h-96" :class="_bgColor">
                          <img class="absolute size-full object-cover brightness-75 opacity-50" src="/storage/profile-photos/lieuwe.jpg" alt="" />
                        </div>
                        <div class="mt-6 px-4 sm:mt-8 sm:flex sm:items-end sm:px-6">
                          <div class="sm:flex-1">
                            <div>
                              <div class="flex items-center">
                                <h3 class="text-xl font-bold text-gray-900 sm:text-2xl">{{activeUser.name}}</h3>
                                <h3 class="text-xl font-bold text-gray-900 sm:text-2xl"> </h3>
                                <span class="ml-2.5 inline-block size-2 shrink-0 rounded-full bg-green-400">
                                  <span class="sr-only">Online</span>
                                </span>
                              </div>
                              <p class="text-sm text-gray-500">jawsome.orbit@gmail.com</p>
                            </div>
                            <div class="mt-5 flex flex-wrap space-y-3 sm:space-x-3 sm:space-y-0">
                              <button type="button" class="inline-flex w-full shrink-0 items-center justify-center rounded-md  px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:flex-1" :class="[_bgColor,_hoverColor]">Message</button>
                              <button type="button" class="inline-flex w-full flex-1 items-center justify-center rounded-md bg-slate-50 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-white">Call</button>
                              <div class="ml-3 inline-flex sm:ml-0">
                                <Menu as="div" class="relative inline-block text-left">
                                  <MenuButton class="relative inline-flex items-center rounded-md bg-slate-50 p-2 text-gray-400 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-white">
                                    <span class="absolute -inset-1" />
                                    <span class="sr-only">Open options menu</span>
                                    <EllipsisVerticalIcon class="size-5" aria-hidden="true" />
                                  </MenuButton>
                                  <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                                      <div class="py-1">
                                        <MenuItem v-slot="{ active }">
                                          <a href="#" :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700', 'block px-4 py-2 text-sm']">View profile</a>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                          <a href="#" :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700', 'block px-4 py-2 text-sm']">Copy profile link</a>
                                        </MenuItem>
                                      </div>
                                    </MenuItems>
                                  </transition>
                                </Menu>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="px-4 pb-5 pt-5 sm:px-0 sm:pt-0">
                      <dl class="space-y-8 px-4 sm:space-y-6 sm:px-6">
                        <div>
                          <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:shrink-0">Bio</dt>
                          <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <p>Enim feugiat ut ipsum, neque ut. Tristique mi id elementum praesent. Gravida in tempus feugiat netus enim aliquet a, quam scelerisque. Dictumst in convallis nec in bibendum aenean arcu.</p>
                          </dd>
                        </div>
                        <div>
                          <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:shrink-0">Location</dt>
                          <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">New York, NY, USA</dd>
                        </div>
                        <div>
                          <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:shrink-0">Website</dt>
                          <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">ashleyporter.com</dd>
                        </div>
                        <div>
                          <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:shrink-0">Birthday</dt>
                          <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                            <time datetime="1988-06-23">June 23, 1988</time>
                          </dd>
                        </div>
                      </dl>
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>