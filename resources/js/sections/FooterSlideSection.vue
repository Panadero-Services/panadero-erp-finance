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
  //console.log( _actualProject.value)
  // board.selectCard({ id: 3, groupMode: true });
});



import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'

const stats = [
  { name: 'Node Requests', stat: '71,897', previousStat: '70,946', change: '12%', changeType: 'increase' },
  { name: 'Hub Performance', stat: '58.16%', previousStat: '56.14%', change: '2.02%', changeType: 'increase' },
  { name: 'Response', stat: '24.57%', previousStat: '28.62%', change: '4.05%', changeType: 'decrease' },
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

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10 " @close="open = false">
      <div class="fixed inset-0" />
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-md pl-10 sm:pl-16">

            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-md">
                <form class="flex h-full flex-col divide-y divide-gray-200 bg-slate-200 shadow-2xl shadow-blue-700 dark:shadow-blue-200">
                  <div class="h-0 flex-1 overflow-y-auto">
                    <div class="bg-blue-700 pl-2 py-2 sm:pl-3">
                        <div class="flex"><clipboard-document-check-icon  class="w-12 text-blue-200" /><div class="text-2xl text-green-200 m-3">Developers</div></div>



                            <div class="text-center font-semibold text-white text-lg ">
                             sections/FooterSlideSection.vue
                            </div>
                        </div>

                    <div class="flex flex-1 flex-col justify-between">
                      <div class="divide-y divide-gray-200 px-4 sm:px-6">
                        <div class="space-y-1 pb-5 pt-4">
  
                          <div>
                            <label for="project-description" class="block text-sm/6 font-medium text-gray-900">Settings</label>
                          </div>



                          <div class="p-2">
                            <h3 class="text-xl font-semibold text-black border border-gray-600 rounded-lg p-2 text-center hover:bg-orange-300">Progress InfoDatek project</h3>
                            <dl class="mt-1 grid grid-cols-1 divide-y divide-gray-200 overflow-hidden border border-gray-600 rounded-lg xl:grid-cols-1 md:divide-x md:divide-y-0">
                              <div v-for="item in stats" :key="item.name" class="px-2 py-1 sm:p-2">
                                <dt class="text-xxs font-normal text-gray-900">{{ item.name }}</dt>
                                <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                  <div class="flex items-baseline text-xl font-semibold text-indigo-600">
                                    {{ item.stat }}
                                    <span class="ml-2 text-xxs font-medium text-gray-500">from {{ item.previousStat }}</span>
                                  </div>

                                  <div :class="[item.changeType === 'increase' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'inline-flex items-baseline rounded-full px-2.5 py-0.5 text-sm font-medium md:mt-2 lg:mt-0']">
                                    <ArrowUpIcon v-if="item.changeType === 'increase'" class="-ml-1 mr-0.5 size-5 shrink-0 self-center text-green-500" aria-hidden="true" />
                                    <ArrowDownIcon v-else class="-ml-1 mr-0.5 size-5 shrink-0 self-center text-red-500" aria-hidden="true" />
                                    <span class="sr-only"> {{ item.changeType === 'increase' ? 'Increased' : 'Decreased' }} by </span>
                                    {{ item.change }}
                                  </div>
                                </dd>
                              </div>
                            </dl>
                          </div>


                          <fieldset>
                            <legend class="text-sm/6 font-medium text-gray-900">Details</legend>
                            <div class="mt-2 space-y-4">
          

settings: {{set}}

                            </div>
                          </fieldset>
                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="flex shrink-0 justify-end px-4 py-4">
                    <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-orange-500" @click="open = false">Cancel</button>
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