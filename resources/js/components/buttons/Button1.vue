<script setup>
import { computed } from 'vue';

const props = defineProps({
	// Button variants
	variant: {
		type: String,
		default: 'primary',
		validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'].includes(value)
	},
	// Button sizes
	size: {
		type: String,
		default: 'xs',
		validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
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
	// Icon only button
	iconOnly: {
		type: Boolean,
		default: false
	},
	// Full width button
	fullWidth: {
		type: Boolean,
		default: false
	},
	// Rounded button
	rounded: {
		type: Boolean,
		default: false
	},
	// Outline style
	outline: {
		type: Boolean,
		default: false
	}
});

const emit = defineEmits(['click']);

const buttonClasses = computed(() => {
	const baseClasses = [
		'inline-flex items-center justify-center font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
		props.fullWidth ? 'w-full' : '',
		props.rounded ? 'rounded-full' : 'rounded-md',
		props.disabled || props.loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
	];

	// Size classes
	const sizeClasses = {
		xs: 'px-2 py-1 text-xs',
		sm: 'px-3 py-1.5 text-sm',
		md: 'px-4 py-2 text-sm',
		lg: 'px-5 py-2.5 text-base',
		xl: 'px-6 py-3 text-lg'
	};

	// Variant classes
	const variantClasses = {
		primary: props.outline
			? 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500'
			: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
		secondary: props.outline
			? 'border-2 border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500'
			: 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
		success: props.outline
			? 'border-2 border-green-600 text-green-600 hover:bg-green-50 focus:ring-green-500'
			: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
		danger: props.outline
			? 'border-2 border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500'
			: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
		warning: props.outline
			? 'border-2 border-yellow-600 text-yellow-600 hover:bg-yellow-50 focus:ring-yellow-500'
			: 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
		info: props.outline
			? 'border-2 border-cyan-600 text-cyan-600 hover:bg-cyan-50 focus:ring-cyan-500'
			: 'bg-cyan-600 text-white hover:bg-cyan-700 focus:ring-cyan-500',
		light: props.outline
			? 'border-2 border-gray-200 text-gray-700 hover:bg-gray-50 focus:ring-gray-200'
			: 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-200',
		dark: props.outline
			? 'border-2 border-gray-800 text-gray-800 hover:bg-gray-50 focus:ring-gray-800'
			: 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-800'
	};

	return [
		...baseClasses,
		sizeClasses[props.size],
		variantClasses[props.variant],
		props.iconOnly ? 'p-2' : ''
	].join(' ');
});

const handleClick = (event) => {
	if (!props.disabled && !props.loading) {
		emit('click', event);
	}
};
</script>

<template>
	<button
		:type="type"
		:class="buttonClasses"
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

		<!-- Button content -->
		<slot></slot>
	</button>
</template>