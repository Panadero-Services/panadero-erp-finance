<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';

const _set = useSettingsStore();
const _contract = useContractStore();

// Form state
const formData = ref({
  name: '',
  email: '',
  company: '',
  message: '',
  service: ''
});

const isSubmitting = ref(false);
const formSuccess = ref(false);
const formError = ref('');

// Features data
const features = [
  {
    title: 'Web Applications 2.0',
    description: 'Blazing Fast Dashboard Applications and API integrations with performance identical to native apps.',
    icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    color: 'text-blue-500',
    gradient: 'from-blue-500 to-blue-600'
  },
  {
    title: 'Process & Logistic Control',
    description: 'Empowering your Process and Logistic business with the version3 of our proven framework.',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    color: 'text-green-500',
    gradient: 'from-green-500 to-green-600'
  },
  {
    title: 'Project Planning',
    description: 'Management requires tools, and techniques to plan, execute, monitor, and complete projects within their timeframes.',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    color: 'text-purple-500',
    gradient: 'from-purple-500 to-purple-600'
  },
  {
    title: 'Content Management',
    description: 'Alter your Company Presentation while sipping your coffee.',
    icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    color: 'text-orange-500',
    gradient: 'from-orange-500 to-orange-600'
  },
  {
    title: 'Web3 Innovations',
    description: 'Smart Contracts and DAPP`s for self governing and alternative financial solutions.',
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    color: 'text-indigo-500',
    gradient: 'from-indigo-500 to-indigo-600'
  },
  {
    title: 'DESIGN Mind',
    description: 'Brainstorm with your team using one of our mind maps for a project? Start with a brilliant idea!',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    color: 'text-pink-500',
    gradient: 'from-pink-500 to-pink-600'
  }
];

// Stats data
const stats = [
  { label: 'Active Users', value: '10K+', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
  { label: 'Projects Completed', value: '500+', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { label: 'Client Satisfaction', value: '98%', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' },
  { label: 'Years Experience', value: '15+', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }
];

// Testimonials data
const testimonials = [
  {
    content: "The Web Applications 2.0 platform has transformed how we handle our business operations. The performance is incredible!",
    author: "Sarah Johnson",
    role: "CTO, TechCorp",
    image: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  },
  {
    content: "The Process & Logistic Control system has streamlined our operations and reduced costs by 30%. Highly recommended!",
    author: "Michael Chen",
    role: "Operations Director, Global Logistics",
    image: "https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  },
  {
    content: "The Project Planning tools have made collaboration across our global teams seamless and efficient.",
    author: "Emma Rodriguez",
    role: "Project Manager, International Solutions",
    image: "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  }
];

// Animation states
const isVisible = ref({
  hero: false,
  stats: false,
  features: false,
  testimonials: false,
  contact: false,
  aiFeatures: false,
  enterpriseModules: false
});

// Intersection Observer setup
onMounted(() => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        isVisible.value[entry.target.id] = true;
      }
    });
  }, { threshold: 0.1 });

  // Observe all sections
  Object.keys(isVisible.value).forEach(key => {
    const element = document.getElementById(key);
    if (element) observer.observe(element);
  });
});

