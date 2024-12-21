<script setup>
//import {computed, onMounted, onUnmounted, ref} from 'vue';

//import i3Card from '@/shared/cards/i3Card.vue';

// css
const _title = "text-indigo-600 dark:text-indigo-300";
const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";
const _shadow_indigo = "shadow-lg shadow-indigo-300 dark:shadow-indigo-600";


</script>


<template >

  <div class="py-6 sm:py-8 lg:py-12 dark:bg-slate-950">
    <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
      <h2 class="text-center text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">Panadero Deployments</h2>
      <p class="mt-2 text-2xl md:text-3xl lg:text-4xl font-semibold tracking-tight text-center text-gray-950 dark:text-gray-200">Everything you need to deploy your dashboard.</p>
      <div class="mt-10 grid gap-4 sm:mt-16 lg:grid-cols-3 lg:grid-rows-2" >
       
        <!-- card1 -->
        <div class="relative lg:row-span-2">
          <div class="absolute inset-px rounded-lg bg-white dark:bg-black lg:rounded-l-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] lg:rounded-l-[calc(2rem+1px)]" :class="_shadow">
            <div class="px-8 pb-3 pt-8 sm:px-10 sm:pb-0 sm:pt-10">
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-900 dark:text-gray-100 max-lg:text-center">Mobile First!</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600 dark:text-gray-400 max-lg:text-center">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo.</p>
            </div>
            <div class="relative min-h-[30rem] w-full grow [container-type:inline-size] max-lg:mx-auto max-lg:max-w-sm ">
              <div class="absolute inset-x-10 bottom-0 top-10 overflow-hidden rounded-t-[12cqw] border-x-[3cqw] border-t-[3cqw] border-gray-700 bg-gray-900 shadow-2xl">
                <img class="size-full object-cover object-top" src="https://tailwindui.com/plus/img/component-images/bento-03-mobile-friendly.png" alt="" />
              </div>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 dark:ring-gray-600 lg:rounded-l-[2rem]" />
        </div>

        <!-- card2 -->
        <div class="relative max-lg:row-start-1" :class="_shadow_indigo">
          <div class="absolute inset-px rounded-lg bg-white dark:bg-black max-lg:rounded-t-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
            <div class="px-8 pt-8 sm:px-10 sm:pt-10">
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-900 dark:text-gray-200 max-lg:text-center">Self Dashboard</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600 dark:text-gray-400 max-lg:text-center">Awesome out-of-the-box building Performances.</p>
            </div>
            <div class="flex flex-1 items-center justify-center px-8 max-lg:pb-12 max-lg:pt-10 sm:px-10 lg:pb-2">
              <img class="w-full max-lg:max-w-xs" src="https://tailwindui.com/plus/img/component-images/bento-03-performance.png" alt="" />
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 dark:ring-gray-600 max-lg:rounded-t-[2rem]" />
        </div>

        <!-- card3 -->
        <div class="relative max-lg:row-start-3 lg:col-start-2 lg:row-start-2" :class="_shadow">
          <div class="absolute inset-px rounded-lg bg-white dark:bg-black" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)]">
            <div class="px-8 pt-8 sm:px-10 sm:pt-10">
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950 dark:text-gray-200 max-lg:text-center">Security</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600 dark:text-gray-400 max-lg:text-center">Morbi viverra dui mi arcu sed. Tellus semper adipiscing suspendisse semper morbi.</p>
            </div>
            <div class="flex flex-1 items-center [container-type:inline-size] max-lg:py-6 lg:pb-2 ">
              <img class="h-[min(152px,40cqw)] object-cover object-center" src="https://tailwindui.com/plus/img/component-images/bento-03-security.png" alt="" />
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 dark:ring-gray-600" />
        </div>



        <!-- card4 -->
        <div class="relative lg:row-span-2 dark:border border-gray-600" :class="_shadow">
          <div class="absolute inset-px rounded-lg bg-white dark:bg-black  max-lg:rounded-b-[2rem] lg:rounded-r-[2rem] " />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-b-[calc(2rem+1px)] lg:rounded-r-[calc(2rem+1px)] ">
            <div class="px-8 pb-3 pt-8 sm:px-10 sm:pb-0 sm:pt-10  ">
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950 dark:text-gray-200 max-lg:text-center ">Powerful APIs</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600 dark:text-gray-400 max-lg:text-center ">Sit quis amet rutrum tellus ullamcorper ultricies libero dolor eget sem sodales gravida.</p>
            </div>
            <div class="relative min-h-[30rem] w-full grow">
              <div class="absolute bottom-0 left-10 right-0 top-10 overflow-hidden rounded-tl-xl bg-gray-900 shadow-2xl">
                <div class="flex bg-gray-800/40 ring-1 ring-white/5 ">
                  <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                    <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 px-4 py-2 text-white">EventHandlers.vue</div>
                    <div class="border-r border-gray-600/10 px-4 py-2">JaWsome.mjs</div>
                  </div>
                </div>
                <div class="px-6 pb-14 pt-6">
                  <!-- Your code example -->
