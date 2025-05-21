<script setup>
import { useForm } from "@inertiajs/vue3";

import TheButton from "@/components/buttons/TheButton.vue";
import { Switch } from '@headlessui/vue'

const props = defineProps({
    contract: Object,
    line: Object,
    maintenanceMode: Boolean
});

const form = useForm({
    id: props.line.id,
    web3record_id: props.line.web3record_id,
    line: props.line.line,
    line_nr: props.line.line_nr,
    parameters: props.line.parameters,
    abi: props.line.abi,
    value: props.line.value,
    int_value: props.line.int_value,
    columns: props.line.columns,
    type: props.line.type,
    style: props.line.style,
    detail: props.line.detail,
    updated_at: props.line.updated_at,
    created_at: props.line.created_at,
    is_active: props.line.is_active,
    is_live: props.line.is_live,
    chainNr: props.line.chainNr,
    address: props.line.address,
    contractCounter: props.line.contractCounter,
    contractAbi: props.line.contractAbi,
    update_parameters: false,
    allow_delete: false,
    allow_insert: false,
    extended_form: false
});

const emit = defineEmits(['close'])

const submit = async () => {

 // validate inputs
    //if (form.update_parameters && form.parameters.length<1) form.update_parameters = false;

    await form.put(route("web3RecordLines.update", props.line.id));

    props.line.web3record_id = form.web3record_id;
    props.line.line_nr = form.line_nr;
    props.line.int_value = form.int_value;
    props.line.parameters = form.parameters;
    props.line.abi = form.abi;
    props.line.value = form.value;
    props.line.columns = form.columns;
    props.line.type = form.type;
    props.line.style = form.style;
    props.line.detail = form.detail;
    props.line.is_active = form.is_active;
    props.line.is_live = form.is_live;

    emit('close');
};


const submitNew = async () => {
    form.line_nr = props.contract.nextNr++;
    await form.post(route("web3RecordLines.store"));
    emit('close');
};

const submitDelete = async () => {
    await form.put(route("web3RecordLines.destroy", props.line.id));
    emit('close');
};



const hide = () => {
    console.log('click registered');
}

</script>

<!-- template-->
<template>
    <div>
        <div class="h-full w-full fixed top-0 left-0 z-20  bg-black opacity-50" v-on:click="hide"></div>

        <div class="px-8 bg-gray-50 rounded-lg text-lg pt-8 shadow-lg w-2/3 max-w-4xl fixed top-1/2 left-1/2 h-3/8 transform -translate-x-1/2 -translate-y-1/2 z-30 opacity-95  ">
            <div class="pb-4 text-left">
                <h3 class="text-xl mb-2 font-semibold">Edit ContractParameter{{id}}<span v-if="maintenanceMode" class="text-indigo-500 text-xs ml-8">MAINTENANCE MODE</span></h3>
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
                <div class="grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-6 text-gray-700 mb-2">

<!-- form.line -->
                    <div class="sm:col-span-3 mt-2">
                        <label for="line" class="block text-sm font-medium">line</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.line" class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.web3record_id -->
                    <div v-if="form.extended_form" class="sm:col-span-1 mt-2">
                        <label for="web3record_id" class="block text-sm font-medium">web3record_id</label>
                        <div class="mt-1">
                            <input  :disabled="!maintenanceMode"  type="text" v-model="form.web3record_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="maintenanceMode ? 'text-indigo-500' : 'bg-gray-100'" />
                        </div>
                    </div>

<!-- form.line_nr -->
                    <div v-if="form.extended_form" class="sm:col-span-1 mt-2">
                        <label for="line_nr" class="block text-sm font-medium">line_nr</label>
                        <div class="mt-1">
                            <input :disabled="!maintenanceMode" type="text" v-model="form.line_nr" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="maintenanceMode ? 'text-indigo-500' : 'bg-gray-100'" />
                        </div>
                    </div>

<!-- form.int_value -->
                    <div v-if="form.extended_form" class="sm:col-span-1 mt-2">
                        <label for="int_value" class="block text-sm font-medium">int_value</label>
                        <div class="mt-1">
                            <input :disabled="!maintenanceMode" type="text" v-model="form.int_value" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="maintenanceMode ? 'text-indigo-500' : 'bg-gray-100'" />
                        </div>
                    </div>

<!-- form.parameters -->
                    <div class="sm:col-span-3">
                        <label for="parameters" class=" text-sm font-medium text-gray-700">parameters</label>
                        <div class="mt-1">
                            <input :disabled="!form.update_parameters" type="text" v-model="form.parameters" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="form.update_parameters ? 'text-indigo-500' : 'bg-gray-100'" />
                        </div>
                    </div>

<!-- form.abi -->
                    <div v-if="form.extended_form" class="sm:col-span-3">
                        <label for="abi" class=" text-sm font-medium text-gray-700">abi</label>
                        <div class="mt-1">
                            <input :disabled="!maintenanceMode" type="text" v-model="form.abi" class=" w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :class="maintenanceMode ? 'text-indigo-500' : 'bg-gray-100'" />
                        </div>
                    </div>

<!-- form.value -->
                    <div class="sm:col-span-2">
                        <label for="value" class="block text-sm font-medium text-gray-700">value</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.value" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-indigo-500" />
                        </div>
                    </div>

<!-- form.columns -->
                    <div class="sm:col-span-2">
                        <label for="columns" class="block text-sm font-medium text-gray-700">columns</label>
                        <div class="mt-1">
                            <input type="text" v-model.trim="form.columns" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-indigo-500" />
                        </div>
                    </div>
