<script setup>
  import { ref, computed, watch } from 'vue';
  import i3Card from '@/panadero/shared/cards/i3Card.vue';

  import { useDataLayerStore } from '@/panaderosbento/stores/BentoDataLayer';
  let dataLayer = useDataLayerStore();

  const props = defineProps({
    set: Object,
    pulse: Boolean
  });

  // css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
  const _shadow_indigo = "shadow-lg shadow-indigo-300 dark:shadow-indigo-600";

  const _card = ref({ active: true, 
                  title: "title KPI",
                  subTitle: "sub KPI",
                  footer: false, 
                  waarde: dataLayer.kpi1, 
                });

  const _buttons = [
      { command:'start', color: 'green'},
      { command:'stop', color: 'indigo'},
      { command:'reset', color: 'indigo'},
      { command:'kill', color: 'red'},
    ];

const _start = (_p) => {
  dataLayer.kpi1++;
}

const _pulse = computed ( () => { return props.pulse;} );

// watch works directly on a ref
// watch(_pulse, async () => { dataLayer.kpi1++; });

watch(() => props.pulse, () => {
        dataLayer.kpi1++
      _card.value.waarde=dataLayer.kpi1;
    });



/*
ERROR Cross-Origin Resource Sharing (CORS) 
import axios from 'axios';
const _data = { endpoint: 'https://api.kraken.com/0/public/Ticker?pair=ethusdt'}
const getCall = async (_pair='ethusdt') => {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("https://api.kraken.com/0/public/Ticker?pair=ethusdt", {
                          headers: {
                            'Content-Type': 'application/json;charset=UTF-8',
                            'Access-Control-Allow-Origin': '*' // Could work and fix the previous problem, but not in all APIs
                          },
                        })
                        .then((response) => {
                            console.log(response);
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/db.js:getPrice:: ${err}`);
                }
              }
              check(); 
            });
        };
    <div @click="getCall">AXIOS</div>
*/


</script>

<template >
  <div class="py-6 sm:py-2 lg:py-4 dark:bg-black">
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 mx-auto max-w-2xl px-4 lg:px-6 lg:max-w-9xl gap-3 lg:gap-4 ">
        <i3Card :id="1" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="2" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="3" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="4" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="5" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="6" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="7" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="8" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="9" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="10" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="11" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="12" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="13" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="14" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
        <i3Card :id="15" v-model:card="_card" v-model:buttons="_buttons" @start="_start" />
      </div>
    </div>
</template>
