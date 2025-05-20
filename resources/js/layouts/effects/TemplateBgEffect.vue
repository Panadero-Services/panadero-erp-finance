<script setup>

const p = defineProps({
    effect: Number
});

  let stop1= "#9089FC";
  let stop2= "#FF80B5";

  let _class="absolute inset-x-0 top-1/2 -z-10 -translate-y-1/2 transform-gpu overflow-hidden opacity-30 blur-3xl";
  if (p.effect==2) _class="absolute inset-x-0 top-1/2 -z-10 -translate-y-1/2 transform-gpu overflow-hidden opacity-80 blur-1xl";
  if (p.effect==3) {
    _class="absolute -left-60 top-1/2 -z-10 -translate-y-1/2 transform-gpu overflow-hidden opacity-25 dark:opacity-50 blur-2xl";
      stop1= "#122eff";
      stop2= "#8b25f7";

  }

</script>

<template>
    <!-- template background effect -->
    <div :class="_class">
      <svg viewBox="0 0 1313 771" aria-hidden="true" class="ml-[max(50%,32rem)] w-[82.0625rem]">
        <path id="bc169a03-3518-42d4-ab1e-d3eadac65edc" fill="url(#85a0eca5-25f1-4ab9-af84-4e2d8d9cdbf3)" d="M360.508 589.796 231.671 770.522 0 498.159l360.508 91.637 232.034-325.485c1.485 150.396 51.235 393.965 238.354 165.069C1064.79 143.261 1002.42-107.094 1171.72 46.97c135.44 123.252 148.51 335.641 138.11 426.428L971.677 339.803l24.062 411.461-635.231-161.468Z" />
        <defs>
          <linearGradient id="85a0eca5-25f1-4ab9-af84-4e2d8d9cdbf3" x1="1313.17" x2="-88.881" y1=".201" y2="539.417" gradientUnits="userSpaceOnUse">
            <stop :stop-color="stop1" />
            <stop offset="1" :stop-color="stop2" />
          </linearGradient>
        </defs>
      </svg>
    </div>
</template>