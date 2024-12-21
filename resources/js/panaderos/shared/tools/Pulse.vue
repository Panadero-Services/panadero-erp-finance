<script setup>
import { onMounted, ref } from 'vue';


  const props = defineProps({
    animation: Boolean
  });

const emit = defineEmits(['pulse'])

// *** Timer Section ***
// pageTimer
// Make page timer avail for all child components...... in order to avoid Timer Circus.... 1 timer pulse serves ALL ! 
var PULSE_TIME = 2_000;

// webhooks
onMounted(async ()=> {
   await _startTimer();
})

const _started= ref(true);
const _pulseTime = ref(PULSE_TIME);
const _pulse=ref(false);
var _pageTimer;
var _startTimer = async () => {
    _pageTimer = setInterval( () => {
        if (props.animation){
            _pulse.value = !_pulse.value;
            emit('pulse', _pulse.value);
        }
    }, _pulseTime.value);
}

const _stopTimer = () => {
    clearInterval(_pageTimer); 
}

const _switch = async ()=> {
    if (_started.value){
        _stopTimer(); 
        _started.value=false;
    } else {
        _startTimer();
        _started.value=true;
    }
}
// *** END Timer Section ***

</script>

<template>
</template>