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

const _updateStruct = { 
   name:"userRequest",
   loading: false,
   payload: {},
   step: 0,
   start: 0,
   end: 0,
   prepare: "",
   validate1: "",
   request: "",
   response: "", 
   validate2: "",
   finished: ""
}

const _oUpdate=ref(_updateStruct);

//const updateProfileInformation = () => {
const _update = async () => {

   _oUpdate.value = _updateStruct; 

   // 1 prepare
   _oUpdate.value.step=0;
   _oUpdate.value.loading = true;
   _oUpdate.value.start = new Date().getTime();
   _oUpdate.value.prepare = "prepared";

   //_shadowColor.value = "yellow";

   // 2 validate
   _oUpdate.value.step = 1;
   const _model = "User";
   props.set.mode = form.mode;
   form.user.json = JSON.stringify(props.set);
   _oUpdate.value.payload = form.user;
   _oUpdate.value.validate1 = "pre-validated";
   console.log(form.user);

   // 3 request
   _oUpdate.value.step = 2;
   let _response = await props.db.setUser(_model, _oUpdate.value.payload);
   _oUpdate.value.request = "requested";

   // 4 response
   _oUpdate.value.step = 3;
   _oUpdate.value.request = "responded";

   // 5 validate
   _oUpdate.value.step = 4;
   _oUpdate.value.end = new Date().getTime() - _oUpdate.value.start;
   _oUpdate.value.validate2 = "post-validated";

   // 6 finished
   _oUpdate.value.step = 5;
   _oUpdate.value.loading = false;
   _oUpdate.value.finished = "finished";

   //_shadowColor.value ="indigo";
}

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

</script>
<template>

	<div class=""></div>


	<!-- Profile -->
	<div class="pt-1">

		<!-- Title User -->
		<div class="my-1 sm:flex sm:items-end">
			<div class="sm:flex-1">
				<div class="text-center h-10">
					<h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-300 sm:text-3xl">{{form.user.name}} </h3>
				</div>
			</div>
		</div>

		Profile Information

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
			<div></div>
			<div></div>
			<div></div>
			<div>
				<button type="update" :disabled="_oUpdate.loading" @click="_update" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Save</button>
			</div>
			<div>
				<button type="cancel" :disabled="_oUpdate.loading" @click="_cancel" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-800 text-white dark:text-slate-300 dark:hover:bg-indigo-900" :class="_button">Cancel</button>
			</div>
			<div class="mt-4 col-span-4">
				<progress-section :package="_oUpdate" />
			</div>
		</div>
	</div>

</template>
<style>
	

</style>