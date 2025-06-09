<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

interface Category {
  id: number;
  name: string;
}

interface Tag {
  id: number;
  name: string;
}

interface Post {
  id: number;
  title: string;
  body: string;
  content: string;
  slug: string;
  created_at: string;
  category?: Category;
  tags: Tag[];
  user: {
    name: string;
  };
}

interface Props {
  page: any;
  baseSections: any[];
  featuredPosts: Post[];
}

const props = defineProps<Props>();

const _set = useSettingsStore();
const _contract = useContractStore();

type VisibilityState = {
  [key: string]: boolean;
  hero: boolean;
  stats: boolean;
  features: boolean;
  testimonials: boolean;
  contact: boolean;
  aiFeatures: boolean;
  enterpriseModules: boolean;
};

// Animation states
const isVisible = ref<VisibilityState>({
  hero: false,
  stats: false,
  features: false,
  testimonials: false,
  contact: false,
  aiFeatures: false,
  enterpriseModules: false
});

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

// AI chat state
const aiMessages = ref<Array<{ type: 'user' | 'ai'; content: string }>>([]);
const userInput = ref('');
const isAiTyping = ref(false);
const relevantPosts = ref<Post[]>([]);
const selectedCategory = ref<Category | null>(null);
const selectedTags = ref<number[]>([]);
const errorMessage = ref('');

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
    title: 'Cloud-Native Bots',
    description: 'Automated Workflows that live in the cloud with real-time data syncing and offline-first capabilities.',
    icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
    gradient: 'from-green-500 to-emerald-600',
    items: [
      'API-Providers for inputs',
      'Processors with continuous improvements',
      'Executors for outputs',
      'Real-time data syncing',
      'Microservices architecture',
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

// Add business functions data
const businessFunctions = [
  { name: 'Accounting', icon: 'fas fa-calculator' },
  { name: 'CMS', icon: 'fas fa-file-alt' },
  { name: 'Ecommerce', icon: 'fas fa-shopping-cart' },
  { name: 'Inventory Forecasting', icon: 'fas fa-boxes' },
  { name: 'Process Management', icon: 'fas fa-comments' },
  { name: 'Social Media', icon: 'fas fa-share-alt' },
  { name: 'Project Management', icon: 'fas fa-tasks' },
  { name: 'AI Agents', icon: 'fas fa-robot' },
  { name: 'ERP', icon: 'fas fa-cogs' },
  { name: 'Team Collaboration', icon: 'fas fa-users' }
];

// Computed properties for filtered posts
const availableCategories = computed(() => {
  const categories = new Set<Category>();
  relevantPosts.value.forEach(post => {
    if (post.category) categories.add(post.category);
  });
  return Array.from(categories);
});

const availableTags = computed(() => {
  const tags = new Set<Tag>();
  relevantPosts.value.forEach(post => {
    post.tags.forEach(tag => tags.add(tag));
  });
  return Array.from(tags);
});

const filteredPosts = computed(() => {
  return relevantPosts.value.filter(post => {
    const matchesCategory = !selectedCategory.value || 
      (post.category && post.category.id === selectedCategory.value.id);
    const matchesTags = selectedTags.value.length === 0 || 
      post.tags.some(tag => selectedTags.value.includes(tag.id));
    return matchesCategory && matchesTags;
  });
});

// Methods for post interaction
const viewFullPost = (post: Post) => {
  router.visit(`/posts/${post.slug}`);
};

const toggleTag = (tagId: number) => {
  const index = selectedTags.value.indexOf(tagId);
  if (index === -1) {
    selectedTags.value.push(tagId);
  } else {
    selectedTags.value.splice(index, 1);
  }
};

const clearFilters = () => {
  selectedCategory.value = null;
  selectedTags.value = [];
};

const aiCapabilities = [
  { title: 'Natural Language Processing', icon: 'fas fa-comments', description: 'Advanced understanding and processing of human language' },
  { title: 'Smart Automation', icon: 'fas fa-robot', description: 'Automate repetitive tasks and workflows' },
  { title: 'Data Analysis', icon: 'fas fa-chart-line', description: 'Real-time insights and predictive analytics' },
  { title: '24/7 Support', icon: 'fas fa-clock', description: 'Round-the-clock assistance and monitoring' }
];

// AI Response Generation
const generateAIResponse = async (userMessage: string) => {
  try {
    errorMessage.value = ''; // Clear any previous errors
    const response = await axios.post('/api/ai/chat', {
      message: userMessage,
      context: aiMessages.value.slice(-5).map(m => ({
        role: m.type === 'user' ? 'user' : 'assistant',
        content: m.content
      }))
    });

    // Update relevant posts if available
    if (response.data.relevant_posts) {
      relevantPosts.value = response.data.relevant_posts;
    }

    return response.data.message;
  } catch (error: any) {
    console.error('AI Response Error:', error);
    errorMessage.value = error.response?.data?.message || 'Failed to get response from AI. Please try again.';
    return "I apologize, but I'm having trouble processing your request right now. Please try again in a moment.";
  }
};

const sendMessage = async () => {
  if (!userInput.value.trim()) return;
  
  // Add user message
  aiMessages.value.push({ type: 'user', content: userInput.value });
  const currentInput = userInput.value;
  userInput.value = '';
  
  // Show typing indicator
  isAiTyping.value = true;
  
  try {
    // Generate AI response
    const aiResponse = await generateAIResponse(currentInput);
    
    // Add AI response to messages
    aiMessages.value.push({ 
      type: 'ai', 
      content: aiResponse
    });
  } catch (error) {
    console.error('Error in sendMessage:', error);
    aiMessages.value.push({ 
      type: 'ai', 
      content: "I apologize, but I encountered an error. Please try again."
    });
  } finally {
    isAiTyping.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
    <!-- Hero Section -->
    <div class="relative bg-white dark:bg-gray-900 overflow-hidden">
      <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white dark:bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
          <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
            <div class="sm:text-center lg:text-left">
              <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                <span class="block xl:inline">Transform Your Business with</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 xl:inline"> AI-Powered Solutions</span>
              </h1>
              <p class="mt-3 text-base text-gray-500 dark:text-gray-300 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                Streamline operations, enhance productivity, and drive growth with our comprehensive suite of AI and blockchain-powered tools.
              </p>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 md:py-4 md:text-lg md:px-10">
                    Get Started
                  </a>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <a href="#contact" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 md:py-4 md:text-lg md:px-10">
                    Contact Us
                  </a>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
      <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="/images/hero-image.jpg" alt="AI and Blockchain Technology">
      </div>
    </div>
           {{featuredPosts.data}}

    <!-- Featured Posts Section -->
    <div v-if="featuredPosts && featuredPosts.length > 0" class="py-12 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-8">
          <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 font-semibold tracking-wide uppercase">Featured</h2>
          <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
            Latest Updates            
          </p>
        </div>

        <div class="mt-10">
          <div v-for="post in featuredPosts.data" :key="post.id" 
               class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transform transition-all duration-500 hover:scale-105">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <span v-if="post.category" 
                        class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ post.category.name }}
                  </span>
                  <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ new Date(post.created_at).toLocaleDateString() }}
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500 dark:text-gray-400">
                    By {{ post.user.name }}
                  </span>
                </div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ post.title }}
              </h3>
              <p class="text-gray-600 dark:text-gray-300 line-clamp-3">
                {{ post.body }}
              </p>
              <div class="mt-4">
                <button @click="router.visit(`/posts/${post.slug}`)"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  Read More
                  <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </button>
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
            AI-Powered Enterprise Solutionsz
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

    <!-- AI Agent Section (Moved to second layer) -->
    <div class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
            <span class="block">Meet Your AI Assistant</span>
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">Powered by Advanced AI</span>
          </h2>
          <p class="mt-4 text-lg text-gray-500 dark:text-gray-300">
            Experience the future of business automation with our intelligent AI agent
          </p>
        </div>

        <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-start">
          <!-- Left Column: AI Chat Interface -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center">
                  <i class="fas fa-robot text-white"></i>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">AI Assistant</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Online and ready to help</p>
                </div>
              </div>
            </div>

            <!-- Chat Messages -->
            <div class="h-96 overflow-y-auto p-6 space-y-4" ref="chatContainer">
              <div v-for="(message, index) in aiMessages" :key="index" 
                   :class="[
                     'flex',
                     message.type === 'user' ? 'justify-end' : 'justify-start'
                   ]">
                <div :class="[
                  'max-w-[80%] rounded-2xl px-4 py-2',
                  message.type === 'user' 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
                ]">
                  <div class="whitespace-pre-line">{{ message.content }}</div>
                </div>
              </div>
              <div v-if="isAiTyping" class="flex justify-start">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-2">
                  <div class="flex space-x-2">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                  </div>
                </div>
              </div>

              <!-- Error Message -->
              <div v-if="errorMessage" 
                   class="flex justify-start">
                <div class="max-w-[80%] rounded-2xl px-4 py-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                  <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ errorMessage }}</span>
                  </div>
                </div>
              </div>

              <!-- Relevant Posts Section -->
              <div v-if="relevantPosts.length > 0" class="mt-4">
                <div class="flex items-center justify-between mb-4">
                  <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Relevant Information
                  </div>
                  <button @click="clearFilters" 
                          class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    Clear Filters
                  </button>
                </div>

                <!-- Filters -->
                <div class="mb-4 space-y-3">
                  <!-- Category Filter -->
                  <div v-if="availableCategories.length > 0">
                    <label class="text-xs text-gray-500 dark:text-gray-400 mb-1 block">Categories</label>
                    <div class="flex flex-wrap gap-2">
                      <button v-for="category in availableCategories" 
                              :key="category.id"
                              @click="selectedCategory = selectedCategory?.id === category.id ? null : category"
                              :class="[
                                'px-2 py-1 rounded-full text-xs',
                                selectedCategory?.id === category.id
                                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                  : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                              ]">
                        {{ category.name }}
                      </button>
                    </div>
                  </div>

                  <!-- Tags Filter -->
                  <div v-if="availableTags.length > 0">
                    <label class="text-xs text-gray-500 dark:text-gray-400 mb-1 block">Tags</label>
                    <div class="flex flex-wrap gap-2">
                      <button v-for="tag in availableTags" 
                              :key="tag.id"
                              @click="toggleTag(tag.id)"
                              :class="[
                                'px-2 py-1 rounded-full text-xs',
                                selectedTags.includes(tag.id)
                                  ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
                                  : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                              ]">
                        {{ tag.name }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Posts List -->
                <div class="space-y-3">
                  <div v-for="post in filteredPosts" 
                       :key="post.id"
                       class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                    <div class="flex justify-between items-start">
                      <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-1">{{ post.title }}</h4>
                        <div class="flex items-center space-x-2 mb-2">
                          <span v-if="post.category" 
                                class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ post.category.name }}
                          </span>
                          <span v-for="tag in post.tags" 
                                :key="tag.id"
                                class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ tag.name }}
                          </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ post.content }}</p>
                      </div>
                      <button @click="viewFullPost(post)"
                              class="ml-4 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        <i class="fas fa-external-link-alt"></i>
                      </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                      {{ new Date(post.created_at).toLocaleDateString() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
              <form @submit.prevent="sendMessage" class="flex space-x-4">
                <input type="text" 
                       v-model="userInput"
                       placeholder="Type your message..."
                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                <button type="submit" 
                        :disabled="isAiTyping"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                  <i class="fas fa-paper-plane mr-2"></i>
                  Sendd
                </button>
              </form>
            </div>
          </div>

          <!-- Right Column: AI Capabilities -->
          <div class="mt-8 lg:mt-0">
            <div class="grid grid-cols-1 gap-6">
              <div v-for="(capability, index) in aiCapabilities" :key="index"
                   class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center">
                      <i :class="[capability.icon, 'text-white text-xl']"></i>
                    </div>
                  </div>
                  <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ capability.title }}</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">{{ capability.description }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- AI Stats -->
            <div class="mt-8 grid grid-cols-2 gap-4">
              <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">99.9%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Uptime</div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">24/7</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Availability</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div id="stats" class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 pt-12 sm:pt-16" :class="{ 'animate-fade-in': isVisible.stats }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
            Trusted by businesses worldwidez
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

    <!-- CTA Section -->
    <div id="cta" class="bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-start">
          <!-- Left Column: CTA -->
          <div class="lg:pr-8">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
              <span class="block">Ready to get started?</span>
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">Start your free trial today.</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-gray-500 dark:text-gray-300">
              Join thousands of companies that trust our software solutions to power their business operations.
            </p>
            <div class="mt-8 flex">
              <div class="inline-flex rounded-md shadow">
                <a href="#" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700">
                  Get started
                </a>
              </div>
              <div class="ml-3 inline-flex">
                <a href="#" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10 transition-all duration-300 transform hover:scale-105">
                  Learn more
                </a>
              </div>
            </div>
          </div>

          <!-- Right Column: Contact Form -->
          <div class="mt-8 lg:mt-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
              <!-- Decorative elements -->
              <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 opacity-50"></div>
              <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-blue-500 opacity-10 blur-2xl"></div>
              <div class="absolute bottom-0 left-0 -mb-4 -ml-4 h-24 w-24 rounded-full bg-indigo-500 opacity-10 blur-2xl"></div>
              
              <!-- Form content -->
              <div class="relative p-8">
                <div class="flex items-center justify-between mb-8">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Contact Us</h3>
                  <div class="flex space-x-2">
                    <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                    <div class="h-3 w-3 rounded-full bg-indigo-500"></div>
                    <div class="h-3 w-3 rounded-full bg-purple-500"></div>
                  </div>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="relative">
                      <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                      <input type="text" id="name" v-model="formData.name" required
                             class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                    </div>

                    <div class="relative">
                      <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                      <input type="email" id="email" v-model="formData.email" required
                             class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  <div class="relative">
                    <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                    <input type="text" id="company" v-model="formData.company"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <i class="fas fa-building text-gray-400"></i>
                    </div>
                  </div>

                  <div class="relative">
                    <label for="service" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Interested In</label>
                    <select id="service" v-model="formData.service" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                      <option value="">Select a service</option>
                      <option v-for="option in serviceOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                  </div>

                  <div class="relative">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                    <textarea id="message" v-model="formData.message" rows="3" required
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"></textarea>
                  </div>

                  <div>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-[1.02]">
                      <span v-if="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                      </span>
                      <span v-else>Send Message</span>
                    </button>
                  </div>

                  <div v-if="formSuccess" class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4">
                    <div class="flex">
                      <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                          {{ formSuccess }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <div v-if="formError" class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4">
                    <div class="flex">
                      <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                          {{ formError }}
                        </p>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
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
  background-clip: text;
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

/* Add new styles for business functions */
.group:hover .group-hover\:from-blue-500\/5 {
  --tw-gradient-from: rgb(59 130 246 / 0.05);
  --tw-gradient-to: rgb(59 130 246 / 0.1);
}

.group:hover .group-hover\:to-blue-500\/10 {
  --tw-gradient-to: rgb(59 130 246 / 0.1);
}

/* Add new styles for AI section */
.animate-bounce {
  animation: bounce 1s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-25%);
  }
}

/* Add smooth scrolling to chat container */
.overflow-y-auto {
  scroll-behavior: smooth;
}

/* Add styles for relevant posts */
.line-clamp-2 {
  display: -webkit-box;
  -line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Add transition for hover effects */
.transition-shadow {
  transition: box-shadow 0.2s ease-in-out;
}
</style> 