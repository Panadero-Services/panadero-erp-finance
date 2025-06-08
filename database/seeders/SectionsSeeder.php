<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'id' => 1,
                'title' => 'Supercharge your Web Applications',
                'page_id' => 4,
                'file' => 'HHeroSection.vue',
                'subtitle' => 'Specify the web2 processes of PANADEROS, by implementing... etc',
                'icon' => 'ApplicationLogo',
                'image' => '',
                'slogan' => 'Upgrade with responsive and realtime api\'s',
                'html' => '<div class="flex bg-gray-800/40 ring-1 ring-white/5">
                      <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                        <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 py-2 px-4 text-white">Bots.io:Strategies P0ng.jsx</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">TeslaGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">IBMGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">Self quotes.vue3</div>
                      </div>
                    </div>
                    <div class="px-6 pt-6 pb-4">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Indigo Core","color": "indigo","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon", "route": "/content/2"},
{"name":"Indigo","color": "red","description":"Configure your (customer) ERP with specific API that secures your business significantly.","icon":"WalletIcon", "route": "/content/2"},
{"name":"Indigo 2.0", "color": "blue",
"description":"Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.", "route": "/content/2",
"icon":"FingerPrintIcon"},
{"name":"Indigo Labs Framework","color": "purple","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon", "route": "/content/2"}]',
                'settings' => 'id, title, subtitle,icon, image, html, features, slogan',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 1,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2024-08-23 12:13:13',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 2,
                'title' => 'Process Transactions in Realtime',
                'page_id' => 4,
                'file' => 'TableSection.vue',
                'subtitle' => '<p>Reliable information for projecting future inventory demands is mandatory for todays business. Redesign your API framework to provide a reliable source for all stakeholders with accurate information. Follow our practical guide in order to provide a stable, coherent and composable business resource API.
</p>
<p>
A table of placeholder production  data that does not make any sense. Add your own dynamic data handles  here!
</p>',
                'icon' => '',
                'image' => '',
                'slogan' => 'Benefits of Accurate Forecasting with Improved Revenue Expectations',
                'html' => '<p>
A table of placeholder production  data that does not make any sense. Add your own dynamic data handles  here!
</p>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Indigo Core","color": "indigo","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon", "route": "/content/2"},
{"name":"Indigo","color": "red","description":"Configure your (customer) ERP with specific API that secures your business significantly.","icon":"WalletIcon", "route": "/content/2"},
{"name":"Indigo 2.0", "color": "blue",
"description":"Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.", "route": "/content/2",
"icon":"FingerPrintIcon"},
{"name":"Indigo Labs Framework","color": "purple","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon", "route": "/content/2"}]',
                'settings' => 'id, title, settings, css, subtitle',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 2,
                'created_at' => '2023-11-03 20:02:54',
                'updated_at' => '2024-08-23 12:12:57',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Content Section',
                'page_id' => 3,
                'file' => 'Hero5Section.vue',
                'subtitle' => 'Welcome to the webportal of PANADEROS, your partner for digital innovation and prototyping of the latest digital technologies. Request a demo to experience how to enhance your Web2 Processes with the power of Web3.',
                'icon' => '-',
                'image' => '',
                'slogan' => 'Dive into the Web3 Renaissance where Simulation Art Meets the Digital Age',
                'html' => '<div class="flex bg-gray-800/40 ring-1 ring-white/5">
                      <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                        <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 py-2 px-4 text-white">Bots.io:Strategies P0ng.jsx</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">TeslaGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">IBMGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">Self quotes.vue3</div>
                      </div>
                    </div>
                    <div class="px-6 pt-6 pb-4">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, slogan, html, icon, features',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 3,
                'created_at' => '2023-11-03 20:02:54',
                'updated_at' => '2024-08-23 12:13:33',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 4,
                'title' => 'History',
                'page_id' => 3,
                'file' => 'CalendarSection.vue',
                'subtitle' => '',
                'icon' => '-',
                'image' => '',
                'slogan' => 'check the highlights for panaderos.web2',
                'html' => 'These are the highlights for panaderos.web2',
                'css' => 'col-span-6',
                'features' => '[
  {
    "id": 1,
    "content": "Init Panadero Holding",
    "target": "Panaderos",
    "href": "/content",
    "date": "Nov 18",
    "datetime": "2018-11-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-gray-500"
  },  {
    "id": 2,
    "content": "Finished Indigo2",
    "target": "Panaderos",
    "href": "www.ad.nl",
    "date": "Dec 19",
    "datetime": "2019-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-gray-500"
  },
{
    "id": 2,
    "content": "Init Indigo3",
    "target": "Panaderos",
    "href": "www.ad.nl",
    "date": "Dec 19",
    "datetime": "2019-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-gray-500"
  },

