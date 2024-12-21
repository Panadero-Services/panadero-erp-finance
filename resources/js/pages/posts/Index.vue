<script setup>
	import AppLayout from "@/layouts/AppLayout.vue"
	import Pagination from "@/layouts/Pagination.vue"
	defineProps(['posts']);


// css
	const _title= "text-indigo-600 dark:text-indigo-300";

</script>
<template>
	<AppLayout>
		<div class="dark:text-slate-200 m-4 text-base">
		<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
			<div v-for="post in posts.data" key="post.id" class="p-4 text-sm ">
       
        <div class="relative max-lg:row-start-1">
          <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-t-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
            <div class="px-8 pt-8 sm:px-10 sm:pt-10">
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950 max-lg:text-center">{{post.id}} {{post.title}}</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">{{post.body}}</p>
            </div>
            <div class="flex flex-1 items-center justify-center px-8 max-lg:pb-12 max-lg:pt-10 sm:px-10 lg:pb-2 border-t-2 my-4 text-gray-200">
            	

    <div class="-ml-4 flex flex-wrap items-center justify-between sm:flex-nowrap">
      <div class="ml-4 mt-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <img class="h-12 w-12 rounded-full" :src="post.user.profile_photo_url" alt="" />
          </div>
          <div class="ml-4">
            <h3 class="text-base font-semibold leading-6 text-gray-900">{{post.user.name}}</h3>
            <p class="text-sm text-gray-500">
              <a href="#">@email:{{post.user.email}}</a>
            </p>
          </div>
        </div>
      </div>
      </div>


            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 max-lg:rounded-t-[2rem]" />
        </div>





			</div>
		</div>








		</div>



<Pagination :meta="posts.meta" />



	</AppLayout>
</template>