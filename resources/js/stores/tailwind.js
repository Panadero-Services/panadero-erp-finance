// stores/counter.js
import { defineStore } from 'pinia'

export const useTailwindStore = defineStore('tailwind', {
  state: () => {
    return { 
        count: 33,
        bgLight : "bg-indigo-light-25", 
        bgDark : "bg-indigo-dark-800", 
        bg1 : "bg-indigo-light-25 dark:bg-indigo-dark-900 ",
        bg2 : "bg-indigo-light-50 dark:bg-indigo-dark-800 ",
        bg3 : "bg-indigo-light-100 dark:bg-indigo-dark-700 ",
        bg4 : "bg-indigo-light-200 dark:bg-indigo-dark-600 ",
        bg5 : "bg-indigo-light-300 dark:bg-indigo-dark-500 ",
        section_bg_indigo : "bg-gradient-to-l from-indigo-light-200 to-indigo-light-25 dark:from-indigo-dark-600 dark:to-indigo-dark-900 ",
        section_bg_green : "bg-gradient-to-l from-green-200 to-green-50 dark:from-green-600 dark:to-green-900 ",
        menu_bg : "bg-slate-100 dark:bg-slate-900 ",
        menu_bg_gradient : "bg-gradient-to-b from-slate-200 dark:from-slate-900 via-slate-100 dark:via-slate-800 to-slate-200 dark:to-black ",
        menu_bg_gradient2 : "bg-gradient-to-b from-slate-200 dark:from-slate-900 via-slate-100 dark:via-slate-800 to-indigo-light-25 dark:to-black ",
        content : "text-indigo-dark-700 dark:text-indigo-light-200 ", 
        form_field : "bg-indigo-100 focus:bg-white dark:bg-indigo-900 dark:focus:bg-indigo-700 dark:text-white block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs ",
        basic : "text-sm font-normal leading-tight hover:text-black hover:font-semibold dark:hover:text-yellow-400 ",
        menu : "text-xs lg:text-sm font-normal text-gray-700 hover:text-black hover:font-semibold dark:text-gray-300 dark:hover:text-yellow-400 ",
        menuFooter : "text-xs lg:text-sm font-normal text-gray-700 hover:text-black hover:font-semibold dark:text-gray-300 dark:hover:text-yellow-400 ",
        menuTitle : "lg:text-base font-normal text-gray-900 font-semibold dark:text-gray-200  ",
        txt : "text-xs md:text-sm lg:text-base font-normal leading-6 ",
        txt_sm : "text-xs sm:text-sm font-normal leading-6 ",
        orange : "text-orange-600 dark:text-orange-400 ",
        //indigo : "text-indigo-700 dark:text-indigo-light-500 ",
        indigo : "text-indigo-dark-300 dark:text-indigo-light-300 ",
        //idigo : "text-indigo-dark-400 dark:text-indigo-300 ",
        purple : "text-purple-800 dark:text-purple-500 ",
        gray : "text-gray-800 dark:text-gray-400 ",
        gray_light : "text-gray-500 dark:text-gray-500  ",
        titleGray : "font-semibold text-gray-900 dark:text-gray-400 ",

        icon : "w-3 h-3 lg:w-4 lg:h-4 mr-0.5 lg:mr-1.5 ",
        iconTitle : "w-4 h-4 lg:w-5 lg:h-5 mr-0.5 lg:mr-1.5 ",
        social : "h-4 w-4 md:h-5 md:w-5 lg:w-6 lg:h-6 ",
        scale : " scale-75 sm:scale-90 lg:scale-100 ", 
        title : "text-xs lg:text-sm mr-4 tracking-tighter font-semibold ",
        border : " border-gray-300 dark:border-gray-600 ",
        border_thin : "border-gray-300 md:dark:border-gray-800 ",
        sectionTitle : "text-xl sm:text-3xl lg:text-5xl font-semibold tracking-tight text-center ",
        slogan : "text-xxxs md:text-xxs lg:text-xs text-center  uppercase ",
        sectionSubtitle : "leading-6 sm:leading-7 text-sm sm:text-base lg:text-lg font-semibold ",
        wrench1 : "bg-violet-700 dark:bg-violet-400 text-xs text-white dark:text-black ",
        wrench2 : "bg-purple-700 dark:bg-purple-400 text-xs text-white dark:text-black ", 
        card :  "rounded-lg shadow-lg text-gray-400 bg-indigo-light-100 dark:bg-indigo-dark-600 text-gray-800 dark:text-gray-300 ",
        label : "rounded-sm shadow-md border-gray-700 bg-indigo-light-100 dark:bg-indigo-dark-600 text-gray-800 dark:text-gray-300 ",
       
        hover : "hover:text-black hover:font-semibold dark:hover:text-yellow-400 ",
        hoverActive : "hover:shadow-gray-300 dark:hover:shadow-yellow-300 ",
        hoverError : "hover:text-black hover:font-semibold dark:hover:text-red-400 ",
        shadow : "shadow-gray-500 dark:shadow-gray-500 ",
        shadowError : "shadow-red-400 dark:shadow-red-500 ",
        shadowActive : "shadow-green-400 dark:shadow-green-600 ",
     }
  },
  // could also be defined as
  // state: () => ({ count: 0 })
  actions: {
    increment() {
      this.count++
    },
  },
})