function getData() {

    const resourceData = [
      {id: 1, text: "RM Analyse", type: "project", progress: 0.99, open: false, start_date: "11-11-2024 00:00", duration: 40, end_date: "02-01-2025 00:00", parent: 0},
     
        {id: 2, text: "Onboarding Infodatek", type: "project", progress: 0.99, open: false, start_date: "11-11-2024 00:00", duration: 15, end_date: "18-11-2024 00:00", parent: 1},
        {id: 3, text: "Kennismaking", start_date: "11-11-2024 00:00", duration: 2, parent: 2, progress: 0.99, open: false, end_date: "12-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 6 }] },
        {id: 4, text: "Systems", start_date: "13-11-2024 00:00", duration: 2, parent: 2, progress: 0.99, open: false, end_date: "14-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 6 }] },
        {id: 5, text: "devOps", start_date: "15-11-2024 00:00", duration: 2, parent: 2, progress: 0.99, open: false, end_date: "16-11-2024 00:00", "owner": [{ "resource_id": 3, "value": 6 }] },

        {id: 37, text: "RM Application Learn", type: "project", start_date: "18-11-2024 00:00", duration: 8, parent: 1, progress: 1, open: false, end_date: "01-12-2024 00:00"},
        {id: 38, text: "User Interface", start_date: "18-11-2024 00:00", duration: 2, progress: 1, parent: 37, open: false, end_date: "24-11-2024 00:00","owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 30, text: "Admin Options", start_date: "25-11-2024 00:00", duration: 2, progress: 1, parent: 37, open: false, end_date: "01-12-2024 00:00","owner": [{ "resource_id": 3, "value": 3 }]},

        {id: 6, text: "CodeBase Study", type: "project", start_date: "02-12-2024 00:00", duration: 8, parent: 1, progress: 1, open: false, end_date: "02-01-2025 00:00"},
        {id: 7, text: "DB Scheme", start_date: "02-12-2024 00:00", duration: 2, progress: 1, parent: 6, open: false, end_date: "05-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 8, text: "Models", start_date: "05-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "07-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 9, text: "App lLayout", start_date: "09-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "12-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 10, text: "App Logic", start_date: "12-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "14-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 11, text: "Frontend", start_date: "16-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "21-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 12, text: "Backend", start_date: "23-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "28-12-2024 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 39, text: "Presentation", start_date: "30-12-2024 00:00", duration: 2, parent: 6, progress: 1, open: false, end_date: "03-01-2025 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 40, text: "Evaluation 1.0", start_date: "04-01-2025 00:00", type: "milestone", parent: 1, progress: 1, open: false, end_date: "04-01-2025 00:00", duration: 1},

        {id: 13, text: "RM new TopLevel Technical Design", type: "project", start_date: "03-01-2025 00:00", duration: 30, parent: 0, progress: 1, open: false, end_date: "18-02-2025 00:00"},
        {id: 41, text: "MVC",  start_date: "06-01-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "10-01-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 42, text: "Microservices",  start_date: "13-01-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "17-01-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 43, text: "Middleware",  start_date: "20-01-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "24-01-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 44, text: "Isolated modules",  start_date: "27-01-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "31-01-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 45, text: "Agile methodology ",  start_date: "03-02-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "07-02-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 46, text: "Processes",  start_date: "10-02-2025 00:00", duration: 5, parent: 13, progress: 1, open: false, end_date: "12-02-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 47, text: "Presentation", start_date: "12-02-2025 00:00", duration: 2, parent: 13, progress: 1, open: false, end_date: "14-02-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
        {id: 48, text: "Evaluation 2.0", start_date: "15-02-2025 00:00", type: "milestone", parent: 13, progress: 1, open: false, end_date: "15-02-2025 00:00", duration: 1},

        {id: 31, text: "RM new TD Prototype", type: "project", start_date: "17-02-2025 00:00", duration: 5, parent: 0, progress: 0.2, open: false, end_date: "01-07-2025 00:00"},
        {id: 49, text: "devCycle1", type: "project", start_date: "17-02-2025 00:00", duration: 5, parent: 31, progress: 0.2, open: false, end_date: "30-03-2025 00:00"},

        {id: 14, text: "wk1 Frontend Preparation",  start_date: "17-02-2025 00:00", duration: 3, parent: 49, progress: 1, open: false, end_date: "20-02-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},

        {id: 65, text: "Frontend Stores",  start_date: "24-02-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "01-03-2025 00:00", "owner": [{ "resource_id": 4, "value": 8 }]},
        {id: 56, text: "Pre-controller",  start_date: "24-02-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "01-03-2025 00:00", "owner": [{ "resource_id": 7, "value": 8 }]},
        {id: 57, text: "Nano-service",  start_date: "24-02-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "01-03-2025 00:00", "owner": [{ "resource_id": 3, "value": 7 }]},

        {id: 58, text: "wk3 Backend Preparation",  start_date: "03-03-2025 00:00", duration: 3, parent: 49, progress: 1, open: false, end_date: "06-03-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},

        {id: 15, text: "Backend Model",  start_date: "10-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "15-03-2025 00:00", "owner": [{ "resource_id": 5, "value": 8 }]},
        {id: 61, text: "Backend Controller",  start_date: "10-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "15-03-2025 00:00", "owner": [{ "resource_id": 6, "value": 8 }]},
        {id: 62, text: "Backend Middleware",  start_date: "10-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "15-03-2025 00:00", "owner": [{ "resource_id": 8, "value": 8 }]},
        {id: 63, text: "Documenting",  start_date: "10-03-2025 00:00", duration: 3, parent: 49, progress: 1, open: false, end_date: "15-03-2025 00:00", "owner": [{ "resource_id": 3, "value": 7 }]},

        {id: 59, text: "wk5 Summarize Preparation",  start_date: "17-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "20-03-2025 00:00", "owner": [{ "resource_id": 3, "value": 4 }]},
     
        {id: 17, text: "Backend Model",  start_date: "24-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "29-03-2025 00:00", "owner": [{ "resource_id": 5, "value": 8 }]},
        {id: 27, text: "Backend Middleware",  start_date: "24-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "29-03-2025 00:00", "owner": [{ "resource_id": 6, "value": 8 }]},
        {id: 28, text: "Documenting",  start_date: "24-03-2025 00:00", duration: 3, parent: 49, progress: 1, open: false, end_date: "29-03-2025 00:00", "owner": [{ "resource_id": 3, "value": 7 }]},
        {id: 26, text: "Frontend Stores",  start_date: "24-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "29-03-2025 00:00", "owner": [{ "resource_id": 4, "value": 8 }]},
        {id: 29, text: "Pre-controller",  start_date: "24-03-2025 00:00", duration: 5, parent: 49, progress: 1, open: false, end_date: "29-03-2025 00:00", "owner": [{ "resource_id": 7, "value": 8 }]},
        {id: 20, text: "Evaluation 3.0", start_date: "31-03-2025 00:00", type: "milestone", parent: 49, progress: 0.6, open: false, end_date: "01-04-2025 00:00", duration: 1},

        {id: 60, text: "RM new TD Implementation", type: "project", start_date: "04-04-2025 00:00", duration: 5, parent: 31, progress: 0.1, open: false, end_date: "05-05-2025 00:00"},
        {id: 64, text: "Item #9 Structure redesign",  start_date: "18-04-2025 00:00", duration: 5, parent: 55, progress: 0, open: true, end_date: "23-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 18, text: "Item #10 Structure Modules",  start_date: "25-04-2025 00:00", duration: 5, parent: 55, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 19, text: "Item #11 Structure Components",  start_date: "25-04-2025 00:00", duration: 5, parent: 60, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 21, text: "Item #12 Deployments",  start_date: "25-04-2025 00:00", duration: 5, parent: 60, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 22, text: "Item #13 Rebalance datasources",  start_date: "25-04-2025 00:00", duration: 5, parent: 60, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 23, text: "Item #14 Training",  start_date: "25-04-2025 00:00", duration: 5, parent: 60, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 24, text: "Item #15 Documentation",  start_date: "25-04-2025 00:00", duration: 5, parent: 60, progress: 0, open: true, end_date: "30-04-2025 00:00", "owner": [{ "resource_id": 9, "value": 1 }]},
        {id: 25, text: "Final Deployment 2.0", start_date: "27-04-2025 00:00", type: "milestone", parent: 60, progress: 0, open: true, end_date: "27-04-2025 00:00", duration: 1},
        {id: 32, text: "KlantA database", start_date: "01-05-2025 00:00", duration: 7, parent: 60, progress: 0, open: true, end_date: "05-05-2025 00:00"},
        {id: 33, text: "KlantB Userbase", start_date: "04-05-2025 00:00", duration: 7, parent: 60, progress: 0, open: true, end_date: "07-05-2025 00:00"},
        {id: 34, text: "Interface setup", start_date: "08-05-2025 00:00", duration: 7, parent: 60, progress: 0, open: true, end_date: "15-05-2025 00:00"},
        {id: 35, text: "Documenting", start_date: "01-05-2025 00:00", duration: 7, parent: 60, progress: 0, open: true, end_date: "12-05-2025 00:00"},
        {id: 36, text: "Release v1.0", start_date: "01-05-2025 00:00", type: "milestone", parent: 60, progress: 0, open: true, end_date: "05-05-2025 00:00", duration: 1},


        {id: 65, text: "RM refactor Functional Design", type: "project", start_date: "08-04-2025 00:00", duration: 4, parent: 0, progress: 0.1, open: false, end_date: "09-05-2025 00:00"},
        {id: 66, text: "Item #1 Scope FD",  start_date: "08-04-2025 00:00", duration: 3, parent: 65, progress: 0.25, open: true, end_date: "11-04-2025 00:00", "owner": [{ "resource_id": 3, "value": 3 }]},
        {id: 67, text: "Item #2 Interview & Requirements Stakeholders",  start_date: "14-04-2025 00:00", duration: 4, parent: 65, progress: 0, open: true, end_date: "18-04-2025 00:00", "owner": [{ "resource_id": 3, "value": 2 }]},
        {id: 68, text: "Item #3 Create Functional Design (iterative)",  start_date: "21-04-2025 00:00", duration: 10, parent: 65, progress: 0, open: true, end_date: "01-05-2025 00:00", "owner": [{ "resource_id": 3, "value": 2 }]},
        {id: 69, text: "Item #4 Review Final FD with Technical Architects + Stakeholders",  start_date: "01-05-2025 00:00", duration: 7, parent: 65, progress: 0, open: true, end_date: "06-05-2025 00:00", "owner": [{ "resource_id": 3, "value": 2 }]},
        {id: 70, text: "Item #5 Deliver FD to TD responsible<role> ",  start_date: "06-05-2025 00:00", duration: 7, parent: 65, progress: 0, open: true, end_date: "10-05-2025 00:00", "owner": [{ "resource_id": 3, "value": 2 }]},
        {id: 71, text: "Evaluation 4.0", start_date: "13-05-2025 00:00", type: "milestone", parent: 65, progress: 0.0, open: false, end_date: "13-05-2025 00:00", duration: 1},

    ];
/*
    const ppl = [
     {id: 1, text: "R&D", parent:null},
     {id: 2, text: "devOps", parent:null},
     {id: 3, text: "Lieuwe", parent:1, unit: "hours/day" },
     {id: 4, text: "Marco", parent:2, unit: "hours/day" },
     {id: 5, text: "Gert-Jan", parent:2, unit: "hours/day" },
     {id: 6, text: "Mark", parent:2, unit: "hours/day" },
     {id: 7, text: "Dwayne", parent:2, unit: "hours/day" },
     {id: 8, text: "Andreas", parent:2, unit: "hours/day" },
     {id: 9, text: "Unassigned", parent:2},
     ];
*/

const ppl =[{"id":1,"text":"Planning","parent":0},{"id":2,"text":"Klusploeg","parent":0},{"id":3,"text":"Vince","parent":1,"unit":"hours/day"},{"id":4,"text":"Lieuwe","parent":2,"unit":"hours/day"},{"id":5,"text":"Tessa","parent":2,"unit":"hours/day"},{"id":6,"text":"Mitchel","parent":2,"unit":"hours/day"},{"id":9,"text":"Unassigned","parent":2},{"id":7,"text":"Member","parent":1,"unit":"hours/day"},{"id":8,"text":"Louis","parent":1,"unit":"hours/day"},{"id":10,"text":"Admin","parent":0,"unit":"hours/day"},{"id":11,"text":"Fred","parent":10,"unit":"hours/day"},{"id":12,"text":"FreeWilly","parent":10,"unit":"hours/day"}];


    const resources = [
     {id: 1, text: "Planning", parent:null},
     {id: 2, text: "Klusploeg", parent:null},
     {id: 3, text: "Vince", parent:1, unit: "hours/day" },
     {id: 4, text: "Lieuwe", parent:2, unit: "hours/day" },
     {id: 5, text: "Tessa", parent:2, unit: "hours/day" },
     {id: 6, text: "Mitchel", parent:2, unit: "hours/day" },
     {id: 9, text: "Unassigned", parent:2},
     ];

    const links = [
       {id: "1", source: "1", target: "2", type: "1"},
       {id: "2", source: "1", target: "3", type: "0"},
       {id: "3", source: "3", target: "4", type: "0"},
       {id: "4", source: "4", target: "5", type: "0"},
       {id: "5", source: "5", target: "37", type: "0"},
       {id: "25", source: "2", target: "37", type: "0"},
       {id: "26", source: "37", target: "6", type: "0"},
       {id: "27", source: "6", target: "40", type: "0"},
       {id: "28", source: "2", target: "38", type: "0"},
       {id: "29", source: "38", target: "30", type: "0"},
       {id: "30", source: "30", target: "6", type: "0"},
       {id: "31", source: "12", target: "39", type: "0"},
       {id: "32", source: "39", target: "40", type: "0"},
       {id: "33", source: "40", target: "13", type: "0"},

       {id: "34", source: "41", target: "42", type: "0"},
       {id: "35", source: "42", target: "43", type: "0"},
       {id: "36", source: "43", target: "44", type: "0"},
       {id: "37", source: "44", target: "45", type: "0"},
       {id: "38", source: "45", target: "46", type: "0"},
       {id: "39", source: "46", target: "47", type: "0"},
       {id: "40", source: "47", target: "48", type: "0"},
       {id: "41", source: "48", target: "49", type: "0"},

       {id: "6", source: "30", target: "7", type: "0"},
       {id: "7", source: "7", target: "8", type: "0"},
       {id: "8", source: "8", target: "9", type: "0"},
       {id: "9", source: "9", target: "10", type: "0"},
       {id: "10", source: "10", target: "11", type: "0"},
       {id: "11", source: "11", target: "12", type: "0"},

       {id: "13", source: "15", target: "16", type: "0"},
       {id: "14", source: "16", target: "17", type: "0"},
       {id: "15", source: "17", target: "18", type: "0"},
       {id: "16", source: "18", target: "19", type: "0"},
       {id: "17", source: "19", target: "20", type: "0"},
       {id: "18", source: "20", target: "21", type: "0"},
       {id: "19", source: "21", target: "22", type: "0"},
       {id: "20", source: "22", target: "23", type: "0"},
       {id: "20", source: "23", target: "24", type: "0"},
       {id: "20", source: "24", target: "25", type: "0"},
       {id: "21", source: "32", target: "33", type: "0"},
       {id: "22", source: "33", target: "34", type: "0"},
       {id: "23", source: "34", target: "35", type: "0"},
       {id: "24", source: "35", target: "36", type: "0"},

       {id: "12", source: "14", target: "56", type: "0"},
       {id: "41", source: "14", target: "57", type: "0"},
       {id: "42", source: "14", target: "65", type: "0"},
       {id: "43", source: "56", target: "58", type: "0"},
       {id: "44", source: "57", target: "58", type: "0"},
       {id: "45", source: "65", target: "58", type: "0"},

       {id: "46", source: "58", target: "15", type: "0"},
       {id: "47", source: "58", target: "61", type: "0"},
       {id: "48", source: "58", target: "62", type: "0"},
       {id: "49", source: "58", target: "63", type: "0"},

       {id: "50", source: "15", target: "59", type: "0"},
       {id: "51", source: "61", target: "59", type: "0"},
       {id: "52", source: "62", target: "59", type: "0"},
       {id: "53", source: "63", target: "59", type: "0"},

       {id: "54", source: "59", target: "17", type: "0"},
       {id: "55", source: "59", target: "26", type: "0"},
       {id: "56", source: "59", target: "27", type: "0"},
       {id: "57", source: "59", target: "28", type: "0"},
       {id: "58", source: "59", target: "29", type: "0"},

       {id: "59", source: "17", target: "20", type: "0"},
       {id: "60", source: "26", target: "20", type: "0"},
       {id: "61", source: "27", target: "20", type: "0"},
       {id: "62", source: "28", target: "20", type: "0"},
       {id: "63", source: "29", target: "20", type: "0"},

       {id: "64", source: "65", target: "66", type: "0"},
       {id: "65", source: "66", target: "67", type: "0"},
       {id: "66", source: "67", target: "68", type: "0"},
       {id: "67", source: "68", target: "69", type: "0"},
       {id: "68", source: "69", target: "70", type: "0"},
       {id: "69", source: "70", target: "71", type: "0"},

       ];

    const colors = [['#00695c','#26a69a','#330099','#6633FF', '#ede'],
    ['#F9C241','#FCDF9C','#3CB2C9','#A7E1EC', '#121'],
    ['#37AFE1','#4CC9FE','#FFB22C','#FFDE4D', '#121'],
    ['#9B7EBD','#D4BEE4','#A5B68D','#C1CFA1', '#121'],
    ['#0D92F4','#F95454','#0A6847','#F3CA52', '#000'],
    ['#344C64','#577B8D','#3C3D37','#697565', '#ede']];

//let color = ref(['#00695c','#26a69a','#330099','#6633FF', '#ede']);
//let color = ref(['#F9C241','#FCDF9C','#3CB2C9','#A7E1EC', '#121']);
//let color = ref(['#9B7EBD','#D4BEE4','#A5B68D','#C1CFA1', '#121']);
//let color = ref(['#344C64','#577B8D','#3C3D37','#697565', '#ede']);

    const defaultColor = ['#37AFE1','#4CC9FE','#FFB22C','#FFDE4D', '#121'];

    const taskScaleColor = "#FCFCFC";
    const weekendColor = "#F2F2F2";
    const darkWeekendColor = "#000000";


    return {
        resources, // ppl_vince
        resourceData, 
        ppl, 
        links, 
        colors, 
        defaultColor, 
        taskScaleColor, 
        weekendColor,
        darkWeekendColor 
    };

}

export { getData }
