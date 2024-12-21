<script setup>
import { ref } from 'vue';
import { panaderoMiniGames } from "panadero-minigames";



import Stars from '@/panaderos/Stars.vue';
import Game from '@/panaderos/Game.vue';


const emit = defineEmits(['status'])

const props =defineProps({
    id: Number,
  callSign: String, 
  activated: Number
});

const _emitStatus = (_status) =>{
 emit('status', _status);
}



//const Sim = new panaderoMinigames('spaceShip');
//const simMove = ref('');

</script>

<template>
    <Game :id="id"  :callSign="callSign" :cvxWidth="225" :cvxHeight="220" :infoPanel="false" :spriteSize="1.25" :activated="activated" @status="_emitStatus"/>
</template>