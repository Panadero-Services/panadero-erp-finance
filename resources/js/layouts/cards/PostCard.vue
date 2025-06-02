<script setup>
import { ref, computed } from 'vue';

import { formatDistance } from 'date-fns';
import CategorySectionIcon from '@/components/icons/CategorySectionIcon.vue';

const props = defineProps({
    module: String,
    table: String,
    post: Object,
    db: Object
});

// variables
const _iconChangedField = ref('iconChanged');
const _iconChangedId = ref(0);
const _iconChangedValue = ref(false);
const _true = true;
const _false = false;


const processHtml = (html) => {
  return html.replace(/<a data-inertia-link href="([^"]+)">([^<]+)<\/a>/g, '<inertia-link href="$1">$2 click on me</inertia-link>')
}

const _body = processHtml(props.post.body);



let _updateIcon = async (_id, _field, _value) => {    
    console.log('_updateIcon');
    let i={};
    i.id = _id;
    i.field=_field;
    i.value=_value;
    _iconChangedField.value = `${_field} updated`;
    _iconChangedId.value = _id;
    _iconChangedValue.value = _value;
    let _response = await props.db.postUpdateIcon('Post', i);
    console.log(_response);

    // logAction
    const _logData = {
        action: "postcard.updateicon",
        user_id:  usePage().props.auth.user.id || 'no_uid',
        module: props.module, 
        node: 'none',
        team: usePage().props.auth.user.current_team.name || 'no_team', 
        project: 'none', 
        content: _response.data,
        json: '{}',
        //json: JSON.stringify(_response),
        tags: 'content, posts',
    }
      let _response2 = await props.db.logAction(_logData);
      console.log(_response2);
   }

 // css
const _container = {
  base: "p-2 h-[400px] opacity-90 from-gray-700/50 via-transparent rounded-xs shadow-md shadow-gray-400 motion-safe:hover:scale-[1.01] transition-all duration-250  focus:outline focus:outline-2 focus:outline-purple-500",
  light: "bg-white text-gray-600",
  dark: "dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:ring-1 dark:ring-inset dark:ring-white/5 dark:shadow-none dark:text-gray-300"
};

const _card = {
  base: "flex flex-col h-full min-h-24 px-8 pt-4 sm:px-6 sm:pt-6 tracking-tight leading-4 ",
  body: "hover:text-xs transition-all duration-250",
  flex: props.post.body.length > 800 ? "text-xxs pr-6" : "text-xs"
};

const _title = {
  base: "text-lg/5 tracking-tight text-gray-950 max-lg:text-center dark:text-gray-50 ",
};

const _subtitle = {
  base: "text-xxs font-medium tracking-tight text-gray-500 max-lg:text-center dark:text-green-400",
}

const _user = {
  base: "text-base text-gray-900 dark:text-indigo-400",
}

const _footer = {
  base: "mt-auto",
};

</script>

<template>

  <div class="relative block w-full" >
    <div :class="[_container.base, _container.light, _container.dark]">
      <div :class="_card.base">
        <div class="flex flex-col flex-grow overflow-scroll">
          <div id="headers" class="space-y-1">
            <p @click="$emit('edit',post.id)" :class="[_title.base]" >{{post.id}} {{post.title}}</p>
            <p :class="_subtitle.base"> updated: {{formatDistance(post.updated_at, new Date())}} ago </p>
          </div>
          <div id="body" v-html="_body.replaceAll('.','.<br>')"  class="mt-2" :class="[_card.flex, _card.body]">
          </div>
        </div>

        <div :class="_footer.base">
          <div class="grid grid-cols-2 border-t-2 dark:border-gray-700 my-3 mx-6">
            <div class="flex flex-1 justify-start ">
              <div class=" flex flex-wrap items-center justify-between sm:flex-nowrap">
                <div class="ml-0 mt-4 ">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <img class="h-12 w-12 rounded-full" :src="post.user.profile_photo_url" alt="" />
                    </div>
                    <div class="ml-2">
                      <p :class="_user.base">{{post.user.name}}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-2 mt-2 justify-end mr-3">  
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_self', post.is_self==0), post.is_self = post.is_self == 1 ? 0 : 1 " icon="FingerPrint" :activated="post.is_self==1" :error="_false" title="is self"/>
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_locked', post.is_locked==0), post.is_locked = post.is_locked == 1 ? 0 : 1 " icon="LockClosed" :activated="post.is_locked==1" :error="_false" title="is locked"/>
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_featured', post.is_featured==0), post.is_featured = post.is_featured == 1 ? 0 : 1 " icon="Fire" :activated="post.is_featured==1" :error="_false" title="is featured"/>
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_smart', post.is_smart==0), post.smart = post.is_smart == 1 ? 0 : 1 " icon="Heart" :activated="post.is_smart==1" :error="_false" title="is smart"/>
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_public', post.is_public==0), post.public = post.is_public == 1 ? 0 : 1 " icon="LockOpen" :activated="post.is_public==1" :error="_false" title="is public"/>
              <CategorySectionIcon @click="_updateIcon(post.id, 'is_published', post.is_published==0), post.is_published = post.is_published == 1 ? 0 : 1 " icon="Tv" :activated="post.is_published==1" :error="_false" title="is published"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>




</template>