// Form submission
const handleSubmit = async () => {
  isSubmitting.value = true;
  formError.value = '';
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    formSuccess.value = true;
    formData.value = {
      name: '',
      email: '',
      company: '',
      message: '',
      service: ''
    };
  } catch (error) {
    formError.value = 'An error occurred. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

// Service options for contact form
const serviceOptions = [
  { value: 'web-apps', label: 'Web Applications 2.0' },
  { value: 'process-logistics', label: 'Process & Logistic Control' },
  { value: 'project-planning', label: 'Project Planning' },
  { value: 'content-management', label: 'Content Management' },
  { value: 'web3', label: 'Web3 Innovations' },
  { value: 'design-mind', label: 'DESIGN Mind' }
];

// Add new features data
const aiFeatures = [
  {
    title: 'AI & Machine Learning',
    description: 'Predictive analytics, intelligent forecasting, and smart anomaly detection powered by advanced AI algorithms.',
    icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    gradient: 'from-purple-500 to-indigo-600',
    items: [
      'Predictive analytics for demand and inventory',
      'Intelligent forecasting and planning',
      'Smart anomaly detection',
      'AI-powered chatbots and support',
      'Machine learning optimization'
    ]
  },
  {
    title: 'Web3 & Blockchain',
    description: 'Smart contracts, decentralized identity, and tokenized assets for next-generation business operations.',
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    gradient: 'from-blue-500 to-cyan-600',
    items: [
      'Smart contracts for automation',
      'On-chain audit trails',
      'Decentralized identity management',
      'Tokenized assets and loyalty',
      'DAO integration'
    ]
  },
  {
    title: 'Cloud-Native & Edge',
    description: 'Scalable, serverless architecture with real-time data syncing and offline-first capabilities.',
    icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
    gradient: 'from-green-500 to-emerald-600',
    items: [
      'Serverless architecture',
      'Real-time data syncing',
      'Offline-first functionality',
      'Microservices architecture',
      'API-first design'
    ]
  }
];

const enterpriseModules = [
  {
    title: 'Smart Finance',
    features: ['Real-time liquidity analysis', 'AI auditing', 'Blockchain reconciliations']
  },
  {
    title: 'Smart HR',
    features: ['AI-driven talent matching', 'Dynamic org charts', 'Sentiment analysis']
  },
  {
    title: 'Smart Manufacturing',
    features: ['AI scheduling', 'Predictive maintenance', 'IIoT integration']
  },
  {
    title: 'Smart Sales & CRM',
    features: ['AI-driven lead scoring', 'Dynamic pricing models', 'Predictive analytics']
  }
];
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
    <!-- Hero Section with Animated Background -->
    <div id="hero" class="relative overflow-hidden" :class="{ 'animate-fade-in': isVisible.hero }">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="absolute inset-0 opacity-30 dark:opacity-20">
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(120,119,198,0.3),rgba(255,255,255,0))]"></div>
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_0%_0%,rgba(120,119,198,0.3),rgba(255,255,255,0))]"></div>
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_100%_0%,rgba(120,119,198,0.3),rgba(255,255,255,0))]"></div>
        </div>
        <!-- Animated Mesh -->
        <div class="absolute inset-0 opacity-20 dark:opacity-10">
          <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        </div>
        <!-- Floating Elements -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -top-40 -left-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
          <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto relative">
        <div class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
          <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
            <div class="sm:text-center lg:text-left">
              <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                <span class="block transform transition-all duration-500 hover:scale-105">Types of Business Software</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 transform transition-all duration-500 hover:scale-105">Every Company Needs</span>
              </h1>
              <p class="mt-3 text-base text-gray-500 dark:text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                From small-scale start-up organisations through to global corporates, software is a vital part of day-to-day operations. When implemented correctly, software allows for increased collaboration and oversight, minimises repetitive tasks, streamlines operations and ensures accountability across the board.
              </p>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 md:py-4 md:text-lg md:px-10 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    Explore Solutions
                  </a>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <a href="#contact" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    Contact Us
                  </a>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div id="stats" class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 pt-12 sm:pt-16" :class="{ 'animate-fade-in': isVisible.stats }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
            Trusted by businesses worldwidezzz
          </h2>
        </div>
      </div>
      <div class="mt-10 pb-12 sm:pb-16">
        <div class="relative">
          <div class="absolute inset-0 h-1/2 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900"></div>
          <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
              <dl class="rounded-lg bg-white dark:bg-gray-800 shadow-lg sm:grid sm:grid-cols-4 transform transition-all duration-500 hover:scale-105">
                <div v-for="stat in stats" :key="stat.label" class="flex flex-col border-b border-gray-100 dark:border-gray-700 p-6 text-center sm:border-0 sm:border-r">
                  <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500 dark:text-gray-400">
                    {{ stat.label }}
                  </dt>
                  <dd class="order-1 text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">
                    {{ stat.value }}
                  </dd>
                  <div class="mt-4">
                    <svg class="h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                    </svg>
                  </div>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800" :class="{ 'animate-fade-in': isVisible.features }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Features</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            A better way to manage your business
          </p>
          <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
            Everything you need to take your business to the next level.
          </p>
        </div>

        <div class="mt-10">
          <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
            <div v-for="feature in features" :key="feature.title" 
                 class="relative transform transition-all duration-500 hover:scale-105">
              <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r" :class="feature.gradient">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                </svg>
              </div>
              <div class="ml-16">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ feature.title }}</h3>
                <p class="mt-2 text-base text-gray-500 dark:text-gray-300">{{ feature.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Features Section -->
    <div id="ai-features" class="py-12 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900" :class="{ 'animate-fade-in': isVisible.aiFeatures }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Next-Gen Features</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            AI-Powered Enterprise Solutionszz
          </p>
          <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
            Harness the power of artificial intelligence and blockchain technology
          </p>
        </div>

        <div class="mt-10">
          <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
            <div v-for="feature in aiFeatures" :key="feature.title" 
                 class="relative transform transition-all duration-500 hover:scale-105">
              <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r" :class="feature.gradient">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                </svg>
              </div>
              <div class="ml-16">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ feature.title }}</h3>
                <p class="mt-2 text-base text-gray-500 dark:text-gray-300">{{ feature.description }}</p>
                <ul class="mt-4 space-y-2">
                  <li v-for="item in feature.items" :key="item" class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ item }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enterprise Modules Section -->
    <div id="enterprise-modules" class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800" :class="{ 'animate-fade-in': isVisible.enterpriseModules }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Enterprise Modules</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            Smart Enterprise Solutions
          </p>
        </div>

        <div class="mt-10">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div v-for="module in enterpriseModules" :key="module.title" 
                 class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transform transition-all duration-500 hover:scale-105">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ module.title }}</h3>
              <ul class="space-y-3">
                <li v-for="feature in module.features" :key="feature" class="flex items-start">
                  <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ feature }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Testimonials Section -->
    <div id="testimonials" class="py-12 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900" :class="{ 'animate-fade-in': isVisible.testimonials }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Testimonials</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            What our clients say
          </p>
        </div>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
          <div v-for="testimonial in testimonials" :key="testimonial.author" 
               class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transform transition-all duration-500 hover:scale-105">
            <div class="flex items-center mb-4">
              <img class="h-12 w-12 rounded-full" :src="testimonial.image" :alt="testimonial.author">
              <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ testimonial.author }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ testimonial.role }}</p>
              </div>
            </div>
            <p class="text-gray-600 dark:text-gray-300">{{ testimonial.content }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Form Section -->
    <div id="contact" class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800" :class="{ 'animate-fade-in': isVisible.contact }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Contact Us</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            Get in touch
          </p>
          <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
            Ready to transform your business? Let's talk about your needs.
          </p>
        </div>

        <div class="mt-12 max-w-lg mx-auto">
          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
              <input type="text" id="name" v-model="formData.name" required
                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
              <input type="email" id="email" v-model="formData.email" required
                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
              <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
              <input type="text" id="company" v-model="formData.company"
                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
              <label for="service" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Interested In</label>
              <select id="service" v-model="formData.service" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Select a service</option>
                <option v-for="option in serviceOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
              <textarea id="message" v-model="formData.message" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div>
              <button type="submit" 
                      :disabled="isSubmitting"
                      class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105">
                <span v-if="isSubmitting">Sending...</span>
                <span v-else>Send Message</span>
              </button>
            </div>

            <div v-if="formSuccess" class="rounded-md bg-green-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800">
                    Thank you for your message! We'll get back to you soon.
                  </p>
                </div>
              </div>
            </div>

            <div v-if="formError" class="rounded-md bg-red-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800">
                    {{ formError }}
                  </p>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
          <span class="block">Ready to get started?</span>
          <span class="block">Start your free trial today.</span>
        </h2>
        <p class="mt-4 text-lg leading-6 text-blue-100">
          Join thousands of businesses that trust our software solutions.
        </p>
        <a href="#contact" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto transition-all duration-300 transform hover:scale-105">
          Sign up for free
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Add smooth scrolling to the whole page */
html {
  scroll-behavior: smooth;
}

/* Add hover effects to all interactive elements */
a, button {
  transition: all 0.3s ease;
}

/* Add gradient text effect */
.gradient-text {
  background: linear-gradient(to right, #3B82F6, #6366F1);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Add new animation for module cards */
.module-card {
  transition: all 0.3s ease;
}

.module-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Add gradient animation */
.gradient-animate {
  background-size: 200% 200%;
  animation: gradient 15s ease infinite;
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

/* Add new animations */
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Add hover effects */
.hover-lift {
  transition: transform 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
}

/* Add glass effect */
.glass-effect {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Add shimmer effect */
.shimmer {
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.2) 50%,
    transparent 100%
  );
  background-size: 200% 100%;
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}
</style> 