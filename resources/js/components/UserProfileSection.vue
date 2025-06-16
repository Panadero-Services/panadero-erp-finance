<script setup>
import { ref, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['cancel', 'update']);

const _oUpdate = reactive({
	loading: false,
	error: null
});
</script>

<template>
	<div class="flex justify-end gap-2">
		<button type="button" :disabled="_oUpdate.loading" @click="emit('cancel')" :class="buttonStyles.default">Cancel</button>
	</div> 
</template> 