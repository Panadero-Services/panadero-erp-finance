import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useProjectStore = defineStore('project', () => {
    const balance = ref(1);
    const defaultTitle = ref("none");
    const defaultEnvironment = ref("none");
    const defaultPath = ref("none");
    const defaultCategory = ref("none");
    const validTitles = ref(['none', 'demo']);
    const validEnvironments = ref([]);

    const doubleCount = computed(() => balance.value * 2);

    async function initialize() {
        // this.fill();
    }

    async function getProjectFromDb(_projectId) {
        return new Promise((resolve, reject) => {
            const check = async () => {
                try {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    const response = await axios.get("/getproject", {
                        params: {
                            id: _projectId,
                            provider: 'Project',
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                    resolve(response.data);
                } catch (err) {
                    reject(`stores/ProjectStore:getProjectFromDb:: ${err}`);
                }
            };
            check();
        });
    }

    return {
        balance,
        defaultTitle,
        defaultEnvironment,
        defaultPath,
        defaultCategory,
        validTitles,
        validEnvironments,
        doubleCount,
        initialize,
        getProjectFromDb
    };
});