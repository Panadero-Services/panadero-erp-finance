<script setup>
  import { ref } from 'vue'; 
    const props = defineProps({
       records: Object,
       types: Object,
       tag_id: Number,
       scope: String,
       archived: Boolean
    })

  const count = ref(3.3);
  const msg = 'Hello!';
  const show = true;

</script>

<template>

    <div v-if="scope=='Activated'" class="col-span-8 md:col-span-2 2xl:col-span-2 bg-white shadow-xl rounded-lg px-8 py-2 mb-12 h-[425px] overflow-auto">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-assignment">
        Activated
      </label>
      <ul>
        <li v-for="e in records.filter(x=>!x.completed)" :key="e.id" class ="text-xs mb-1">
          <label class="text-xs">
            <input type="checkbox" v-model="e.completed">
            {{e.title}}
          </label>
        </li>
          <!-- Experimental !
        !Experiment -->
      </ul>
    </div>
    
          <!-- Experimental !

    <div class="col-span-3 bg-white shadow-xl rounded-lg px-8 py-2 mb-0">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-assignment">
          Statistics
        </label>

        <div class="bg-white rounded text-sm" v-show="show && records.length<100"> 
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-1">
            <span>Web3Records: ({{ records.length }})</span>
            <p>Types: ({{ types.length }})</p>
            <p>tag:  tag_id   tag_name </p>
          </div>
              <div class="col-span-1">
            <Child @addCount="(i) => count += i" /> 
            : {{ count.toFixed(4) }}
          </div>
        </div>
      </div>
    </div>

    !Experiment -->

    <div v-if="scope=='Finished'" class="col-span-8 md:col-span-2 2xl:col-span-2 bg-white shadow-xl rounded-lg px-8 py-2 mb-12 h-[425px] overflow-auto">
      <label class="block uppercase tracking-wide text-black text-xs font-bold mb-3" for="grid-assignment">
        {{ archived ? 'Archived' : 'Finished' }}
      
      </label>

      <ul v-if="!archived">
        <li v-for="e in records.filter(x=>x.completed && !x.archived)" :key="e.id" class="text-xs mb-1" >
          <label class="text-xs">
            <input type="checkbox" v-model="e.completed"  class="text-xs">
            {{e.id +" " + e.title}}
          </label>
        </li>
      </ul>
    
      <ul v-if="archived">
        <li v-for="e in records.filter(x=>x.archived)" :key="e.id" class="text-xs mb-1" >
          <label class="text-xs">
            <input type="checkbox" v-model="e.archived"  class="text-xs">
            {{e.id +" " + e.title}}
          </label>
        </li>
      </ul>

    </div>

          <!-- Experimental !
        !Experiment -->

</template>