<!-- form.type -->
                    <div class="sm:col-span-2">
                        <label for="country" class="block text-sm font-medium text-gray-700">Type</label>
                        <div class="mt-1">
                            <select v-model="form.type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-indigo-500">
                                <option>int</option>
                                <option>percentage</option>
                                <option>bigInt</option>
                                <option>bigInt_3n</option>
                                <option>bigInt_6n</option>
                                <option>bigInt_9n</option>
                                <option>bigInt_12n</option>
                                <option>bigInt_15n</option>
                                <option>text</option>
                                <option>address</option>
                                <option>array</option>
                            </select>
                        </div>
                    </div>

<!-- form.style -->
                    <div class="sm:col-span-2">
                        <label for="style" class=" text-sm font-medium text-gray-700">style</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.style" class=" w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-indigo-500" />
                        </div>
                    </div>


<!-- form.created_at -->
                    <div  v-if="form.extended_form" class="sm:col-span-2">
                        <label for="created_at" class=" text-sm font-medium text-gray-700">created_at</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.created_at" class="w-full rounded-md border-gray-300 shadow-sm  bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.updated_at -->
                    <div  v-if="form.extended_form" class="sm:col-span-2">
                        <label for="updated_at" class=" text-sm font-medium text-gray-700">updated_at</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.updated_at" class="w-full rounded-md border-gray-300 shadow-sm  bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.address -->
                    <div class="sm:col-span-4">
                        <label for="address" class=" text-sm font-medium text-gray-700">address</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.address" class="w-full rounded-md border-gray-300 shadow-sm  bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.chainNr -->
                    <div  v-if="form.extended_form" class="sm:col-span-1">
                        <label for="chainNr" class=" text-sm font-medium text-gray-700">chainNr</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.chainNr" class="w-full rounded-md border-gray-300 shadow-sm  bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.contractCounter -->
                    <div  v-if="form.extended_form" class="sm:col-span-1">
                        <label for="contractCounter" class=" text-sm font-medium text-gray-700">contractCounter</label>
                        <div class="mt-1">
                            <input :disabled="true" type="text" v-model="form.contractCounter" class="w-full rounded-md border-gray-300 shadow-sm  bg-gray-100 sm:text-sm" />
                        </div>
                    </div>

<!-- form.detail -->
                    <div v-if="form.extended_form" class="sm:col-span-6">
                        <label for="detail" class=" text-sm font-medium text-gray-700">detail</label>
                        <div class="mt-1">
                            <input type="text" v-model="form.detail" class=" w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-indigo-500" />
                        </div>
                    </div>

<!-- form.contractAbi -->
                    <div v-if="form.extended_form" class="sm:col-span-6">
                        <label for="contractAbi" class=" text-sm font-medium text-gray-700">contractAbi</label>
                        <div class="mt-1">
                            <textarea :disabled="true"  v-model="form.contractAbi"  rows="3" cols="50" class=" w-full rounded-md border-gray-300 shadow-sm bg-gray-100 sm:text-sm">></textarea>
                        </div>
                    </div>

<!-- form.is_active -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">isActive</label>
                      <Switch v-model="form.is_active" :class="[form.is_active  ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">isActive</span>
                        <span aria-hidden="true" :class="[form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

<!-- form.is_live -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="is_live" class="block text-sm font-medium text-gray-700 mb-2">isLive</label>
                      <Switch v-model="form.is_live" :class="[form.is_live  ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">isLive</span>
                        <span aria-hidden="true" :class="[form.is_live ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

<!-- form.update_parameters -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="update_parameters" class="block text-sm font-medium text-gray-700 mb-2">updateParameters</label>
                      <Switch v-model="form.update_parameters" :class="[form.update_parameters  ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">updateParameters</span>
                        <span aria-hidden="true" :class="[form.update_parameters ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

<!-- form.allow_delete -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="allow_delete" class="block text-sm font-medium text-gray-700 mb-2">allowDelete</label>
                      <Switch v-model="form.allow_delete" :class="[form.allow_delete ? 'bg-red-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2']">
                        <span class="sr-only">allowDelete</span>
                        <span aria-hidden="true" :class="[form.allow_delete ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>


<!-- form.allow_insert -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="allow_insert" class="block text-sm font-medium text-gray-700 mb-2">allow_insert</label>
                      <Switch v-model="form.allow_insert" :class="[form.allow_insert ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">allow_insert</span>
                        <span aria-hidden="true" :class="[form.allow_insert ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

<!-- form.extended_form -->
                    <div class="sm:col-span-1 mt-2">
                        <label for="extended_form" class="block text-sm font-medium text-gray-700 mb-2">extendedForm</label>
                      <Switch v-model="form.extended_form" :class="[form.extended_form ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                        <span class="sr-only">extendedForm</span>
                        <span aria-hidden="true" :class="[form.extended_form ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                      </Switch>
                    </div>

<!-- form.buttons -->
                <div class="sm:col-span-6 border-t-2 mb-3 ">
                    <div class="flex justify-end mt-2">
                        <button type="button" @click="$emit('close')" class="rounded-md bg-white px-3 py-2 xl:px-3.5 xl:py-2.5 text-xs xl:text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-400 hover:bg-indigo-50 ml-2">Cancel</button>
                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                        <button type="button" :disabled="!form.allow_insert"  @click="submitNew" :class="form.allow_insert ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300'"  class="ml-3 inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium text-white shadow-sm  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save New</button>
                        <button type="submitDelete" :disabled="!form.allow_delete" :class="form.allow_delete ? 'bg-red-600 hover:bg-indigo-700' : 'bg-gray-300'" class="ml-3 inline-flex justify-center rounded-md border border-transparent  py-2 px-4 text-sm font-medium text-white shadow-sm  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Delete</button>
                    </div>
                </div>

<!-- end form-->
            </div>
        </form>
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
