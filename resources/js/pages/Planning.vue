<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

// usePage
import { usePage } from '@inertiajs/vue3';
const _usePage = usePage();

// stores

import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import { useDbStore } from '@/stores/db';
const _set = useSettingsStore();
const _contract = useContractStore();
const _db = useDbStore();

// sections
import HeaderSection from "@/sections/HeaderSection.vue"
import SubHeaderSection from "@/sections/SubHeaderSection.vue"
import Banner from '@/Components/Banner.vue';

import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

//import { Gantt} from "dhtmlx-gantt";
//import "dhtmlx-gantt/codebase/dhtmlxgantt.css";

// webhooks
onMounted(async ()=> {
  await _set.initMM();
  await _set.initialize();

  let gantt = Gantt.getGanttInstance();
  //gantt.setSkin("meadow");

  gantt.setSkin(_set.dark ? "dark" :  "meadow");
  gantt.message({
    text: "Presentatie van Panadero Services voor Infodatek dag 1",
    expire: 0
  });
  gantt.message({
    text: "Resource planning<b> gefaseeerd naar taken</b> ... (pending...)",
    expire: 0
  });

  gantt.config.row_height = 32;
  gantt.config.bar_height = 24;
  gantt.config.min_column_width = 50;

  gantt.templates.timeline_cell_class = function (item, date) {
    if (date.getDay() == 0 || date.getDay() == 6) {
      return "weekend";
    }
  };

  gantt.config.columns = [
    {name: "text", label: "Task name", width: "*", resize: true, tree: true},
    {name: "start_date", label: "Start time", resize: true, align: "center", width: 90},
    {name: "duration", label: "Duration", resize: true, align: "center", width: 60},
    {name: "add", label: "", width: 44}
  ];

  gantt.config.grid_width = 390;
  gantt.config.date_grid = "%D %d %M";
  gantt.config.scale_height = 40;
  gantt.config.scales = [
    {unit: "week", step: 1, format: "Week #%W"},
    {unit: "day", step: 1, format: "%d %M", css: function (date) {
      if (date.getDay() == 0 || date.getDay() == 6) {
        return "weekend";
      }
    }}
  ];

  (function () {
    gantt.config.font_width_ratio = 7;
    gantt.templates.leftside_text = function leftSideTextTemplate(start, end, task) {
      if (getTaskFitValue(task) === "left") {
        return task.text;
      }
      return "";
    };
    gantt.templates.rightside_text = function rightSideTextTemplate(start, end, task) {
      if (getTaskFitValue(task) === "right") {
        return task.text;
      }
      return "";
    };
    gantt.templates.task_text = function taskTextTemplate(start, end, task) {
      if (getTaskFitValue(task) === "center") {
        return task.text;
      }
      return "";
    };

    function getTaskFitValue(task) {
      var taskStartPos = gantt.posFromDate(task.start_date),
        taskEndPos = gantt.posFromDate(task.end_date);

      var width = taskEndPos - taskStartPos;
      var textWidth = (task.text || "").length * gantt.config.font_width_ratio;

      if (width < textWidth) {
        var ganttLastDate = gantt.getState().max_date;
        var ganttEndPos = gantt.posFromDate(ganttLastDate);
        if (ganttEndPos - taskEndPos < textWidth) {
          return "left"
        }
        else {
          return "right"
        }
      }
      else {
        return "center";
      }
    }
  })();

/*gantt.config.scale_height = 60;

  gantt.config.scales = [
    {unit: "month", step: 1, format: "%F, %Y"},
    {unit: "day", step: 1, format: "%j, %D"}
  ];
*/

//gantt.config.date_scale = "%h, %D";

//gantt.config.start_date = new Da11-2024, 08, 10);
//gantt.config.end_date = new Da11-2024, 08, 20);

  gantt.init("whatever");
  // this.gantt = gantt;

gantt.parse({
    data: [
      {id: 1, text: "InfoDatek Introduction", type: "project", progress: 0.4, open: true, start_date: "30-10-2024 00:00", duration: 16, end_date: "10-11-2024 00:00", parent: 0},
      {id: 2, text: "PresentA", start_date: "30-10-2024 00:00", duration: 2, parent: "1", progress: 0.6, open: true, end_date: "01-11-2024 00:00"},
      {id: 5, text: "Office facing", type: "project", start_date: "30-10-2024 00:00", duration: 8, progress: 0.6, parent: "1", open: true, end_date: "05-11-2024 00:00"},
      {id: 6, text: "PresentB", start_date: "03-11-2024 00:00", duration: 3, parent: "5", progress: 0.6, open: true, end_date: "06-11-2024 00:00"},
      {id: 3, text: "Furniture installation", type: "project", start_date: "11-11-2024 00:00", duration: 8, parent: "1", progress: 0.6, open: true, end_date: "19-11-2024 00:00"},
      {id: 7, text: "Workplaces preparation", start_date: "11-11-2024 00:00", duration: 8, parent: "3", progress: 0.6, open: true, end_date: "19-11-2024 00:00"},
      {id: 4, text: "The employee relocation", type: "project", start_date: "14-11-2024 00:00", duration: 5, parent: "1", progress: 0.5, open: true, end_date: "19-11-2024 00:00"},
      {id: 8, text: "Preparing workplaces", start_date: "14-11-2024 00:00", duration: 5, parent: "4", progress: 0.5, open: true, end_date: "19-11-2024 00:00"},
      {id: 9, text: "Workplaces importation", start_date: "14-11-2024 00:00", duration: 4, parent: "4", progress: 0.5, open: true, end_date: "18-11-2024 00:00"},
      {id: 10, text: "Workplaces exportation", start_date: "14-11-2024 00:00", duration: 3, parent: "4", progress: 0.5, open: true, end_date: "17-11-2024 00:00"},
      {id: 11, text: "Product launch", type: "project", progress: 0.6, open: true, start_date: "02-11-2024 00:00", duration: 18, end_date: "20-11-2024 00:00", parent: 0},
      {id: 12, text: "Perform Initial testing", start_date: "03-11-2024 00:00", duration: 5, parent: "11", progress: 1, open: true, end_date: "08-11-2024 00:00"},
      {id: 13, text: "Development", type: "project", start_date: "03-11-2024 00:00", duration: 14, parent: "11", progress: 0.5, open: true, end_date: "17-11-2024 00:00"},
      {id: 17, text: "Develop System", start_date: "03-11-2024 00:00", duration: 1, parent: "13", progress: 0.4785714285714286, open: true, end_date: "04-11-2024 00:00"},
      {id: 25, text: "Beta Release", start_date: "06-11-2024 00:00", type: "milestone", parent: "13", progress: 0, open: true, end_date: "06-11-2024 00:00", duration: 0},
      {id: 18, text: "Integrate System", start_date: "08-11-2024 00:00", duration: 1, parent: "13", progress: 0.8, open: true, end_date: "09-11-2024 00:00"},
      {id: 19, text: "Test", start_date: "10-11-2024 00:00", duration: 4, parent: "13", progress: 0.2, open: true, end_date: "14-11-2024 00:00"},
      {id: 20, text: "Marketing", start_date: "15-11-2024 00:00", duration: 2, parent: "13", progress: 0, open: true, end_date: "17-11-2024 00:00"},
      {id: 14, text: "Analysis", start_date: "02-11-2024 00:00", duration: 6, parent: "11", progress: 0.8, open: true, end_date: "08-11-2024 00:00"},
      {id: 15, text: "Design", type: "project", start_date: "03-11-2024 00:00", duration: 5, parent: "11", progress: 0.2, open: false, end_date: "08-11-2024 00:00"},
      {id: 21, text: "Design database", start_date: "03-11-2024 00:00", duration: 4, parent: "15", progress: 0.5, open: true, end_date: "07-11-2024 00:00"},
      {id: 22, text: "Software design", start_date: "03-11-2024 00:00", duration: 4, parent: "15", progress: 0.1, open: true, end_date: "07-11-2024 00:00"},
      {id: 23, text: "Interface setup", start_date: "03-11-2024 00:00", duration: 5, parent: "15", progress: 0, open: true, end_date: "08-11-2024 00:00"},
      {id: 16, text: "Documentation creation", start_date: "05-11-2024 00:00", duration: 1, parent: "11", progress: 0, open: true, end_date: "06-11-2024 00:00"},
      {id: 24, text: "Release v1.0", start_date: "20-11-2024 00:00", type: "milestone", parent: "11", progress: 0, open: true, end_date: "20-11-2024 00:00", duration: 0}
    ],
    links: [
      {id: "1", source: "1", target: "2", type: "1"},
      {id: "2", source: "2", target: "3", type: "0"},
      {id: "3", source: "3", target: "4", type: "0"},
      {id: "4", source: "2", target: "5", type: "2"},
      {id: "5", source: "2", target: "6", type: "2"},
      {id: "6", source: "3", target: "7", type: "2"},
      {id: "7", source: "4", target: "8", type: "2"},
      {id: "8", source: "4", target: "9", type: "2"},
      {id: "9", source: "4", target: "10", type: "2"},
      {id: "10", source: "11", target: "12", type: "1"},
      {id: "11", source: "11", target: "13", type: "1"},
      {id: "12", source: "11", target: "14", type: "1"},
      {id: "13", source: "11", target: "15", type: "1"},
      {id: "14", source: "11", target: "16", type: "1"},
      {id: "15", source: "13", target: "17", type: "1"},
      {id: "16", source: "17", target: "25", type: "0"},
      {id: "17", source: "18", target: "19", type: "0"},
      {id: "18", source: "19", target: "20", type: "0"},
      {id: "19", source: "15", target: "21", type: "2"},
      {id: "20", source: "15", target: "22", type: "2"},
      {id: "21", source: "15", target: "23", type: "2"},
      {id: "22", source: "13", target: "24", type: "0"},
      {id: "23", source: "25", target: "18", type: "0"}
    ]
  });

})