{
    "id": 3,
    "content": "Init Bots.io",
    "target": "Bots.io",
    "href": "www.ad.nl",
    "date": "Dec 19",
    "datetime": "2019-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-green-500"
  }, 

{
    "id": 4,
    "content": "New P0ng Strategies",
    "target": "Bots.io",
    "href": "www.ad.nl",
    "date": "Dec 19",
    "datetime": "2019-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-green-500"
  },
{
    "id": 5,
    "content": "P0ng Cardano32  ",
    "target": "Bots.io",
    "href": "www.ad.nl",
    "date": "Dec 19",
    "datetime": "2019-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-green-500"
  },
{
    "id": 5,
    "content": "P0ng Doge HFT  ",
    "target": "Bots.io",
    "href": "www.ad.nl",
    "date": "Feb 23",
    "datetime": "2023-02-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-green-500"
  },
{
    "id": 6,
    "content": "Launch Indigo3   ",
    "target": "Frontend",
    "href": "www.ad.nl",
    "date": "Feb 23",
    "datetime": "2023-02-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-yellow-500"
  },
{
    "id": 7,
    "content": "Indigo Core   ",
    "target": "Indigo",
    "href": "www.ad.nl",
    "date": "Sep 23",
    "datetime": "2023-02-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-yellow-500"
  },
{
    "id": 8,
    "content": "Launch Webservices ",
    "target": "Web2",
    "href": "www.ad.nl",
    "date": "Jan 23",
    "datetime": "2023-12-24",
    "icon":"CheckIcon",
    "iconBackground": "bg-black"
  }

]',
                'settings' => 'id, title, settings, features, html,, slogan, priority',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 15,
                'created_at' => '2023-11-04 16:48:17',
                'updated_at' => '2023-12-06 19:59:20',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 5,
                'title' => 'Blog',
                'page_id' => 3,
                'file' => 'BlogSection.vue',
                'subtitle' => 'These are some past projects',
                'icon' => '',
                'image' => '',
                'slogan' => 'write something nice herez',
                'html' => '<div class="px-2 pt-2 pb-2">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings',
                'self' => 'col-span-4',
                'self_admin' => 'henrimatisse',
                'priority' => 13,
                'created_at' => '2023-11-04 16:49:48',
                'updated_at' => '2023-12-06 23:20:54',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 6,
                'title' => 'Check Web3 SELF integrations',
                'page_id' => 6,
                'file' => 'Hero6Section.vue',
                'subtitle' => 'This is your chance to become an earlie bird adaptor of the new SELF authorization module and particpate in the programn that changes blockchain experience significantly!',
                'icon' => '',
                'image' => '',
                'slogan' => 'Whatever it takes',
                'html' => null,
                'css' => 'col-span-6',
                'features' => '[{"name":"Decentralized Finance : DeFi","description":" How to pay your customers using alternate routes.","icon":"CloudArrowUpIcon"},{"name":"NFT Earning","description":"Enhancing your (customer) ERP with specific NFT Ownerships to increase your productivity.","icon":"WalletIcon"},{"name":"Company Identity Tokens","description":"How to introduce your product and service in a new dimension.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'title, self,self_admin, subtitle , priority,, features, slogan',
                'self' => 'superman',
                'self_admin' => 'killer, henrymatisse',
                'priority' => 1,
                'created_at' => '2023-11-08 15:31:46',
                'updated_at' => '2023-11-30 17:53:34',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 1,
            ],
            [
                'id' => 7,
                'title' => '<h2 class="text-3xl text-black font-bold  sm:text-5xl">Get in Touch!    </h2>',
                'page_id' => 3,
                'file' => 'JumboSection2.vue',
                'subtitle' => '<div class="ml-12">
          <h2 class="text-xl text-red-700 font-bold tracking-tight">And let\'s connect Today!</h2>
          <p class="mt-4 text-lg leading-8 text-gray-800 dark:text-gray-100">Request a demo to find out how Indigo3 boost your operational efficiency and exceed your customer\'s expectations</p>',
                'icon' => '',
                'image' => '',
                'slogan' => 'And meet our development team',
                'html' => '<div class="px-2 pt-2 pb-2">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, subtitle,slogan, priority, html',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 9,
                'created_at' => '2023-11-08 23:15:23',
                'updated_at' => '2023-12-05 23:14:02',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 8,
                'title' => '@SELF Partnership',
                'page_id' => 6,
                'file' => 'SelfSection.vue',
                'subtitle' => 'This is your chance to become an early bird adaptor of the new SELF authorization module and particpate in the programn that changes blockchain experience significantly!',
                'icon' => '',
                'image' => '',
                'slogan' => 'The fine art of using NFT as a SELF identity.. : Reserve your Name!',
                'html' => '.',
                'css' => 'col-span-6 bg-indigo-100 dark:bg-gray-900 bg-dots',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'title, html, subtitle',
                'self' => 'superman',
                'self_admin' => 'killer, henrymatisse',
                'priority' => 4,
                'created_at' => '2023-11-11 14:19:53',
                'updated_at' => '2023-11-30 17:06:49',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 1,
            ],
            [
                'id' => 9,
                'title' => 'AI Business Software',
                'page_id' => 27,
                'file' => 'WelcomeSection.vue',
                'subtitle' => 'Welcome Eric .. start here...  Types of Business Software Every Company Needs
<p class ="mt-4">
From small-scale start-up organisations through to global corporates, software is a vital part of day-to-day operations. Increased collaboration and oversight, minimises repetitive tasks, streamlines operations and ensures accountability across the boar</p>',
                'icon' => 'ApplicationLogo',
                'image' => '',
                'slogan' => 'We just love clean sheets',
                'html' => '<hr />
<span class=\'text-xs dark:text-indigo-400 text-indigo-600\'>cleanDesign - richOptions - userFriendly - coreFunctions - innovative - modular - connected  - scalable - web3Secure - richOptions
</span>',
                'css' => 'col-span-6 bg-white dark:bg-slate-950',
                'features' => '[
{"name":"Web Applications 2.0", "color": "green",
"description":"Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.", "route": "/home/dashboard",
"icon":"I3FrameworkIcon"},

{"name":"Process & Logistic Control","color": "indigo","description":"Enpowering your Process and Logistic business with the version3  of our proven framework.","icon":"CloudArrowUpIcon", "route": "/design/lane"},

{"name":"Project Planning","color": "red","description":"Management requires tools, and techniques to plan, execute, monitor, and complete projects within their timeframes.","icon":"ResourcePlanningIcon", "route": "/project/work"},

{"name":"Content Management","color": "red","description":"Alter your Company Presentation while sipping your coffee.","icon":"ContentManagementIcon", "route": "/content/posts"},

{"name":"Web3 Innovations","color": "purple","description":"Smart Contracts and DAPP`s for self governing and alternative financial solutions.","icon":"LockClosedIcon", "route": "/web3"},

{"name":"DESIGN Mind","color": "purple","description":"Brainstorm with your team using one of our mind maps for a project? Start with a brilliant idea!","icon":"ProjectIcon", "route": "/design/mind"}
]',
                'settings' => 'id,slogan, css, features, self_admin, subtitle, icon, html',
                'self' => 'public, henrimatisse, killer, leonardo',
                'self_admin' => 'jawsome, killer, henrimatisse, leonardo',
                'priority' => 2,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2025-06-03 01:28:28',
                'is_active' => 1,
                'self_auth' => 0,
                'animate' => 0,
            ],
            [
                'id' => 10,
                'title' => 'Header',
                'page_id' => 0,
                'file' => 'HeaderSection',
                'subtitle' => 'Welcome.. subtitle not used',
                'icon' => '',
                'image' => '',
                'slogan' => '',
                'html' => 'whatever text',
                'css' => 'col-span-6 bg-gray-50 dark:bg-gray-950 shadow-sm
border-gray-300 dark:border-gray-700',
                'features' => '[{"name":"Process & Logistic Control","color": "indigo","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon", "route": "/content/2"},
{"name":"Cyber Security","color": "red","description":"Configure your (customer) ERP with specific API that secures your business significantly.","icon":"WalletIcon", "route": "/content/2"},
{"name":"Web Applications 2.0", "color": "blue",
"description":"Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.", "route": "/content/2",
"icon":"FingerPrintIcon"},
{"name":"Web3 Innovations","color": "purple","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon", "route": "/content/2"}]',
                'settings' => 'id  ,css, file, title, self, self_admin',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 1,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2025-05-20 20:55:09',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 11,
                'title' => 'DemoSection WebApplications',
                'page_id' => 3,
                'file' => 'HHeroSection.vue',
                'subtitle' => 'Where cutting-edge technology meets innovative design. We\'re thrilled to present our latest demo, showcasing the powerful Web2 applications we can create using our Indigo-Series development framework. Whether you\'re looking to build a responsive web application, a dynamic e-commerce platform, or a robust logistic management system, our Indigo-Series has you covered.',
                'icon' => 'Logo',
                'image' => '',
                'slogan' => 'Upgrade with responsive and realtime api\'s',
                'html' => '<div class="flex bg-gray-800/40 ring-1 ring-white/5">
                      <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                        <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 py-2 px-4 text-white">Bots.io:Strategies P0ng.jsx</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">TeslaGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">IBMGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">Self quotes.vue3</div>
                      </div>
                    </div>
                    <div class="px-6 pt-6 pb-4">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Content Management","color": "indigo","description":"Advanced content management systems (CMS) are the backbone of modern digital platforms, enabling businesses to efficiently manage, publish, and optimize content across multiple devices. With a focus on responsive design, these systems ensure that content is seamlessly accessible and visually appealing on smartphones, tablets, and desktops alike. In the ICT landscape, an advanced CMS combines powerful features like drag-and-drop editing, multi-user collaboration, and robust security, all within a flexible framework that adapts to the evolving needs of your business. This responsiveness not only enhances user experience but also boosts engagement and SEO performance, making it an essential tool for any digital strategy..","icon":"CloudArrowUpIcon", "route": "/content/2"},
{"name":"API Connectors","color": "red","description":"API connectors play a crucial role in modern web applications by enabling seamless communication between different software systems. These connectors act as bridges, allowing applications to exchange data, trigger functions, and integrate with third-party services like payment gateways, CRMs, or social media platforms. By using API connectors, web applications can extend their functionality, enhance automation, and provide a more dynamic user experience. Whether it\'s real-time data updates, integrating complex workflows, or connecting to cloud services, APIs empower developers to build scalable, modular, and highly flexible web solutions.","icon":"WalletIcon", "route": "/content/2"},
{"name":"Dashboard Applications", "color": "blue",
"description":"Innovative KPI dashboards revolutionize the way businesses track and analyze performance by providing real-time, visually engaging insights into key metrics. These dashboards are highly customizable, allowing users to monitor data that aligns with specific business goals, from sales and marketing performance to operational efficiency. With dynamic visualizations like charts, graphs, and heatmaps, they offer an intuitive, at-a-glance overview of critical data. By integrating with various data sources, innovative KPI dashboards empower teams to make informed decisions quickly, optimize processes, and drive strategic growth, all while fostering a data-driven culture..", "route": "/public/1",
"icon":"FingerPrintIcon"},
{"name":"AI Innovations","color": "purple","description":"Integrating AI into web applications elevates functionality and user experience by enabling features like personalization, automation, and intelligent data analysis. AI-powered web apps can provide real-time recommendations, streamline customer service with chatbots, and enhance decision-making through predictive analytics. By learning from user behavior and data patterns, AI allows web applications to adapt dynamically, offering more tailored experiences. From automating routine tasks to enhancing security with advanced algorithms, adding AI to web apps opens up new possibilities for innovation and scalability, driving efficiency and improving engagement..","icon":"LockClosedIcon", "route": "/content/2"}]',
                'settings' => 'id, title, subtitle,icon, image, html, features, slogan',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 1,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2024-09-06 18:46:11',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 12,
                'title' => 'P0ng Autotrading Section',
                'page_id' => 3,
                'file' => 'DemoBotsSection.vue',
                'subtitle' => '<p>Reliable information for projecting future inventory demands is mandatory for todays business. Redesign your API framework to provide a reliable source for all stakeholders with accurate information. Follow our practical guide in order to provide a stable, coherent and composable business resource API.
