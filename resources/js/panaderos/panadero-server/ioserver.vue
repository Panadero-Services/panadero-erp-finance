<script setup>
import { ref, onMounted } from 'vue';
import { panaderoServer } from 'panadero-server';


const emit = defineEmits(['status'])

const props =defineProps({
    code: String,
    options: Object,
    active: Number
});

let ioServer = new panaderoServer(props.code, props.options);

const serverMsg = ref('');

// callBackRoutine
async function echoL (txt, pos={y:1,x1:170,x2:190}, _color=5) {
  serverMsg.value = txt;
  emit('status', ioServer.status);
}

const startServer =  async () => {
    let _count = 0;
      while (_count<555) { 
          await ioServer.init(echoL, _count++)
      }
}

// webhooks
onMounted(async ()=> {
   startServer();
})

</script>

<template>
  <div class="grid grid-cols-3 text-xxs mt-4"> 

<div class="col-span-2 -mt-2">
  <div v-for="(value, key) in options" :key="key">
    {{key}} : <span class="text-cyan-600 dark:text-cyan-400">{{value}}</span>
  </div>
    status: <span class="text-cyan-600 dark:text-cyan-400 ">{{ioServer.status}}</span>
</div>


<div class="text-xxs ml-0">
  <p><span v-if="ioServer.status=='init'" >🟡</span><span v-else>⚪️</span> init</p>
  <p><span v-if="ioServer.status=='start'" >🟢</span><span v-else>⚪️</span> starting</p>
  <p><span v-if="ioServer.status=='run'" >🟢</span><span v-else>⚪️</span> running</p>
  <p><span v-if="ioServer.status=='stop'" >🟢</span><span v-else>⚪️</span> stopping</p>
  <p><span v-if="ioServer.status=='end'" >🟡</span><span v-else>⚪️</span> end</p>
</div>

</div>
<div class="text-xxs text-slate-500 dark:text-slate-700 absolute bottom-7">
  {{serverMsg}}
</div>

</template>
