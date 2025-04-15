<script setup>

const props = defineProps({
    form: Object 
});

const _button = "rounded-md border border-indigo-400 py-1 px-3 mr-1 text-sm font-medium shadow-sm hover:bg-indigo-700 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2 disabled:opacity-25";

const _successColor = 'bg-green-600';

</script>
<template>

<!-- User Mode Buttons -->
<!-- SubCategory Other Information -->
<div  class="pt-3 pb-3">
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
			{{form.user.current_team.name}}
		</div>
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
		<div v-if="form.user.profile_photo_path" class="col-span-2 text-slate-800 dark:text-slate-300 text-right"> {{form.user.profile_photo_path.substr(0,36)}} ....</div>
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


</template>
<style>
	

</style>