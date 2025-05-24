/* eslint-disable @typescript-eslint/no-unused-vars */
function getData() {
	
   const users = [
		{id: 1, label: "Planning", parent:null},
		{id: 2, label: "devOps", parent:null},
		{id: 3, label: "Boss1", parent:1, unit: "hours/day", avatar: "https://snippet.dhtmlx.com/codebase/data/kanban/03/user1.png" },
		{id: 4, label: "Boss2", parent:1, unit: "hours/day", avatar: "https://snippet.dhtmlx.com/codebase/data/kanban/03/user2.jpeg" },
		{id: 5, label: "Worker1", parent:2, unit: "hours/day" },
		{id: 6, label: "Worker2", parent:2, unit: "hours/day" },
		{id: 7, label: "Worker3", parent:2, unit: "hours/day" },
		{id: 8, label: "Tourist2", parent:2, unit: "hours/day" },
		{id: 9, label: "Unassigned", parent:2},
    ];

	const cardShape = {
		label: true,
		description: true,
		progress: true,
		start_date: true,
		end_date: true,
		users: {
			show: true,
			values: users,
		},
		priority: {
			show: true,
			values: [
				{ id: 1, color: "#FF5252", label: "High" },
				{ id: 2, color: "#FFC975", label: "Medium" },
				{ id: 3, color: "#65D3B3", label: "Low" },
			],
		},
		color: true,
		menu: true,
		cover: true,
		links: true,
		comments: true,
		attached: false,
	};

	const columns = [
		{ label: "Backlog", id: "backlog" },
		{ label: "Idle", id: "idle"},
		{ label: "Pending", id: "testing" },
		{ label: "Started", id: "started" },
		{ label: "Done", id: "done" },
		{ label: "Archive", id: "archive" },
	];

	const rows = [
		{ label: "Feature", id: "feature" },
		{ label: "Task", id: "task" },
	];

	const cards = [
		{
			id: 1,
			label: "Integration with Angular/React",
			priority: 1,
			color: "#65D3B3",
			start_date: new Date("01-06-2025"),
			users: [3, 2],
			column: "backlog",
			type: "feature",
			comments: [
				{
					id: 1,
					userId: 1,
					cardId: 1,
					text: "Greetings, fellow colleagues. I would like to share my insights on this task. I reckon we should deal with at least half of the points in the plan without further delays. ",
					date: new Date(),
				},
				{
					id: 2,
					userId: 2,
					cardId: 1,
					text: "Hi, Aaron. I am sure that that's exactly what is thought best out there in Dunwall. Let's just do what we are supposed to do to get the result.",
					date: new Date(),
				},
			],
			votes: [1, 3, 4],
		},
		{
			id: 2,
			label: "Archive the cards/boards ",
			priority: 3,
			color: "#58C3FE",
			users: [4],
			progress: 1,
			column: "backlog",
			type: "feature",
		},
		{
			id: 3,
			label: "Searching and filtering",
			priority: 1,
			color: "#58C3FE",
			start_date: new Date("03-06-2025"),
			users: [3, 1],
			progress: 1,
			column: "backlog",
			type: "task",
		},
		{
			id: 4,
			label: "Set the tasks priorities",
			color: "#FFC975",
			start_date: new Date("05-06-2025"),
			users: [4],
			progress: 75,
			column: "idle",
			type: "feature",
			attached: [
				{
					isCover: true,
					coverURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/1.png",
					previewURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/1.png",
					url: "https://snippet.dhtmlx.com/codebase/data/kanban/03/1.png",
					name: "1.png",
				},
			],
		},
		{
			id: 5,
			label: "Custom icons",
			color: "#65D3B3",
			start_date: new Date("01-07-2025"),
			users: [3, 2],
			column: "idle",
			type: "task",
		},
		{
			id: 6,
			label: "Integration with Gantt",
			color: "#FFC975",
			start_date: new Date("12-06-2025"),

			users: [4],
			progress: 75,
			column: "idle",
			type: "task",
		},
		{
			id: 7,
			label: "Drag and drop",
			priority: 1,
			color: "#58C3FE",
			users: [3, 1],
			progress: 100,
			column: "testing",
			type: "feature",
		},
		{
			id: 8,
			label: "Adding images",
			color: "#58C3FE",
			users: [4],
			column: "backlog",
			type: "task",
			attached: [
				{
					isCover: true,
					coverURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					previewURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					url: "https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					name: "2.png",
				},
			],
		},
		{
			id: 9,
			label: "Create cards and lists from the UI and from code",
			priority: 3,
			color: "#65D3B3",
			start_date: new Date("01-07-2025"),
			users: [3, 2],
			column: "done",
			type: "feature",
		},
		{
			id: 10,
			label: "Draw swimlanes",
			color: "#FFC975",
			users: [2],
			column: "done",
			type: "feature",
		},
		{
			id: 11,
			label: "Progress bar",
			priority: 1,
			color: "#FFC975",
			start_date: new Date("12-05-2025"),
			users: [1, 4, 3],
			progress: 100,
			column: "done",
			type: "task",
		},
		{
			id: 12,
			label: "Speech team",
			color: "#FFC975",
			users: [1],
			column: "started",
			type: "feature",
		},
		{
			id: 13,
			label: "Fetch datasource",
			color: "#FFC975",
			users: [2],
			column: "done",
			type: "task",
		},
		{
			id: 14,
			label: "Create scripts as boilerplate",
			color: "#FFC975",
			users: [3],
			column: "started",
			type: "task",
		},
		{
			id: 15,
			label: "Discuss with our MicroManager",
			color: "#FFC975",
			users: [14],
			column: "started",
			type: "task",
		},
		{
			id: 16,
			label: "Set the feature buttonbar",
			color: "#FFC975",
			start_date: new Date("12-06-2025"),
			users: [4],
			progress: 20,
			column: "started",
			type: "task",
			attached: [
				{
					isCover: true,
					coverURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					previewURL:
						"https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					url: "https://snippet.dhtmlx.com/codebase/data/kanban/03/2.png",
					name: "1.png",
				},
			],
		},
		{
			id: 17,
			label: "Predict the feature customer demand",
			color: "#FFC975",
			start_date: new Date("12-05-2025"),
			users: [4],
			progress: 10,
			column: "started",
			type: "feature",
			attached: [
				{
					isCover: true,
					coverURL:
						"/storage/kanban/self.png",
					previewURL:
						"/storage/kanban/self.png",
					url: "/storage/kanban/self.png",
					name: "self.png",
				},
			],
		},

		{
			id: 18,
			label: "Adding images",
			color: "#58C3FE",
			users: [2],
			column: "archive",
			type: "task",
			attached: [
				{
					isCover: true,
					coverURL:
						"/storage/kanban/self.png",
					previewURL:
						"/storage/kanban/self.png",
					url: "/storage/kanban/self.png",
					name: "self.png",
				},
			],
		},
	];

	const groupData = [
		{ id: "column", label: "Column", columns },
		{
			id: "sprint",
			label: "Sprint",
			columns: [
				{ id: "1.0", label: "1.0" },
				{ id: "1.1", label: "1.1" },
				{ id: "1.2", label: "1.2" },
			],
		},
		{
			id: "type",
			label: "Type",
			columns: [
				{ id: "feature", label: "Feature" },
				{ id: "task", label: "Task" },
			],
		},
		{
			id: "priority",
			label: "Priority",
			columns: [
				{ id: 1, label: "High" },
				{ id: 2, label: "Medium" },
				{ id: 3, label: "Low" },
			],
		},
	];

	const links = [
		{
			id: 1,
			masterId: 1,
			slaveId: 3,
			relation: "parent",
		},
		{
			id: 3,
			masterId: 1,
			slaveId: 3,
			relation: "duplicate",
		},
		{
			id: 4,
			masterId: 2,
			slaveId: 1,
			relation: "relatesTo",
		},
	];

	return {
		users,
		rows,
		columns,
		cards,
		cardShape,
		groupData,
		links,
	};
}

