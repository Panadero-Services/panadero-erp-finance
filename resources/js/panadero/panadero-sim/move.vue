<script setup>
import { ref } from 'vue';
import { panaderoSim } from "panadero-sim";

const emit = defineEmits(['status'])

const props =defineProps({
    size: Number,
    interval: Number,
    active: Number
});

const Sim = new panaderoSim('selfSim');
const simMove = ref('');

let _last="";

function move() {
  if (props.active==1){
    simMove.value = Sim.move( props.size);

    let _new = simMove.value.substr(-2);
    if(_last !== _new) {
      if(_new=='🟩') emit('status', 'active');
      if(_new=='🟥') emit('status', 'error');
      if(_new=='🟨') emit('status', 'pause');
      _last = _new;
    }
  }
}

setInterval(move, props.interval);

</script>

<template>
  <p class=" text-gray-950 dark:text-gray-200 max-lg:text-center">Sim.move{{active}}</p>
  <p class="mt-1 text-xxxs lg:text-xxs">{{simMove}}</p>
</template>