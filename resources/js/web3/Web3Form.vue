<script setup>
  import Web3CustomInput from "@/web3/Web3CustomInput.vue";
  import { useWeb3DbStore } from "@/stores/Web3DbStore.js";

  import { ref, reactive, computed } from 'vue';
  const props = defineProps({
   types: Object,
   projects: Object,
   chains: Object
  })

  const web3Db = useWeb3DbStore();

  const newTitle = ref('');
  const newDescription = ref('');
  const newAddress = ref('');
  const newUrl = ref('');
  const newJson = ref('');
  const newAbi = ref('');
  const newTags = ref('');
  const selectedProject = ref(1);
  const selectedChain = ref(1);

  function _addButton (){
  //  newTitle.value='';
    newDescription.value='';
    newAddress.value='';
    newUrl.value='';
    newJson.value='';
    newAbi.value='';
    newTags.value='';
  }

  function _retrieveButton (){
    newTitle.value = web3Db.payload.title;
    newDescription.value = web3Db.payload.description;
    newAddress.value = web3Db.payload.address;
    newUrl.value = web3Db.payload.url;
    newJson.value = web3Db.payload.json;
    newAbi.value = web3Db.payload.abi;
    newTags.value = web3Db.payload.tags;
  }

  const validateButtons = computed (() => {
    let _check = (newTitle.value.length<4 || newDescription.value.length<1 || newAddress.value.length<20 || newUrl.value.length<20 || newJson.value.length<1 || newAbi.value.length<1 || newTags.value.length<1);
    return _check;
  });
  
</script>

<template>


  <div class="col-span-8 md:col-span-4 2xl:col-span-4 h-[450px]">
    <form @submit.prevent="add" class="bg-white shadow-xl rounded-lg px-2 pt-2 pb-2 mb-2">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-4 ml-4 " for="grid-assignment" :class="newTitle==web3Db.payload.title ? 'text-green-500' : 'text-red-500'">
        <button @click="_retrieveButton">Web3Record</button>
      </label>
      <Web3CustomInput title="TITLE" :rows="1" :min="4" :max="32" :modelValue="newTitle" @update:modelValue="newValue => newTitle = newValue"/>
      <Web3CustomInput title="DESCRIPTION" :rows="1" :min="1" :max="600" :modelValue="newDescription" @update:modelValue="newValue => newDescription = newValue"/>
      <Web3CustomInput title="ADDRESS" :rows="1" :min="20" :max="64" :modelValue="newAddress" @update:modelValue="newValue => newAddress = newValue"/>
      <Web3CustomInput title="URL" :rows="1" :min="20" :max="128" :modelValue="newUrl" @update:modelValue="newValue => newUrl = newValue"/>
      <Web3CustomInput title="JSON" :rows="1" :min="1" :max="12000" :modelValue="newJson" @update:modelValue="newValue => newJson = newValue"/>
      <Web3CustomInput title="ABI" :rows="1" :min="1" :max="12000" :modelValue="newAbi" @update:modelValue="newValue => newAbi = newValue"/>
      <Web3CustomInput title="TAGS" :rows="1" :min="3" :max="32" :modelValue="newTags" @update:modelValue="newValue => newTags = newValue"/>


      <div class="grid grid-cols-12 gap-0 mt-2">
        <div class="block col-span-3 lowercase text-gray-700 text-xs font-semibold mb-1 ml-3 mr-3 mt-2">
          project
        </div>
        
        <div class="col-span-3">
          <select v-model="selectedProject" class="text-xs rounded">
            <option v-for="project in projects" :key="project.id" :value="project.id" class="text-xs">
              {{project.title}}
            </option>
          </select>
        </div>

        <div class="block col-span-2 lowercase tracking-wide text-gray-700 text-xs font-bold mb-1 ml-4 mt-2">
          chain
        </div>
        <div class="col-span-3">
          <select v-model="selectedChain" class="text-xs rounded">
            <option v-for="chain in chains" :key="chain.id" :value="chain.id" >
              {{chain.title}}
            </option>
          </select>
        </div>
      </div>

      <div class="-ml-1 mt-6 text-center">
          <button :disabled="validateButtons" v-for="type in types" @click="[$emit('add',  
          {
            title: newTitle,
            web3type_id: type.id,
            web3project_id: selectedProject,
            web3chain_id: selectedChain,
            tags: newTags,
            address: newAddress,
            url: newUrl,
            abi: newAbi,
            description: newDescription,
            json: newJson,
            user_id: $page.props.auth.user.id
          }), _addButton()]"
          class="bg-indigo-600 hover:bg-indigo-700 hover:bg-gray-500 text-white m-1 p-1 text-xs rounded focus:outline-none focus:shadow-outline disabled:bg-gray-200" 
          >
          {{type.title}}
        </button>
      </div>
    </form>
  </div>
</template>