</p>
<p>
A table of placeholder production  data that does not make any sense. Add your own dynamic data handles  here!you tot
</p>',
                'icon' => '',
                'image' => '',
                'slogan' => 'Benefits of Accurate Forecasting with Improved Revenue Expectations',
                'html' => '<p>
A table of placeholder production  data that does not make any sense. Add your own dynamic data handles  here!
</p>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","color": "indigo","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon", "route": "/content/2"},
{"name":"Cyber Security","color": "red","description":"Configure your (customer) ERP with specific API that secures your business significantly.","icon":"WalletIcon", "route": "/content/2"},
{"name":"Web Applications 2.0", "color": "blue",
"description":"Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.", "route": "/content/2",
"icon":"FingerPrintIcon"},
{"name":"Web3 Innovations","color": "purple","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon", "route": "/content/2"}]',
                'settings' => 'id, title, settings',
                'self' => 'col-span-6',
                'self_admin' => 'henrimatisse',
                'priority' => 2,
                'created_at' => '2023-11-03 20:02:54',
                'updated_at' => '2024-08-21 22:05:07',
                'is_active' => 0,
                'self_auth' => 0,
                'animate' => 0,
            ],
            [
                'id' => 13,
                'title' => 'Article',
                'page_id' => 26,
                'file' => 'ArticleSection.vue',
                'subtitle' => 'These are some past projects',
                'icon' => '',
                'image' => '',
                'slogan' => 'write something nice here',
                'html' => '<div class="px-2 pt-2 pb-2">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6  bg-slate-100 dark:bg-indigo-dark-800',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, subtitle, slogan, css',
                'self' => 'col-span-4',
                'self_admin' => 'henrimatisse',
                'priority' => 13,
                'created_at' => '2023-11-04 16:49:48',
                'updated_at' => '2024-08-30 15:56:46',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 14,
                'title' => 'Category',
                'page_id' => 27,
                'file' => 'CategorySection.vue',
                'subtitle' => 'These are some past projects',
                'icon' => '',
                'image' => '',
                'slogan' => 'write something nice here',
                'html' => '<div class="px-2 pt-2 pb-2">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, subtitle, slogan, css',
                'self' => 'col-span-4',
                'self_admin' => 'henrimatisse',
                'priority' => 13,
                'created_at' => '2023-11-04 16:49:48',
                'updated_at' => '2023-12-09 20:51:42',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 15,
                'title' => 'Footer..has no title!',
                'page_id' => 0,
                'file' => 'FooterSection.vue',
                'subtitle' => 'Welcome.. subtitle not used',
                'icon' => '',
                'image' => '',
                'slogan' => 'Try something out of the box....',
                'html' => '<div class="flex bg-gray-800/40 ring-1 ring-white/5">
                      <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                        <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 py-2 px-4 text-white">Bots.io:Strategies P0ng.jsx</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">TeslaGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">IBMGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">Self quotes.vue3</div>
                      </div>
                    </div>
                    <div class="px-6 pt-6 pb-4">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6 bg-slate-100 dark:bg-black bg-dots',
                'features' => '{ "Services":  
	[
		{ "language": "EN", "name": "Web2", "href": "/public/105" }, 
		{ "language": "NL", "name": "Web2NL", "href": "/public/105" }, 
		{ "language": "EN", "name": "Process", "href": "/public/106" },
		{ "language": "EN", "name": "Security", "href": "/public/106" },
		{ "language": "EN", "name": "Web3", "href": "/public/106" }
	] ,
"Support" : 
	[
		{ "language": "EN", "name": "Pricing", "href": "/public/111" }, 
		{ "language": "EN", "name": "Documentation", "href": "/public/111" }, 
		{ "language": "EN", "name": "Guides", "href": "/public/111" }, 
		{ "language": "EN", "name": "AI Apps", "href": "/public/3" }, 
		{ "language": "EN", "name": "API Status", "href": "/public/107" }
	
	] ,
"Company" : 
	[
		{ "language": "EN", "name": "About", "href": "/public/111" }, 
		{ "language": "EN", "name": "Blog", "href": "/public/111" }, 
		{ "language": "EN", "name": "Jobs", "href": "/public/111" }, 
		{ "language": "EN", "name": "Press", "href": "/public/107" },
	{ "language": "EN", "name": "Partners", "href": "/public/107" }
	
	] ,
"Legal" : 
	[
		{ "language": "EN", "name": "Claim", "href": "/public/111" }, 
		{ "language": "EN", "name": "Privacy", "href": "/public/119"}, 
		{ "language": "EN", "name": "Terms", "href": "/public/111" }, 
		{ "language": "EN", "name": "Disclaimer", "href": "/public/107" }	
	]
}',
                'settings' => 'id, file, title, self, self_admin, features,css',
                'self' => 'killer',
                'self_admin' => 'henrimatisse',
                'priority' => 99,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2024-09-04 17:21:50',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 16,
                'title' => 'SubHeader',
                'page_id' => 0,
                'file' => 'SubHeaderSection',
                'subtitle' => 'Header2 Section',
                'icon' => '',
                'image' => '',
                'slogan' => 'Try something out of the box....',
                'html' => '<div class="flex bg-gray-800/40 ring-1 ring-white/5">
                      <div class="-mb-px flex text-sm font-medium leading-6 text-gray-400">
                        <div class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 py-2 px-4 text-white">Bots.io:Strategies P0ng.jsx</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">TeslaGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">IBMGrid.vue3</div>
                        <div class="border-r border-gray-600/10 py-2 px-4">Self quotes.vue3</div>
                      </div>
                    </div>
                    <div class="px-6 pt-6 pb-4">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6  bg-slate-100 dark:bg-indigo-dark-800',
                'features' => '[
  { "name": "Home", "href": "welcome", "icon": "home" },
  { "name": "Dashboard", "href": "dashboard", "icon": "cake"}
]',
                'settings' => 'id, file, title, self, self_admin, features,css',
                'self' => 'killer',
                'self_admin' => 'henrimatisse',
                'priority' => 0,
                'created_at' => '2023-11-01 17:01:55',
                'updated_at' => '2025-05-19 21:08:56',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 17,
                'title' => 'Records',
                'page_id' => 30,
                'file' => 'RecordsSection.vue',
                'subtitle' => 'These are some past ideas!',
                'icon' => '',
                'image' => '',
                'slogan' => 'write something nice here',
                'html' => '<div class="px-2 pt-2 pb-2">

