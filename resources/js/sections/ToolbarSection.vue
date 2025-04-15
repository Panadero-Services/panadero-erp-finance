<script setup>
import { ref } from 'vue';
import { BarsArrowUpIcon, PlayIcon, HomeIcon, RocketLaunchIcon, BellIcon, Bars3Icon, WalletIcon, CloudArrowDownIcon, WrenchIcon, UsersIcon, UserIcon, TableCellsIcon, ServerStackIcon, ClipboardDocumentCheckIcon, CircleStackIcon, SwatchIcon, QuestionMarkCircleIcon, SignalIcon, H2Icon} from '@heroicons/vue/24/outline'

const props = defineProps({
    set: Object
});

// define emits
const emit = defineEmits(['activate']);

const _toggleExtended = async () => { _extended.value = !_extended.value; }
//const _toggleUser = async () => { myUserRight.value.open = !myUserRight.value.open; }
const whatever = async (_subject) => { console.log('whatever: '+_subject)}
const _toggleUser = async () => { emit('activate', 'user'); }
const _toggle = async ( _subject) => { emit('activate',  _subject); }

// css
const _indigo = " text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-yellow-400 ";

const navigation = [
  { name: 'Extened', icon: BarsArrowUpIcon, href: _toggleExtended, current: false },
  { name: 'Documents', icon: ClipboardDocumentCheckIcon, href: whatever, current: false },
  { name: 'Signals', icon: SignalIcon, href: whatever, current: false },
  { name: 'Basetables', icon: CircleStackIcon, href: whatever, current: false },
  { name: 'Play', icon: PlayIcon, href: whatever, current: false },
  { name: 'Launch', icon: RocketLaunchIcon, href: whatever, current: false },
  { name: 'Bell', icon: BellIcon, href: whatever, current: false },
  { name: 'Swatch', icon: SwatchIcon, href: whatever, current: false },
  { name: 'Users', icon: UsersIcon, href: whatever, current: false },
  { name: 'Wallet', icon: WalletIcon, href: whatever, current: false },
  { name: 'Tables', icon: TableCellsIcon, href: whatever, current: false },
  { name: 'Serverstacks', icon: ServerStackIcon, href: whatever, current: false },
  { name: 'Manuals', icon: QuestionMarkCircleIcon, href: whatever, current: false },
  { name: 'Wrench', icon: WrenchIcon, href: whatever, current: false },
  { name: 'Team', icon: UsersIcon, href: whatever, current: false },
  { name: 'Reports', icon: TableCellsIcon, href: whatever, current: false },
  { name: 'Project', icon: ClipboardDocumentCheckIcon, href: _toggle, current: false },
  { name: 'Developer', icon: WrenchIcon, href: _toggle, current: false },
  { name: 'User', icon: UserIcon, href: _toggle, current: false },
];

let _extended=ref(false);

</script>

<template>  
  <div v-if="set.layout.sidebar" class="flex bg-slate-100 dark:bg-slate-950 ">
    <nav class="flex flex-1 flex-col" aria-label="Sidebar">
      <ul role="list" class=" space-y-1 mt-1" :class="_extended?'w-32':'w-12'">
        <h2-icon v-if="!set.layout.subHeader" @click="set.layout.subHeader=true" class="w-10 px-3 pb-3 mx-2" :class="_indigo" />
        <li v-for="item in navigation" :key="item.name" class="-ml-1">
          <span @click="item.href(item.name)" class="flex pt-3" :class="_indigo"><component :is="item.icon" :set="set" class="w-10 px-3 mx-1 " /><span v-if="_extended" class="-ml-1 text-sm">{{item.name}}</span></span>
        </li>
      </ul>
    </nav>
  </div>
</template>