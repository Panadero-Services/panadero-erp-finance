<!-- Web3CustomInput.vue -->
<script setup>
  defineProps(['title','modelValue', 'min', 'max', 'rows' ])
  defineEmits(['update:modelValue'])
</script>

<template>
    <div class="grid grid-cols-12 gap-0 mb-1">
      <div class="block lowercase col-span-3 text-gray-700 text-xs font-semibold mb-1 ml-3 mr-3 mt-2">
        {{title}}
      </div>

    <div class = "col-span-8 mb-1">
      <textarea :value="modelValue" :maxlength="max" :rows="rows"  @input="$emit('update:modelValue', $event.target.value)" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-indigo-500 rounded p-2 leading-tight focus:outline-none focus:bg-white text-xs" :placeholder="'Input your ' + title.toLowerCase() + ' ....'"></textarea>
    </div> 

    <div class=" bg-gray-200 text-indigo-700 border border-indigo-500 rounded text-xs text-center pt-2 ml-1 mb-1"
          :class="[modelValue.length < min ? 'bg-gray-200' : 'bg-green-100'  ]">
          {{modelValue.length}}
    </div>
</div>
</template>
