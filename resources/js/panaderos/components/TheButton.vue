<script setup>
	defineProps({
		busy: Boolean,
		preserveState: Boolean,
		en: String,
		nl: String,
		lng: String,
		type: Number,
	});
	
	let _types = [
		"bg-white hover:bg-indigo-500 hover:ring-indigo-500 text-gray-800 p-1 xl:p-1 hover:text-white border border-gray-300",
		"bg-gray-700 hover:bg-green-600 hover:ring-green-700 text-green-500 p-1 xl:p-1 hover:text-white border border-gray-300",
		"bg-white hover:bg-indigo-500 hover:ring-indigo-500 text-green-600 p-1 xl:p-1 hover:text-white border border-gray-300",
		"bg-white hover:bg-indigo-500 hover:ring-indigo-500 text-red-600 p-1 xl:p-1 hover:text-white border border-gray-300",
		"bg-indigo-600 hover:bg-green-600 hover:ring-gray-500 text-white mb-3 text-sm p-2 hover:text-white border border-gray-700 px-2"
	];
</script>

<template>
	<button v-if="type<6" 
		class= "group max-w-xs mx-auto rounded-lg  my-1 ml-4 ring-1 ring-slate-900/5 shadow-lg space-y-1 text-xs font-semibold "
		:class="[_types[type-1],{'opacity-50':busy}]"
		@click="$emit('clicked')"
		:disabled="busy"
	 	:preserveState= "preserveState">
			{{lng == 'EN' ? en : nl}}
	</button>
</template>