<script setup>
import MiddlewareArchitectureDiagram from '@/components/middleware/MiddlewareArchitectureDiagram.vue';

// You can customize the middleware chain configuration
const customMiddlewareChain = [
    // ... custom middleware configuration
];
</script>

<template>
    <div class="container mx-auto py-8">
        <MiddlewareArchitectureDiagram :middleware-chain="customMiddlewareChain" />
    </div>
</template> 