const notFoundMood = {"cards":[{"column":"idle","id":"temp://1748009221229","label":"This CardSet","description":"","start_date":null,"end_date":null,"priority":"","users":"","color":"","progress":0,"links":"","comments":[]},{"column":"testing","id":"temp://1748009221230","label":"Is Not Found","description":"","start_date":null,"end_date":null,"priority":"","users":"","color":"","progress":0,"links":"","comments":[]}],"links":[{"source":"1744233170934","target":"1744233170941","type":"0","id":1744270710513},{"source":"1744233170927","target":"1744233170934","type":"0","id":1744270710514},{"source":"1744233170941","target":"1744270710533","type":"0","id":1744270710540},{"source":"1744270710533","target":"1744270710541","type":"0","id":1744270710548},{"source":"1744270710541","target":"1744271297417","type":"0","id":1744271297424},{"source":"1744271297425","target":"1744271297417","type":"3","id":1744271297432},{"source":"1744271297433","target":"1744271297440","type":"0","id":1744271297447},{"source":"1744271297433","target":"1744271297425","type":"3","id":1744271297448}],"columns":[{"label":"Backlog","id":"backlog"},{"label":"Idle","id":"idle"},{"label":"Pending","id":"testing"},{"label":"Started","id":"started"},{"label":"Done","id":"done"},{"label":"Archive","id":"archive"}],"rows":[{"id":""}]};