<!-- Your code example -->
                      <pre>
                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">
                          <span class="text-purple-400">token.name</span>
                        /**
                         * Stop polling
                         * <span class="text-purple-400">@param</span> {Object} [options] Options
                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request
                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling
                         * <span class="text-purple-400">@return</span> {Promise} // rejected!
                         */</p>
                        <p class="text-white text-sm -mt-16">
                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {
                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();
                            }
                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;
                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;
                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);
                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {
                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;
                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);
                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;
                            }
                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;
                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {
                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;
                            });
                          }</p></code></pre>

    </div>',
                'css' => 'col-span-6',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, subtitle, slogan, css',
                'self' => 'col-span-4',
                'self_admin' => 'henrimatisse',
                'priority' => 13,
                'created_at' => '2023-11-04 16:49:48',
                'updated_at' => '2024-08-06 11:22:57',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 0,
            ],
            [
                'id' => 18,
                'title' => 'RecordsDuppi2',
                'page_id' => 31,
                'file' => 'RecordsSection.vue',
                'subtitle' => 'These 4are some past projects',
                'icon' => 'icon4',
                'image' => 'img4',
                'slogan' => 'write4 something nice here you 222',
                'html' => '<div class="px-4 pt-2 pb-2"><!-- Your code example -->                      <pre>                        <code class=\'text-indigo-400 \'><p class="text-gray-400 -mt-16 text-sm">                          <span class="text-purple-400">token.name</span>                        /**                         * Stop polling                         * <span class="text-purple-400">@param</span> {Object} [options] Options                         * <span class="text-purple-400">@param</span> {Boolean} [options.cancel] Cancel current request                         * <span class="text-purple-400">@param</span> {String} [options.reason] Reason for stopping polling                         * <span class="text-purple-400">@return</span> {Promise} // rejected!                         */</p>                        <p class="text-white text-sm -mt-16">                          <span class="text-rose-400">stop</span>(<span class="text-yellow-400">options</span> = {}) {                            <span class="text-purple-400">if</span> (!<span class="text-rose-400">this</span>._lastRequest) {                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve</span>();                            }                            <span class="text-purple-400">const</span> <span class="text-rose-400">lastSELFcall</span> = <span class="text-rose-400">this</span>._lastRequest;                            <span class="text-rose-400">this</span>._lastRequest = <span class="text-green-500">null</span>;                            <span class="text-blue-400">clearTimeout</span>(<span class="text-rose-400">this</span>._pollingTimeout);                            <span class="text-rose-400">if</span> (<span class="text-rose-400">options</span>.cancel) {                              <span class="text-purple-400">const</span> <span class="text-rose-400">reason</span> = <span class="text-rose-400">options</span>.reason <span class="text-blue-400">|| \'Polling stop\'</span>;                              <span class="text-rose-400">lastRequest</span>.cancel(<span class="text-rose-400">reason</span>);                              <span class="text-purple-400">return</span> <span class="text-green-500">Promise</span>.<span class="text-blue-400">resolve()</span>;                            }                            <span class="text-rose-400">this</span>._abort = <span class="text-green-500">true</span>;                            <span class="text-purple-400">return</span> <span class="text-rose-400">lastRequest</span>.finally(() => {                              <span class="text-rose-400">this</span>._abort = <span class="text-green-500">false</span>;                            });                          }</p></code></pre>    </div>',
                'css' => 'col-span-4',
                'features' => '[{"name":"Process & Logistic Control","description":"Enpowering your Process and Logistic business with proven frameworks solutions direct out of our libraries.","icon":"CloudArrowUpIcon"},{"name":"ERP Interactions","description":"Enhancing your (customer) ERP with specific API that optimizes your workflows significantly.","icon":"WalletIcon"},{"name":"Single Page Applications For you","description":"Blazing fast Web Applications with performance identical to native apps.","icon":"FingerPrintIcon"},{"name":"Web3 Innovations","description":"Smart Contracts and DAPP`s for self governing or ownerships you probably never heard off.","icon":"LockClosedIcon"}]',
                'settings' => 'id, title, settings, subtitle, slogan, css2',
                'self' => 'col-span-4',
                'self_admin' => 'henrimatisse',
                'priority' => 133,
                'created_at' => '2023-11-04 16:49:48',
                'updated_at' => '2024-08-06 11:22:30',
                'is_active' => 1,
                'self_auth' => 1,
                'animate' => 1,
            ],
        ];

        foreach ($sections as $data) {
            if (!isset($data['html']) || $data['html'] === null) {
                $data['html'] = '';
            }
            Section::updateOrCreate(['id' => $data['id']], $data);
        }
    }
} 