<pre><code class="language-javascript">
<span class="text-yellow-300">function</span> <span class="text-orange-400">rentMagic()</span> {
    console.log(<span class="text-indigo-200">"Hello, @SELF!"</span>);
}
// webhooks
<span class="text-green-500">onMounted</span>(<span class="text-orange-400">async ()</span> => {
   <span class="text-orange-400">await</span> <span class="text-blue-400">_set.initMM();</span>
   <span class="text-orange-400">await</span> <span class="text-blue-400">_set.initialize();</span>
});
                    </code></pre>
                </div>
              </div>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 max-lg:rounded-b-[2rem] lg:rounded-r-[2rem]" />
        </div>

        <move :size="17" :interval="100" />
        <move :size="17" :interval="200" />
        <move :size="17" :interval="300" />

      </div>
    </div>
  </div>

    <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
      <div class="mt-2 grid grid-cols-1 gap-4 sm:mt-4 lg:grid-cols-6 lg:grid-rows-2">
        <div class="flex p-px lg:col-span-4">
          <div class="overflow-hidden rounded-lg bg-gray-800 ring-1 ring-white/15 max-lg:rounded-t-[2rem] lg:rounded-tl-[2rem]">
            <img class="h-80 object-cover object-left" src="https://tailwindui.com/plus/img/component-images/bento-02-releases.png" alt="" />
            <div class="p-10">
              <h3 class="text-sm/4 font-semibold text-gray-400">Releases</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-white">Push to deploy</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In gravida justo et nulla efficitur, maximus egestas sem pellentesque.</p>
            </div>
          </div>
        </div>
        <div class="flex p-px lg:col-span-2">
          <div class="overflow-hidden rounded-lg bg-gray-800 ring-1 ring-white/15 lg:rounded-tr-[2rem]">
            <img class="h-80 object-cover object-center" src="https://tailwindui.com/plus/img/component-images/bento-02-integrations.png" alt="" />
            <div class="p-10">
              <h3 class="text-sm/4 font-semibold text-gray-400">Integrations</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-white">Connect your favorite tools</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-400">Curabitur auctor, ex quis auctor venenatis, eros arcu rhoncus massa.</p>
            </div>
          </div>
        </div>
        <div class="flex p-px lg:col-span-2">
          <div class="overflow-hidden rounded-lg bg-gray-800 ring-1 ring-white/15 lg:rounded-bl-[2rem]">
            <img class="h-80 object-cover object-center" src="https://tailwindui.com/plus/img/component-images/bento-02-security.png" alt="" />
            <div class="p-10">
              <h3 class="text-sm/4 font-semibold text-gray-400">Security</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-white">Advanced access control</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-400">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia.</p>
            </div>
          </div>
        </div>
        <div class="flex p-px lg:col-span-4">
          <div class="overflow-hidden rounded-lg bg-gray-800 ring-1 ring-white/15 max-lg:rounded-b-[2rem] lg:rounded-br-[2rem]">
            <img class="h-80 object-cover object-left" src="https://tailwindui.com/plus/img/component-images/bento-02-performance.png" alt="" />
            <div class="p-10">
              <h3 class="text-sm/4 font-semibold text-gray-400">Performance</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-white">Lightning-fast builds</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-400">Sed congue eros non finibus molestie. Vestibulum euismod augue vel commodo vulputate. Maecenas at augue sed elit dictum vulputate.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-6 lg:grid-rows-2">
        <div class="relative lg:col-span-3">
          <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-t-[2rem] lg:rounded-tl-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)] lg:rounded-tl-[calc(2rem+1px)]">
            <img class="h-80 object-cover object-left" src="https://tailwindui.com/plus/img/component-images/bento-01-performance.png" alt="" />
            <div class="p-10 pt-4">
              <h3 class="text-sm/4 font-semibold text-indigo-600">Performance</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950">Lightning-fast builds</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In gravida justo et nulla efficitur, maximus egestas sem pellentesque.</p>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 max-lg:rounded-t-[2rem] lg:rounded-tl-[2rem]" />
        </div>
        <div class="relative lg:col-span-3">
          <div class="absolute inset-px rounded-lg bg-white lg:rounded-tr-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] lg:rounded-tr-[calc(2rem+1px)]">
            <img class="h-80 object-cover object-left lg:object-right" src="https://tailwindui.com/plus/img/component-images/bento-01-releases.png" alt="" />
            <div class="p-10 pt-4">
              <h3 class="text-sm/4 font-semibold text-indigo-600">Releases</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950">Push to deploy</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600">Curabitur auctor, ex quis auctor venenatis, eros arcu rhoncus massa, laoreet dapibus ex elit vitae odio.</p>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 lg:rounded-tr-[2rem]" />
        </div>
        <div class="relative lg:col-span-2">
          <div class="absolute inset-px rounded-lg bg-white lg:rounded-bl-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] lg:rounded-bl-[calc(2rem+1px)]">
            <img class="h-80 object-cover object-left" src="https://tailwindui.com/plus/img/component-images/bento-01-speed.png" alt="" />
            <div class="p-10 pt-4">
              <h3 class="text-sm/4 font-semibold text-indigo-600">Speed</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950">Built for power users</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600">Sed congue eros non finibus molestie. Vestibulum euismod augue.</p>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 lg:rounded-bl-[2rem]" />
        </div>
        <div class="relative lg:col-span-2">
          <div class="absolute inset-px rounded-lg bg-white" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)]">
            <img class="h-80 object-cover object-center" src="https://tailwindui.com/plus/img/component-images/bento-01-integrations.png" alt="" />
            <div class="p-10 pt-4">
              <h3 class="text-sm/4 font-semibold text-indigo-600">Integrations</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950">Connect your favorite tools</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600">Maecenas at augue sed elit dictum vulputate, in nisi aliquam maximus arcu.</p>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5" />
        </div>
        <div class="relative lg:col-span-2">
          <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-b-[2rem] lg:rounded-br-[2rem]" />
          <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(theme(borderRadius.lg)+1px)] max-lg:rounded-b-[calc(2rem+1px)] lg:rounded-br-[calc(2rem+1px)]">
            <img class="h-80 object-cover object-center" src="https://tailwindui.com/plus/img/component-images/bento-01-network.png" alt="" />
            <div class="p-10 pt-4">
              <h3 class="text-sm/4 font-semibold text-indigo-600">Network</h3>
              <p class="mt-2 text-lg/7 font-medium tracking-tight text-gray-950">Globally distributed CDN</p>
              <p class="mt-2 max-w-lg text-sm/6 text-gray-600">Aenean vulputate justo commodo auctor vehicula in malesuada semper.</p>
            </div>
          </div>
          <div class="pointer-events-none absolute inset-px rounded-lg shadow ring-1 ring-black/5 max-lg:rounded-b-[2rem] lg:rounded-br-[2rem]" />
        </div>
      </div>
    </div>

</template>