const defaultMood = {"cards":[{"id":1744233170927,"start_date":"2025-04-07T22:00:00.000Z","text":"week Functional Design","duration":11,"owner":[{"$id":1744893899090,"start_date":"2025-04-07T22:00:00.000Z","duration":11,"end_date":"2025-04-18T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"},{"$id":1744893899091,"start_date":"2025-04-07T22:00:00.000Z","duration":11,"end_date":"2025-04-18T22:00:00.000Z","delay":0,"resource_id":"4","value":"4","mode":"default"}],"end_date":"2025-04-18T22:00:00.000Z","progress":26.920315865039484,"parent":"0","type":"task","column":"started","color":"","label":"week Functional Design","users":[3,4]},{"id":1744233170934,"start_date":"2025-04-08T22:00:00.000Z","text":"Voorbereiden","duration":1,"parent":"1744233170927","owner":[{"$id":1744893899103,"start_date":"2025-04-08T22:00:00.000Z","duration":1,"end_date":"2025-04-09T22:00:00.000Z","delay":0,"resource_id":"6","value":"4","mode":"default"}],"end_date":"2025-04-09T22:00:00.000Z","progress":34.12698412698413,"type":"task","column":"started","color":"","label":"Voorbereiden","users":[6]},{"id":1744233170941,"start_date":"2025-04-09T22:00:00.000Z","text":"Inpakken","duration":4,"parent":"1744233170927","owner":[{"$id":1744893899114,"start_date":"2025-04-09T22:00:00.000Z","duration":4,"end_date":"2025-04-13T22:00:00.000Z","delay":0,"resource_id":"8","value":"4","mode":"default"}],"end_date":"2025-04-13T22:00:00.000Z","progress":17.984189723320156,"type":"task","column":"started","color":"","label":"Inpakken","users":[8]},{"id":1744270710533,"start_date":"2025-04-11T22:00:00.000Z","text":"Western residentie","duration":3,"parent":"1744233170927","owner":[{"$id":1744893899041,"start_date":"2025-04-11T22:00:00.000Z","duration":3,"end_date":"2025-04-14T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"},{"$id":1744893899042,"start_date":"2025-04-11T22:00:00.000Z","duration":3,"end_date":"2025-04-14T22:00:00.000Z","delay":0,"resource_id":"4","value":"4","mode":"default"}],"end_date":"2025-04-14T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Western residentie","users":[3,4]},{"id":1744271297417,"start_date":"2025-04-16T22:00:00.000Z","text":"whatever","duration":1,"parent":"1744233170927","owner":[],"end_date":"2025-04-17T22:00:00.000Z","progress":0,"column":"idle","color":"","label":"whatever","users":[]},{"id":1744270710541,"start_date":"2025-04-13T22:00:00.000Z","text":"Evaluatie","duration":3,"owner":[],"end_date":"2025-04-16T22:00:00.000Z","progress":0,"parent":0,"column":"idle","color":"","label":"Evaluatie","users":[]},{"id":1744271297425,"start_date":"2025-04-20T22:00:00.000Z","text":"week FD Implementatie","duration":7,"owner":[{"$id":1744893899053,"start_date":"2025-04-20T22:00:00.000Z","duration":7,"end_date":"2025-04-27T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"}],"end_date":"2025-04-27T22:00:00.000Z","progress":0,"parent":"0","type":"task","column":"idle","color":"","label":"week FD Implementatie","users":[3]},{"id":1744271297433,"start_date":"2025-04-20T22:00:00.000Z","text":"Meeting","duration":1,"parent":"1744271297425","owner":[{"$id":1744893899075,"start_date":"2025-04-20T22:00:00.000Z","duration":1,"end_date":"2025-04-21T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"},{"$id":1744893899076,"start_date":"2025-04-20T22:00:00.000Z","duration":1,"end_date":"2025-04-21T22:00:00.000Z","delay":0,"resource_id":"4","value":"4","mode":"default"},{"$id":1744893899077,"start_date":"2025-04-20T22:00:00.000Z","duration":1,"end_date":"2025-04-21T22:00:00.000Z","delay":0,"resource_id":"5","value":"4","mode":"default"},{"$id":1744893899078,"start_date":"2025-04-20T22:00:00.000Z","duration":1,"end_date":"2025-04-21T22:00:00.000Z","delay":0,"resource_id":"6","value":"4","mode":"default"},{"$id":1744893899079,"start_date":"2025-04-20T22:00:00.000Z","duration":1,"end_date":"2025-04-21T22:00:00.000Z","delay":0,"resource_id":"7","value":"4","mode":"default"}],"end_date":"2025-04-21T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Meeting","users":[3,4,5,6,7]},{"id":1744271297440,"start_date":"2025-04-22T22:00:00.000Z","text":"TeamStart","duration":1,"parent":"1744271297425","owner":[{"$id":1744893899064,"start_date":"2025-04-22T22:00:00.000Z","duration":1,"end_date":"2025-04-23T22:00:00.000Z","delay":0,"resource_id":"7","value":"4","mode":"default"}],"end_date":"2025-04-23T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"TeamStart","users":[7]}],"links":[{"source":"1744233170934","target":"1744233170941","type":"0","id":1744270710513},{"source":"1744233170927","target":"1744233170934","type":"0","id":1744270710514},{"source":"1744233170941","target":"1744270710533","type":"0","id":1744270710540},{"source":"1744270710533","target":"1744270710541","type":"0","id":1744270710548},{"source":"1744270710541","target":"1744271297417","type":"0","id":1744271297424},{"source":"1744271297425","target":"1744271297417","type":"3","id":1744271297432},{"source":"1744271297433","target":"1744271297440","type":"0","id":1744271297447},{"source":"1744271297433","target":"1744271297425","type":"3","id":1744271297448}],"columns":[{"label":"Backlog","id":"backlog"},{"label":"Idle","id":"idle"},{"label":"Pending","id":"testing"},{"label":"Started","id":"started"},{"label":"Done","id":"done"},{"label":"Archive","id":"archive"}],"rows":[{"id":""}]};
const defaultMood1 = {"cards":[{"id":1744233170927,"start_date":"2025-04-09T22:00:00.000Z","text":"Bovenverdieping","duration":64,"owner":[],"end_date":"2025-06-12T22:00:00.000Z","progress":22.5,"parent":0,"type":"project","column":"started","color":"#58C3FE","label":"Bovenverdieping","users":[]},{"id":1744233170934,"start_date":"2025-04-09T22:00:00.000Z","text":"Inspectie","duration":2,"parent":"1744233170927","owner":[{"$id":1746031383617,"start_date":"2025-04-09T22:00:00.000Z","duration":2,"end_date":"2025-04-11T22:00:00.000Z","delay":0,"resource_id":"6","value":"2","mode":"default"}],"end_date":"2025-04-11T22:00:00.000Z","progress":100,"type":"task","column":"done","color":"","label":"Inspectie","users":[6]},{"id":1744292388763,"start_date":"2025-04-13T22:00:00.000Z","text":"Strippen Tapijt + Plafond","duration":4,"end_date":"2025-04-17T22:00:00.000Z","progress":100,"parent":"1744233170927","owner":[{"$id":1746031383638,"start_date":"2025-04-13T22:00:00.000Z","duration":4,"end_date":"2025-04-17T22:00:00.000Z","delay":0,"resource_id":"3","value":"2","mode":"default"},{"$id":1746031383639,"start_date":"2025-04-13T22:00:00.000Z","duration":4,"end_date":"2025-04-17T22:00:00.000Z","delay":0,"resource_id":"5","value":"2","mode":"default"}],"type":"task","column":"done","color":"","label":"Strippen Tapijt + Plafond","users":[3,5]},{"id":1746031383367,"start_date":"2025-05-23T22:00:00.000Z","text":"Plafondplaten","duration":4,"parent":"1744233170927","owner":[],"end_date":"2025-05-27T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Plafondplaten","users":[]},{"id":1746031383290,"start_date":"2025-05-27T22:00:00.000Z","text":"Muren + plafond stucen","duration":4,"parent":"1744233170927","owner":[],"end_date":"2025-05-31T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Muren + plafond stucen","users":[]},{"id":1746031383400,"start_date":"2025-05-31T22:00:00.000Z","text":"Muren + plafond spuiten","duration":4,"parent":"1744233170927","owner":[{"$id":1746031383603,"start_date":"2025-05-31T22:00:00.000Z","duration":4,"end_date":"2025-06-04T22:00:00.000Z","delay":0,"resource_id":"5","value":"3","mode":"default"}],"end_date":"2025-06-04T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Muren + plafond spuiten","users":[5]},{"id":1746031383389,"start_date":"2025-06-04T22:00:00.000Z","text":"Deuren verven","duration":4,"parent":"1744233170927","owner":[],"end_date":"2025-06-08T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Deuren verven","users":[]},{"id":1746031383378,"start_date":"2025-06-08T22:00:00.000Z","text":"Vloeren leggen","duration":4,"parent":"1744233170927","owner":[],"end_date":"2025-06-12T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Vloeren leggen","users":[]},{"id":1746031383003,"start_date":"2025-04-20T22:00:00.000Z","text":"Badkamer","duration":20,"owner":[],"end_date":"2025-05-10T22:00:00.000Z","progress":0,"parent":0,"type":"project","column":"idle","color":"#58C3FE","label":"Badkamer","users":[]},{"id":1744377272654,"start_date":"2025-04-20T22:00:00.000Z","text":"Badkamermuur slopen","duration":3,"parent":"1746031383003","owner":[{"$id":1746031383054,"start_date":"2025-04-20T22:00:00.000Z","duration":3,"end_date":"2025-04-23T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"}],"end_date":"2025-04-23T22:00:00.000Z","progress":99.375,"type":"task","column":"done","color":"","label":"Badkamermuur slopen","users":[3]},{"id":1744292388777,"start_date":"2025-04-24T22:00:00.000Z","text":"Tegels badkamer verwijderen","duration":8,"end_date":"2025-05-02T22:00:00.000Z","progress":83.75,"parent":"1746031383003","owner":[{"$id":1746031383202,"start_date":"2025-04-24T22:00:00.000Z","duration":8,"end_date":"2025-05-02T22:00:00.000Z","delay":0,"resource_id":"4","value":"8","mode":"default"}],"type":"task","column":"started","color":"","label":"Tegels badkamer verwijderen","users":[4]},{"id":1744377272668,"start_date":"2025-05-04T22:00:00.000Z","text":"badkamer verven","duration":2,"parent":"1746031383003","owner":[],"end_date":"2025-05-06T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"badkamer verven","users":[]},{"id":1744377272675,"start_date":"2025-05-07T22:00:00.000Z","text":"badkamer voegen","duration":3,"parent":"1746031383003","owner":[],"end_date":"2025-05-10T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"badkamer voegen","users":[]},{"id":1744377272661,"start_date":"2025-05-01T22:00:00.000Z","text":"sanitair","duration":2,"parent":"1746031383003","owner":[],"end_date":"2025-05-03T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"sanitair","users":[]},{"id":1746031383329,"start_date":"2025-04-20T22:00:00.000Z","text":"Elektra","duration":7,"owner":[],"end_date":"2025-04-27T22:00:00.000Z","progress":0,"parent":"0","type":"project","column":"idle","color":"#58C3FE","label":"Elektra","users":[]},{"id":1746031383247,"start_date":"2025-04-20T22:00:00.000Z","text":"kabels slaapkamers","duration":7,"parent":"1746031383329","owner":[],"end_date":"2025-04-27T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"kabels slaapkamers","users":[]}],"links":[{"source":"1744233170934","target":"1744292388763","type":"0","id":1744292388784},{"source":"1744377272654","target":"1744377272661","type":"0","id":1744377272683},{"source":"1744377272661","target":"1744377272668","type":"0","id":1744377272684},{"source":"1744377272668","target":"1744377272675","type":"0","id":1744377272685}],"columns":[{"label":"Backlog","id":"backlog"},{"label":"Idle","id":"idle"},{"label":"Pending","id":"testing"},{"label":"Started","id":"started"},{"label":"Done","id":"done"},{"label":"Archive","id":"archive"}],"rows":[{"id":""}]};
const defaultMood2 = {"cards":[{"id":1744233170927,"start_date":"2025-04-07T22:00:00.000Z","text":"week Functional Design","duration":11,"owner":[{"$id":1744893899090,"start_date":"2025-04-07T22:00:00.000Z","duration":11,"end_date":"2025-04-18T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"},{"$id":1744893899091,"start_date":"2025-04-07T22:00:00.000Z","duration":11,"end_date":"2025-04-18T22:00:00.000Z","delay":0,"resource_id":"4","value":"4","mode":"default"}],"end_date":"2025-04-18T22:00:00.000Z","progress":26.920315865039484,"parent":"0","type":"task","column":"started","color":"","label":"week Functional Design","users":[3,4]},{"id":1744270710533,"start_date":"2025-04-11T22:00:00.000Z","text":"Western residentie","duration":3,"parent":"1744233170927","owner":[{"$id":1744893899041,"start_date":"2025-04-11T22:00:00.000Z","duration":3,"end_date":"2025-04-14T22:00:00.000Z","delay":0,"resource_id":"3","value":"4","mode":"default"},{"$id":1744893899042,"start_date":"2025-04-11T22:00:00.000Z","duration":3,"end_date":"2025-04-14T22:00:00.000Z","delay":0,"resource_id":"4","value":"4","mode":"default"}],"end_date":"2025-04-14T22:00:00.000Z","progress":0,"type":"task","column":"idle","color":"","label":"Western residentie","users":[3,4]}],"links":[{"source":"1744233170934","target":"1744233170941","type":"0","id":1744270710513},{"source":"1744233170927","target":"1744233170934","type":"0","id":1744270710514},{"source":"1744233170941","target":"1744270710533","type":"0","id":1744270710540},{"source":"1744270710533","target":"1744270710541","type":"0","id":1744270710548},{"source":"1744270710541","target":"1744271297417","type":"0","id":1744271297424},{"source":"1744271297425","target":"1744271297417","type":"3","id":1744271297432},{"source":"1744271297433","target":"1744271297440","type":"0","id":1744271297447},{"source":"1744271297433","target":"1744271297425","type":"3","id":1744271297448}],"columns":[{"label":"Backlog","id":"backlog"},{"label":"Idle","id":"idle"},{"label":"Pending","id":"testing"},{"label":"Started","id":"started"},{"label":"Done","id":"done"},{"label":"Archive","id":"archive"}],"rows":[{"id":""}]};

const emptyMoodSet = {"cards":[{"column":"idle","id":"temp://1748009221229","label":"First Card","description":"Start creating your cardSet here !","start_date":null,"end_date":null,"priority":1,"users":[1],"color":"#0096FA","progress":25,"links":"","comments":[]}],"links":[{"source":"1744233170934","target":"1744233170941","type":"0","id":1744270710513},{"source":"1744233170927","target":"1744233170934","type":"0","id":1744270710514},{"source":"1744233170941","target":"1744270710533","type":"0","id":1744270710540},{"source":"1744270710533","target":"1744270710541","type":"0","id":1744270710548},{"source":"1744270710541","target":"1744271297417","type":"0","id":1744271297424},{"source":"1744271297425","target":"1744271297417","type":"3","id":1744271297432},{"source":"1744271297433","target":"1744271297440","type":"0","id":1744271297447},{"source":"1744271297433","target":"1744271297425","type":"3","id":1744271297448}],"columns":[{"label":"Backlog","id":"backlog","collapsed":true},{"label":"Idle","id":"idle"},{"label":"Pending","id":"testing"},{"label":"Started","id":"started"},{"label":"Done","id":"done","collapsed":true},{"label":"Archive","id":"archive","collapsed":true}],"rows":[{"id":""}]};


export { getData, notFoundMood, defaultMood, defaultMood1, defaultMood2, emptyMoodSet  }