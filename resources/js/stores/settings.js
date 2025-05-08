import { defineStore } from 'pinia';
import { ref } from 'vue';

const delay = (s) => new Promise(resolve => setTimeout(resolve, s * 1000));

export const useSettingsStore = defineStore('settings', () => {
    const company = ref('PANADEROS');
    const app = ref('PANADEROS');
    const language = ref('EN');
    const domain = ref('SELF');
    const domainFunction = ref('home');
    const pageDetail = ref(true);
    const web3On = ref(true);
    const game3On = ref(false);
    const wallet = ref("0x0");
    const self = ref("nope");
    const animate = ref(false);
    const healthMonitor = ref(false);
    const isMetaMask = ref(false);
    const isSelfAdmin = ref(false);
    const superSelfAdmins = ref(['henrimatisse', 'cleopatra']);
    const mode = ref({ first: false, noob: false, full: false, advanced: false, dev: false });
    const dark = ref(false);
    const projectVisible = ref(false);
    const cookiesAccepted = ref(false);
    const banner = ref({ active: true, msg: "new env initiated...", style: "success" });
    const assets = ref({});
    const projectType = ref('none');
    const layout = ref({ sidebar: false, header: true, subHeader: true, footer: true, developer: false });
    const project = ref({ id: 0, title: "none", validTitles: ['none', 'demo', 'learn', 'rentmagic', 'self', 'vince'], path: "none", environment: 'none', validEnvironments: [], category: 'none' });
    const colors = ref({ bg: " bg-gradient-to-b from-indigo-100/30 dark:from-gray-800/30 " });

    function setLanguage() {
        language.value = language.value === 'EN' ? 'NL' : 'EN';
    }

    function web3Toggle() {
        web3On.value = !web3On.value;
    }

    function game3Toggle() {
        game3On.value = !game3On.value;
    }

    function darkToggle() {
        dark.value = !dark.value;
    }

    function setSelf(_nombre) {
        self.value = _nombre;
        isSelfAdmin.value = _nombre === 'henrimatisse';
    }

    function resetSelf() {
        self.value = "nope";
        isSelfAdmin.value = false;
    }

    function pageDetailToggle() {
        pageDetail.value = !pageDetail.value;
    }

    async function _connect(_contract) {
        let _connected = await initialize();
        if (!_connected) return;
        await delay(2);
        let _self = await _getNextSelf(_contract);
        banner.value = { msg: `selected SELF : ${_self} ... click on it for next item`, style: 'success', active: true };
        await delay(10);
        banner.value = { msg: ' ' + self.value, style: 'success', active: false };
    }

    async function _disconnect() {
        await reset();
        banner.value = { msg: 'wallet disconnected: ', style: 'danger', active: true };
    }

    async function _getNextSelf(_contract) {
        let _self = await _contract.getNextSelfName(wallet.value);
        setSelf(_self);
        return _self;
    }

    async function initSettings(_projectId, _project) {
        project.value = _project;
    }

    async function initMM() {
        try {
            isMetaMask.value = ethereum.isMetaMask;
        } catch (err) {
            console.log('settings.js:initMM failed', err);
        }
    }

    async function initialize() {
        if (!isMetaMask.value) {
            banner.value = { msg: 'MetaMask not installed, check your configuration!', style: 'danger', active: true };
            return false;
        }
        await fill();
        return true;
    }

    async function setProjectType(_type) {
        projectType.value = _type;
    }

    async function setResetProject() {
        projectType.value = 'none';
        project.value.id = 0;
        project.value.title = 'default';
        console.log('project reset!');
    }

    async function reset() {
        wallet.value = '0x0';
        assets.value = {};
        self.value = "nope";
        isSelfAdmin.value = false;
    }

    async function fill() {
        const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        console.log('ethereum.isMetaMask', ethereum.isMetaMask);
        wallet.value = accounts[0];
        banner.value = { msg: 'wallet connected: ... click on address to disconnect', style: 'success', active: true };
    }

    return {
        domainFunction, company, app, language, domain, pageDetail, web3On, game3On, wallet, self, animate, healthMonitor,
        isMetaMask, isSelfAdmin, superSelfAdmins, mode, dark, projectVisible, cookiesAccepted,
        banner, assets, projectType, layout, project, colors,
        setLanguage, web3Toggle, game3Toggle, darkToggle, setSelf, resetSelf, pageDetailToggle,
        _connect, _disconnect, _getNextSelf, initSettings, initMM, initialize, setProjectType,
        setResetProject, reset, fill
    };
});