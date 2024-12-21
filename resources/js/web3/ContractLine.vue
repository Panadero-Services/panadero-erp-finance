<script setup>

    import { ref } from 'vue';
    
    const props = defineProps({
        stat: Object,
        idz: idz
      });


</script>

<template>
    <div @click="$emit('retrieve',idz)"  class="overflow-hidden rounded-lg bg-white px-3 py-5 shadow sm:p-6" :class="stat.columns">
        <dt class="truncate text-sm font-medium text-gray-500">{{stat.line}}</dt>
        <dd class="mt-1 font-semibold tracking-tight text-truncate" :class="stat.style">{{ stat.value }}</dd>
    </div>
</template>