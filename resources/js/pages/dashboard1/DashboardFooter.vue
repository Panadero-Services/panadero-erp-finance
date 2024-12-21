<script setup>
    const props = defineProps({
    laravelVersion: String,
    phpVersion: String,
    selfVersion: String,
    moduleName: String,
    moduleGit: String
});
</script>

<template>
       <!-- Page Footer -->
        <div class="ml-4 text-center text-xsm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0 inline-block">
            <p>Laravel v{{ laravelVersion }}</p>
            <p>(PHP v{{ phpVersion }})</p>
            <p>SELF v{{selfVersion}}</p>
            <p>moduleName: {{moduleName}}</p> 
            <p>moduleGit: {{moduleGit}}</p> 
        </div>
</template>
