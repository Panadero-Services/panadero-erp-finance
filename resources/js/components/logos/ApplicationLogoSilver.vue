<script setup>

    const props = defineProps({
        green: Number     // 0 (red) - 255 (green)
    });

    function random_rgba() {
        var o = Math.round, r = Math.random, s = 255;
        let a = 'rgb(' + o(r()*s) + ',' + props.green + ',' + o(r()*s) + ')';
        return a;
    }
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="32" height="32" viewBox="0 0 64 64" xml:space="preserve">
        <desc>Created with Fabric.js 4.6.0</desc>
        <defs>
        </defs>
        <g transform="matrix(0.18 0 0 2.13 7.5 32)" id="TOG9SNeY5-WvXTq12Yxya"  >
        <path v-bind:fill="random_rgba()"  style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.18 0 0 1.78 17.5 37)" id="-f0qLJzUzfvObgEwQFDsr"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.18 0 0 1.07 27.5 47)" id="d48iDAmQTaEx0D_qEr_kd"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.18 0 0 0.53 37.5 54.5)" id="cK16CjMrdrdtcVQa_m1ZB"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(1.96 0 0 0.18 32.5 4.5)" id="Z2cDaiLZFwruV7hFZdQmT"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(1.25 0 0 0.18 32.5 14.5)" id="-YiaghUVcr-23NEGsyFHa"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.89 0 0 0.18 37.5 34.5)" id="XPQL0O9h_vplRGCfaq3rW"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.18 0 0 0.89 47.5 24.5)" id="EG_h2u-b8o61GSH8UVb0X"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.18 0 0 1.6 57.5 24.5)" id="ZkNy4BfQHIwW-ivxx82vI"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.89 0 0 0.18 47.5 44.5)" id="V_xKI58taT96CcOm2IL4Q"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        <g transform="matrix(0.53 0 0 0.18 32.5 24.5)" id="pmL6lVVqUq6DfLqyOEgJr"  >
        <path v-bind:fill="random_rgba()" style="stroke: rgb(42,143,118); stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(0, 0)" d="M -14.05152 -14.05152 L 14.05152 -14.05152 L 14.05152 14.05152 L -14.05152 14.05152 z" stroke-linecap="round" />
        </g>
        </svg>
</template>