onUnmounted(async ()=> {
    //gantt.destructor();
    //cont.innerHTML = "";
})
// css
  const _title = "text-indigo-600 dark:text-indigo-300";
  const _shadow = "shadow-lg shadow-gray-300 dark:shadow-slate-600";

</script>
<template>
    <AppLayout title="Tiers" :set="_set">

        <template #header>
            <Banner />
            <HeaderSection :set="_set" :contract="_contract"/>
            <SubHeaderSection :set="_set"/>
        </template>

        <template #default>

            <div id="whatever" class="h-screen max-w-9xl bg-black"></div>

        </template>
  
    </AppLayout>
</template>
<style>
  

.gantt_task_scale{
    font-size: 11px;
    border-bottom: 1px solid #cecece;
}

.gantt_grid_scale .gantt_grid_head_cell {
    font-size: 12px;
    border-bottom: 1px solid #cecece;
}

.gantt_grid_data{
    font-size: 11px;
    border-bottom: 1px solid #cecece;
}

.gantt_scale {
    color: #aaa;
    font-size: 12px;
    border-bottom: 1px solid #cecece;
}

  /* background color of task bars*/
  .gantt_task_line {
    background:   #6633FF    ;
    border: 1px solid #2898b0;
  }

  /* text color */
  .gantt_task_line .gantt_task_content {
    color: #fff;
    font-size: 11px;
  }

  /* progress fill */
  .gantt_task_progress {
    background-color:  #330099  ;
  }

  /* project */
  /* background color of project bars*/
  .gantt_task_line.gantt_bar_project {
    background-color:  #26a69a ;
    border: 1px solid #3c9445;
    font-size: 11px;
  }

  /* progress of project bars */
  .gantt_task_line.gantt_bar_project .gantt_task_progress {
    background-color:  #00695c ;
  }

  .weekend{ background: #eee !important;}
  .dark .weekend{ background: #222 !important;}

  .gantt_milestone {
    background: #FFCC33 }

</style>
