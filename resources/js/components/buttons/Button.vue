<script setup>
import { computed } from 'vue';

const props = defineProps({
	name: {
		type: String,
		default: 'button'
	},
	// Button types
	type: {
		type: String,
		default: 'button',
		validator: (value) => ['button', 'submit', 'reset'].includes(value)
	},
	// Disabled state
	disabled: {
		type: Boolean,
		default: false
	},
	// Loading state
	loading: {
		type: Boolean,
		default: false
	},
	// Focus ring
	focusRing: {
		type: Boolean,
		default: false
	}
});

const emit = defineEmits(['click']);

const _button = computed(() => {
	return [
		// Base styles
		"mx-1 rounded px-2 py-1 text-xs",
		// Ring styles
		"ring-1 ring-inset",
		// Text colors
		"text-gray-600 dark:text-gray-300",
		// Ring colors
		"ring-gray-300 dark:ring-gray-600",
		// Hover states
		"hover:ring-gray-600 dark:hover:ring-indigo-400",
		"hover:text-gray-700 dark:hover:text-gray-200",
		// Focus states
		props.focusRing ? "focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" : "",
		// Dark mode hover background
		"hover:bg-gray-50 dark:hover:bg-gray-800/50",
		// Transition
		"transition-colors duration-200",
		// Disabled state
		props.disabled || props.loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
	].join(' ');
});

const handleClick = (event) => {
	if (!props.disabled && !props.loading) {
		emit('click', props.name);
	}
};
</script>

<template>
	<button 
		:type="type"
		:class="_button"
		:disabled="disabled || loading"
		@click="handleClick"
	>
		<!-- Loading spinner -->
		<svg
			v-if="loading"
			class="animate-spin -ml-1 mr-2 h-4 w-4"
			xmlns="http://www.w3.org/2000/svg"
			fill="none"
			viewBox="0 0 24 24"
		>
			<circle
				class="opacity-25"
				cx="12"
				cy="12"
				r="10"
				stroke="currentColor"
				stroke-width="4"
			></circle>
			<path
				class="opacity-75"
				fill="currentColor"
				d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
			></path>
		</svg>

		{{ name }}
	</button>
</template>