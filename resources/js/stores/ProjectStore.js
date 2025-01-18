import { defineStore } from 'pinia';
import axios from 'axios';

export let useProjectStore = defineStore('project',{
    state: () => ({
        balance:1,
        defaultTitle: "none", 
        defaultEnvironment: "none", 
        defaultPath: "none", 
        defaultCategory: "none", 
        validTitles: ['none', 'demo'] , 
        validEnvironments: [] , 
    }),
  
    //Getters are exactly the equivalent of computed values for the state of a Store. They can be defined with the getters property in defineStore(). They receive the state as the first parameter to encourage the usage of arrow function:
    getters: {
        doubleCount: (state) => state.balance * 2,
    },
    actions: {

        async initialize() {
          //  this.fill();
        },

        async getProjectFromDb(_projectId) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getproject", {
                            params: { 
                                id: _projectId,
                                provider: 'Project', 
                            }
                        })
                        .then((response) => {
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`stores/ProjectStore:getProjectFromDb:: ${err}`);
                }
              }
              check(); 
            });


        },
    },

});
