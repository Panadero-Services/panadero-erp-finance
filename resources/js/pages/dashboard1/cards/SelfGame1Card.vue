<script setup>
import {ref, defineEmits} from 'vue';
import ApplicationLogo from '@/components/logoSelf.vue';
import Stars from '@/panaderos/Stars.vue';
import Game from '@/panaderos/Game.vue';


defineProps({
    gameOn : Boolean,
    ready : Boolean, 
    overlay : Boolean,
    callSign : String
});

</script>

<template>
    <!-- SELF Game1 Card -->
    <div class="scale-100 p-2 bg-white opacity-90 dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div>
            <!-- StarsOverlay removed v-if="_set.web3On" -->
            <div class="absolute z-3">
                <div >
                    <Stars :cvxWidth="565" :cvxHeight="280" :starsCount="25" />
                </div>
            </div>

            <!-- Show msg Start Game" -->
            <div class="absolute z-5 p-32">
                <span v-if="ready"><a @click="$emit('start')"  :class="!gameOn ? 'text-black text-xl' : ''" >START GAME</a></span>
            </div>

            <!-- GameOverlay -->
            <div v-if="overlay" class="absolute z-2 ">
                <div class="" >
                    <!-- StarOverlay 
                        <Stars :cvxWidth="windowWidth-100" :cvxHeight="windowHeight-150"  />
                    <Game :cvxWidth="windowWidth-100" :cvxHeight="windowHeight-150" :callSign="selfResolve" />
                     -->
                        <Game :cvxWidth="565" :cvxHeight="280" :callSign="callSign" infPanel="true" :spriteSize="2"/>
                </div>
            </div>
        </div>
    </div>

</template>