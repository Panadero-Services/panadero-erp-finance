import { defineStore } from 'pinia'
import { ethers } from "ethers";

const delay = (s) =>  {  return new Promise(resolve => setTimeout(resolve, s*1000));  }

export let useSettingsStore = defineStore('settings',{
    state: () => ({
        company: 'PANADEROS',
        app: 'PANADEROS',
        language: 'EN',
        pageDetail: true,
        web3On: true,
        game3On: false,
        wallet: "0x0",
        self: "nope",
        animate : false,
        healthMonitor : false,
        isMetaMask : false,
        isSelfAdmin : false,
        superSelfAdmins : ['henrimatisse','cleopatra'],
        dark: false,
        wrenchMode: false,
        cookiesAccepted: false,
  //      animate: true,
        banner: {active: false, msg:"new env initiated...", style:"success"},
        assets: {}, 
        projectId: 0,
        projectTitle: 'default',
        projectType: 'none',
        project: {},
        colors: {bg:" bg-gradient-to-b from-indigo-100/30 dark:from-gray-800/30 "}
        }),

    actions: {
        setLanguage() {
            this.language=this.language=='EN'?'NL':'EN';
        },
        web3Toggle(){
            this.web3On = !this.web3On;
        },
        game3Toggle(){
            this.game3On = !this.game3On;
        },
        darkToggle(){
            this.dark = !this.dark;
        },
        setSelf(_nombre){
            this.self = _nombre;
            this.isSelfAdmin = _nombre=='henrimatisse';
        },
        resetSelf(){
            this.self = "nope";
            this.isSelfAdmin = false;
        },
        pageDetailToggle(){
            this.pageDetail = !this.pageDetail;
        },

        async _connect (_contract) {
            let _connected = await this.initialize();
            if (!_connected) return;
            await delay(2);
            let _self = await this._getNextSelf(_contract);
            this.banner = {msg :`selected SELF :   ${_self} ... click on it for next item`, style : 'success', active : true }; 
            await delay(10);
            this.banner = {msg :'  '+ this.self, style : 'success', active : false }; 
        },
        async _disconnect (_contract) {
            await this.reset();
            this.banner = {msg :'wallet disconnected: ', style : 'danger', active : true }; 
        },

        async _getNextSelf (_contract) {
            let _self = await _contract.getNextSelfName(this.wallet);
            await this.setSelf(_self);
            return _self;
        },

        async initSettings(_projectId, _project) {
            //console.log('initSettings'+_projectId + _project);
            this.project = _project;
        },

        async initMM() {
            //this.wallet ="0z0";
            try {
                this.isMetaMask = ethereum.isMetaMask;
            } catch (err) {
                console.log('settins.js:initMM failed');
                console.log(err);
            }
        },

        async initialize() {
            // metamask
            if (!this.isMetaMask){
                this.banner = {msg :'MetaMask not installed, check your configuration! ', style : 'danger', active : true }; 
                return false;
            } 
            await this.fill();
            return true;
        },
        
        async setProjectType(_type) {
            this.projectType = _type;
        },

        async setResetProject() {
            this.projectType = 'none';
            this.projectId = 0;
            this.projectTitle = 'default';
            console.log('project reset!');
        },

        async reset(){
            this.wallet = '0x0';
            this.assets = {};
            this.self = "nope";
            this.isSelfAdmin = false;
        },

        async fill(){
            const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            console.log('ethereum.isMetaMask', ethereum.isMetaMask);
            const wallet = accounts[0];
            this.wallet = wallet;
            this.banner = {msg :'wallet connected: ... click on address to disconnect', style : 'success', active : true }; 
        },
    }
});