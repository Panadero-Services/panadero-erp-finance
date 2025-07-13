<script setup>
import { ref } from 'vue';
import SolarSysGame from 'panadero-solarsysinvaders';

defineProps({
    w: Number,
    h: Number,
    callSign: String
});

</script>

<template>
    <div class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center">
        <div class="relative w-full max-w-[1200px] aspect-[3/2] mx-4">
            <SolarSysGame :multiplayer="true" />
        </div>
    </div>
</template>