<script setup>

	import {computed, onMounted, ref } from 'vue'
	import Panel from "@/customComponents/Panel.vue";
	import Child from "@/customComponents/Child.vue";

	import AddWeb3Record from "@/customComponents/AddWeb3Record.vue";
	import AssignmentList from "@/customComponents/AssignmentList.vue";

  	import Web3Record from "@/web3/Web3Record.vue";

	import { useForm } from "@inertiajs/inertia-vue3";

  	import Web3Card from "@/web3/Web3Card.vue";


	let form = useForm({
	    title: 'title',
	    content: 'content',
	    user_id: 3,
	    tag_id: 3,
	});

	const web3Record_store = async () => {
	  	await form.post(route("web3Records.store"));
	  	console.log('created: !!')
	};

	const web3Record_destroy = (id) => {
        form.delete(route('web3Records.destroy', id));
	};

	const props = defineProps({
		records: Object,
		tags: Object,
		tag_id: Number,
		tag_name: String
	});
/*
	let assignments= ref([
		{name: 'system author' , title: 'Finish project Inertia', content: 'Lets Put23 here all details. With digital architecture and is that an algorithm' ,complete: false, id: 1, tag: 'math'},
		{name: 'system author' , title: 'Read chapter 2', content: 'This trendy technology allows users to engage with web applications speedily and responsively because everything is client-side-rendered. However, it can be a pain for developers who build server-side rendered applications with frameworks like Panaderos combo Inertia,  Laravel, Vue.js, and Tailwind CSS', complete: false, id: 2, tag: 'science'},
		{name: 'system author' , title: 'Turn in Homework', content: 'Multi-page applications (MPAs) are getting less popular by the day. Famous platforms such as Facebook, Twitter, YouTube, Github, and many others are already using single-page application (SPA) technology instead.', complete: false, id: 3, tag: 'math'},
		{name: 'system author' , title: 'i5v0/inertia-staging 0.10', content: 'Course Laravel, Inertia, Vue, , Laracast Learn Vue 3: Step by Step, SECTIONS Getting Started, Vue Components, Event Handling, Beyond the Basics - - - create user login system - - - php artisan tinker - - -> User::factory()->count(10)->create()', complete: false, id: 3, tag: 'version'},
		{name: 'system author' , title: 'i5v0/inertia-staging 0.20', content: 'Course Laravel, Inertia, Vue, https://www.youtube.com/watch?v=swelKdHlvco, Laravel Daily, Links & Routing, - - - create Custom Components, Flash Messages, User data from auth, tags table - - - create NavLink / SPA loading versus MPA loading', complete: false, id: 3, tag: 'version'},
		{name: 'system author' , title: 'composition api ', content: 'Seems we are mixing up two syntaxes here - the newer, Composition API (Vue3, setup script) and Options API (Vue2 style, one with data and mounted functions). While its still ok to use the Options API syntax, you should only use one of the two. I suggest reading up on the different syntaxes:', complete: false, id: 3, tag: 'couse'},
	])
*/
//	let assignments = ref(props.tasks);

//	const filters = computed(() => {
//	    return {
			//inProgress: assignments.filter(assignment => ! assignment.complete),
			//completed: assignments.filter(assignment => assignment.complete)
//	    }
//	});

	let _AddWeb3Record = (i) => {
		i.tag={title: i.tagTitle};
		i.user={name: i.userName};
//		assignments.value.push(i);  
		form =useForm(i);
		web3Record_store();
	}

	function removeObjectWithId(arr, id) {
	  return arr.filter((obj) => obj.id !== id);
	}

	let _Delete = (i) => {
		//const newArr = removeObjectWithId(assignments.value, i.id);
		//assignments.value = newArr;
		//task_destroy(i);
	}

	let _Set = (i) => {
		console.log('web3Record.id set:',i);
		//const newArr = removeObjectWithId(assignments.value, i.id);
		//assignments.value = newArr;
		//task_destroy(i);
	}







    const newTitle = ref('');
    const newContent = ref('');

</script>

<template>

	<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4 ">
		<web3-record v-for="e in records" :key="e.id" :e="e"  
			@delete="_Delete"  
			@complete="$emit('setcomplete', e.id, e.completed, 'completed')"  
			@archive="$emit('setcomplete', e.id, e.completed, 'archived')"  
			@loadpay="$emit('loadpayload',[e.id])" 
			@loadtablet="$emit('loadtabletload',[e.id])"
		/>
        
        <!-- Experimental !
		<web3-card v-for="e in records"  :name="e.title" col="bg-white" />
				!Experiment -->
	</div>

</template>

