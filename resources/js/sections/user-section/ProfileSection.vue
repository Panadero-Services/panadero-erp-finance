<script setup>
// vue
import { ref } from 'vue'
import { usePage, Link, useForm } from '@inertiajs/vue3';

// sub-sections
import ProgressSection from '@/sections/user-section/ProgressSection.vue';

import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';

const props = defineProps({
    db: Object,
    set: Object, 
    user: Object
});

const form = useForm({
    user: props.user,
    mode: props.set.mode
});

const _waitFor = (_o, _ms=1000) => new Promise(r => setTimeout(r, _ms));

const _updatePrepare = async (_o, _step=0) => {
  return new Promise(async function(resolve,reject){
	//console.log("prepare");
    try { 
		_o.start = new Date().getTime();
    	_o.loading = true; 
    	_o.stages[_step].result = "prepared"
    	_o.stages[_step].elapsed = new Date().getTime() - _o.start;
		await _waitFor(_o, 1500);
      resolve(_step);
    } catch (_err) {
      reject(_err);
    }  
  });
}

const _updateValidate1 = async (_o, _step=1) => {
	//console.log("validate1");
 	return new Promise(async function(resolve,reject){
    	try { 
   		_o.payload = form.user;
    		_o.stages[_step].result = "pre-validated"
    		_o.stages[_step].elapsed = new Date().getTime() - _o.start;
			await _waitFor(_o, 1100);
      	resolve(_step);
    	} catch (_err) {
      	reject(_err);
    	}  
  	});
}

const _updateRequest = async (_o, _step=2) => {
	//console.log("request");
  	return new Promise(async function(resolve,reject){
   	try {

   		const _model = "User";

    		_o.stages[_step].result = await props.db.setUser(_model, _o.payload);
    		_o.stages[_step].elapsed = new Date().getTime() - _o.start;

			await _waitFor(_o, 1600);
      	resolve(_step);
    	} catch (_err) {
      	reject(_err);
    	}  
  	});
}

const _updateValidate2 = async (_o, _step=3) => {
	//console.log("validate2");
  	return new Promise(async function(resolve,reject){
   	try { 
			await _waitFor(_o, 2100);
    		_o.stages[_step].result = "validated response"
    		_o.stages[_step].elapsed = new Date().getTime() - _o.start;
      	resolve(_step);
    	} catch (_err) {
      	reject(_err);
    	}  
  	});
}

const _updateComplete = async (_o, _step=4) => {
	//console.log("complete");
 	return new Promise(async function(resolve,reject){
   	try {
			await _waitFor(_o,3000);
    		_o.stages[_step].result = "completed"
    		_o.stages[_step].elapsed = new Date().getTime() - _o.start;
			_o.loading = false; 
      	resolve(_step);
    	} catch (_err) {
      	reject(_err);
    	}  
  	});
}

const _stages = [
	{ name:"prepare",   result:"", elapsed:0, max:100, f:_updatePrepare },
	{ name:"validate1", result:"", elapsed:0, max:100, f:_updateValidate1 },
	{ name:"request",   result:"", elapsed:0, max:100, f:_updateRequest },
	{ name:"validate2", result:"", elapsed:0, max:100, f:_updateValidate2 },
	{ name:"complete", result:"", elapsed:0, max:100, f:_updateComplete }
];

const _uStruct = { name:"userRequest", loading: false, step: 0, start: 0, end: 0, stages: _stages, payload: {} }
const _oUpdate = ref(_uStruct);

const _update = async () => {
 	return new Promise(async function(resolve,reject){
    	try {
			_oUpdate.value = _uStruct;

		   let _O = _oUpdate.value;
		   let _S =  _O.stages;

			_O.step = await _S[0].f(_O);
			_O.step = await _S[1].f(_O);
			_O.step = await _S[2].f(_O);
			_O.step = await _S[3].f(_O);
			_O.step = await _S[4].f(_O);

      	resolve();
    	} catch (_err) {
      	reject(_err);
    	}  
 	});
}

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

</script>
<template>

	<!-- Profile -->
	<div class="">

		<!-- Title User -->
		<div class=" sm:flex sm:items-end">
			<div class="sm:flex-1">
				<div class="text-center h-10">
					<h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-300 sm:text-3xl">{{form.user.name}} </h3>
				</div>
			</div>
		</div>
		<div class="mt-1">
				Profile Information
		</div>
		<!-- Name -->
		<div class="flex col-span-6 sm:col-span-4 mt-1">
			<InputLabel for="name" value="Name" class="mt-2 mr-2"/>
			<TextInput id="name" v-model="form.user.name" type="text" class="mt-1 block w-full" required />
			<InputError :message="form.errors.name" class="mt-2" />
		</div>

		<!-- Email -->
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

			<div class="mt-1 col-span-5 -mb-4">
				<progress-section :package="_oUpdate" />
			</div>

			<div></div>
			<div></div>
			<div></div>
			<div>
				<button type="update" :disabled="_oUpdate.loading" @click="_update" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Save</button>
			</div>
			<div>
				<button type="cancel" :disabled="_oUpdate.loading" @click="_cancel" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Cancel</button>
			</div>

		</div>
	</div>

</template>
<style>
	

</style>