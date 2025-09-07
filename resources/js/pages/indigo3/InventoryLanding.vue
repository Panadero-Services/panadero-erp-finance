<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Animation states
const isVisible = ref({
  hero: false,
  features: false,
  modules: false,
  agents: false,
  timeline: false,
  cta: false
});

// AI Agent state
const aiMessages = ref([]);
const userInput = ref('');
const isAiTyping = ref(false);
const selectedProvider = ref('openai');
const aiConfig = ref({
  provider: 'openai',
  apiKey: '',
  model: 'gpt-4',
  temperature: 0.7,
  maxTokens: 1000
});

// Features data
const features = [
  {
    icon: '📦',
    title: 'Real-time Inventory Tracking',
    description: 'Monitor stock levels across multiple warehouses with live updates and automated alerts.'
  },
  {
    icon: '🤖',
    title: 'AI-Powered Insights',
    description: 'Get intelligent recommendations for stock optimization, demand forecasting, and supplier management.'
  },
  {
    icon: '📊',
    title: 'Advanced Analytics',
    description: 'Comprehensive reporting and analytics to make data-driven inventory decisions.'
  },
  {
    icon: '🔄',
    title: 'Automated Workflows',
    description: 'Streamline purchase orders, stock movements, and supplier communications with smart automation.'
  },
  {
    icon: '🏢',
    title: 'Multi-Warehouse Management',
    description: 'Manage inventory across multiple locations with centralized control and distributed visibility.'
  },
  {
    icon: '🔗',
    title: 'Seamless Integration',
    description: 'Connect with existing ERP, accounting, and e-commerce systems through our modular architecture.'
  }
];

const modules = [
  {
    name: 'Stock Management',
    description: 'Complete inventory tracking and control',
    features: ['Real-time stock levels', 'Automated reorder points', 'Batch tracking', 'Expiry management']
  },
  {
    name: 'Warehouse Management',
    description: 'Multi-location warehouse operations',
    features: ['Location management', 'Pick & pack optimization', 'Cycle counting', 'Space utilization']
  },
  {
    name: 'Purchase Orders',
    description: 'Streamlined procurement processes',
    features: ['Automated PO generation', 'Supplier management', 'Approval workflows', 'Delivery tracking']
  },
  {
    name: 'Supplier Management',
    description: 'Comprehensive supplier relationship management',
    features: ['Supplier profiles', 'Performance tracking', 'Contract management', 'Communication logs']
  },
  {
    name: 'Reporting & Analytics',
    description: 'Data-driven inventory insights',
    features: ['Custom reports', 'Trend analysis', 'Cost optimization', 'Performance metrics']
  },
  {
    name: 'AI Agent Portal',
    description: 'Intelligent automation and insights',
    features: ['Natural language queries', 'Predictive analytics', 'Automated recommendations', 'Custom AI models']
  }
];

const timeline = [
  {
    year: '2015',
    title: 'Indigo2 Launch',
    description: 'First generation modular ERP system with agent architecture foundation',
    status: 'completed'
  },
  {
    year: '2018',
    title: 'Agent Framework',
    description: 'Advanced AI agent integration for automated business processes',
    status: 'completed'
  },
  {
    year: '2021',
    title: 'Cloud Migration',
    description: 'Full cloud deployment with enhanced scalability and security',
    status: 'completed'
  },
  {
    year: '2024',
    title: 'Indigo3 Development',
    description: 'Next-generation modular architecture with enhanced AI capabilities',
    status: 'in-progress'
  },
  {
    year: '2025',
    title: 'Indigo3 Launch',
    description: 'Revolutionary ERP platform with advanced AI agents and modular design',
    status: 'upcoming'
  }
];

// Animation functions
const observeElements = () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const section = entry.target.dataset.section;
        if (section) {
          isVisible.value[section] = true;
        }
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('[data-section]').forEach(el => {
    observer.observe(el);
  });
};

// AI Agent functions
const sendAIMessage = async () => {
  if (!userInput.value.trim()) return;
  
  const userMessage = userInput.value;
  userInput.value = '';
  aiMessages.value.push({ type: 'user', content: userMessage });
  
  isAiTyping.value = true;
  
  try {
    const response = await axios.post('/api/ai/call', {
      provider: aiConfig.value.provider,
      config: aiConfig.value,
      prompt: userMessage,
      context: 'inventory_management'
    });
    
    aiMessages.value.push({ 
      type: 'ai', 
      content: response.data.response || 'I apologize, but I encountered an error processing your request.'
    });
  } catch (error) {
    aiMessages.value.push({ 
      type: 'ai', 
      content: 'I apologize, but I\'m currently unable to process your request. Please try again later.'
    });
  } finally {
    isAiTyping.value = false;
  }
};

const saveAIConfig = () => {
  localStorage.setItem('indigo3_ai_config', JSON.stringify(aiConfig.value));
};

const loadAIConfig = () => {
  const saved = localStorage.getItem('indigo3_ai_config');
  if (saved) {
    aiConfig.value = { ...aiConfig.value, ...JSON.parse(saved) };
  }
};

onMounted(() => {
  observeElements();
  loadAIConfig();
});
</script>

<template>
  
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white">
    <!-- Hero Section -->
    <section 
      data-section="hero" 
      class="relative min-h-screen flex items-center justify-center overflow-hidden"
      :class="{ 'animate-fade-in': isVisible.hero }"
    >
      <!-- Background Animation -->
      <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20"></div>
      <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
      </div>
      
      <div class="relative z-10 text-center max-w-6xl mx-auto px-6">
        <div class="mb-8">
          <div class="inline-flex items-center px-4 py-2 bg-indigo-500/20 rounded-full border border-indigo-400/30 mb-6">
            <span class="text-indigo-300 text-sm font-medium">Indigo3 - Coming 2025</span>
          </div>
          <h1 class="text-6xl md:text-8xl font-bold mb-6 bg-gradient-to-r from-white to-indigo-200 bg-clip-text text-transparent">
            Inventory Management
          </h1>
          <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
            Revolutionary ERP inventory management powered by AI agents. 
            Built on 10 years of modular architecture innovation.
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
            Request Demo
          </button>
          <button class="px-8 py-4 border border-indigo-400 hover:bg-indigo-500/20 rounded-lg font-semibold transition-all duration-300">
            Learn More
          </button>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section 
      data-section="features" 
      class="py-20 px-6"
      :class="{ 'animate-fade-in': isVisible.features }"
    >
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">Why Choose Indigo3?</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            Experience the next generation of inventory management with AI-powered insights and modular architecture.
          </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="(feature, index) in features" 
            :key="index"
            class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10 hover:border-indigo-400/50 transition-all duration-300 hover:transform hover:scale-105"
          >
            <div class="text-4xl mb-4">{{ feature.icon }}</div>
            <h3 class="text-xl font-semibold mb-3">{{ feature.title }}</h3>
            <p class="text-gray-300">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Modules Section -->
    <section 
      data-section="modules" 
      class="py-20 px-6 bg-black/20"
      :class="{ 'animate-fade-in': isVisible.modules }"
    >
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">Modular Architecture</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            Scalable, maintainable, and innovative modules that provide competitive advantages.
          </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="(module, index) in modules" 
            :key="index"
            class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-xl p-8 border border-indigo-400/20 hover:border-indigo-400/50 transition-all duration-300"
          >
            <h3 class="text-2xl font-bold mb-3 text-indigo-300">{{ module.name }}</h3>
            <p class="text-gray-300 mb-4">{{ module.description }}</p>
            <ul class="space-y-2">
              <li v-for="feature in module.features" :key="feature" class="flex items-center text-sm text-gray-400">
                <span class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></span>
                {{ feature }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- AI Agent Portal Section -->
    <section 
      data-section="agents" 
      class="py-20 px-6"
      :class="{ 'animate-fade-in': isVisible.agents }"
    >
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">AI Agent Portal</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            Interact with our intelligent AI agents to get insights, recommendations, and automated assistance for your inventory management.
          </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-12">
          <!-- AI Configuration -->
          <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
            <h3 class="text-2xl font-bold mb-6 text-indigo-300">Configure Your AI Agent</h3>
            
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium mb-2">AI Provider</label>
                <select v-model="aiConfig.provider" @change="saveAIConfig" class="w-full p-3 bg-white/10 border border-white/20 rounded-lg text-white">
                  <option value="openai">OpenAI GPT-4</option>
                  <option value="claude">Anthropic Claude</option>
                  <option value="ollama">Ollama (Local)</option>
                  <option value="custom">Custom API</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium mb-2">API Key</label>
                <input 
                  v-model="aiConfig.apiKey" 
                  @input="saveAIConfig"
                  type="password" 
                  placeholder="Enter your API key"
                  class="w-full p-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400"
                >
              </div>
              
              <div>
                <label class="block text-sm font-medium mb-2">Model</label>
                <select v-model="aiConfig.model" @change="saveAIConfig" class="w-full p-3 bg-white/10 border border-white/20 rounded-lg text-white">
                  <option value="gpt-4">GPT-4</option>
                  <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                  <option value="claude-3-sonnet">Claude 3 Sonnet</option>
                  <option value="claude-3-haiku">Claude 3 Haiku</option>
                </select>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Temperature</label>
                  <input 
                    v-model.number="aiConfig.temperature" 
                    @input="saveAIConfig"
                    type="range" 
                    min="0" 
                    max="1" 
                    step="0.1"
                    class="w-full"
                  >
                  <span class="text-sm text-gray-400">{{ aiConfig.temperature }}</span>
                </div>
                
                <div>
                  <label class="block text-sm font-medium mb-2">Max Tokens</label>
                  <input 
                    v-model.number="aiConfig.maxTokens" 
                    @input="saveAIConfig"
                    type="number" 
                    min="100" 
                    max="4000"
                    class="w-full p-2 bg-white/10 border border-white/20 rounded text-white"
                  >
                </div>
              </div>
            </div>
          </div>
          
          <!-- AI Chat Interface -->
          <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
            <h3 class="text-2xl font-bold mb-6 text-indigo-300">Chat with AI Agent</h3>
            
            <!-- Messages -->
            <div class="h-80 overflow-y-auto mb-4 space-y-4">
              <div v-if="aiMessages.length === 0" class="text-center text-gray-400 py-8">
                Start a conversation with your AI inventory assistant...
              </div>
              
              <div 
                v-for="(message, index) in aiMessages" 
                :key="index"
                class="flex"
                :class="message.type === 'user' ? 'justify-end' : 'justify-start'"
              >
                <div 
                  class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg"
                  :class="message.type === 'user' 
                    ? 'bg-indigo-600 text-white' 
                    : 'bg-white/10 text-gray-200'"
                >
                  {{ message.content }}
                </div>
              </div>
              
              <div v-if="isAiTyping" class="flex justify-start">
                <div class="bg-white/10 text-gray-200 px-4 py-2 rounded-lg">
                  <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-100"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-200"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Input -->
            <div class="flex space-x-2">
              <input 
                v-model="userInput"
                @keyup.enter="sendAIMessage"
                placeholder="Ask about inventory management..."
                class="flex-1 p-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-indigo-400"
                :disabled="isAiTyping"
              >
              <button 
                @click="sendAIMessage"
                :disabled="!userInput.trim() || isAiTyping"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-600 disabled:cursor-not-allowed rounded-lg font-semibold transition-colors"
              >
                Send
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Timeline Section -->
    <section 
      data-section="timeline" 
      class="py-20 px-6 bg-black/20"
      :class="{ 'animate-fade-in': isVisible.timeline }"
    >
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">10 Years of Innovation</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            From Indigo2 to Indigo3 - A decade of modular architecture and AI agent development.
          </p>
        </div>
        
        <div class="relative">
          <!-- Timeline line -->
          <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>
          
          <div class="space-y-12">
            <div 
              v-for="(item, index) in timeline" 
              :key="index"
              class="relative flex items-center"
              :class="index % 2 === 0 ? 'flex-row' : 'flex-row-reverse'"
            >
              <div class="w-1/2 px-8">
                <div 
                  class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10"
                  :class="item.status === 'completed' ? 'border-green-400/50' : item.status === 'in-progress' ? 'border-yellow-400/50' : 'border-gray-400/50'"
                >
                  <div class="flex items-center mb-2">
                    <span 
                      class="text-2xl font-bold mr-3"
                      :class="item.status === 'completed' ? 'text-green-400' : item.status === 'in-progress' ? 'text-yellow-400' : 'text-gray-400'"
                    >
                      {{ item.year }}
                    </span>
                    <span 
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="item.status === 'completed' ? 'bg-green-500/20 text-green-300' : item.status === 'in-progress' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-gray-500/20 text-gray-300'"
                    >
                      {{ item.status.replace('-', ' ').toUpperCase() }}
                    </span>
                  </div>
                  <h3 class="text-xl font-semibold mb-2">{{ item.title }}</h3>
                  <p class="text-gray-300">{{ item.description }}</p>
                </div>
              </div>
              
              <!-- Timeline dot -->
              <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full border-4 border-slate-900"
                   :class="item.status === 'completed' ? 'bg-green-400' : item.status === 'in-progress' ? 'bg-yellow-400' : 'bg-gray-400'">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section 
      data-section="cta" 
      class="py-20 px-6"
      :class="{ 'animate-fade-in': isVisible.cta }"
    >
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Transform Your Inventory Management?</h2>
        <p class="text-xl text-gray-300 mb-8">
          Join the next generation of ERP with Indigo3. Experience the power of AI agents and modular architecture.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
            Request Early Access
          </button>
          <button class="px-8 py-4 border border-indigo-400 hover:bg-indigo-500/20 rounded-lg font-semibold transition-all duration-300">
            Schedule Demo
          </button>
        </div>
        
        <div class="mt-12 text-center">
          <p class="text-gray-400 mb-4">Built on 10 years of innovation</p>
          <div class="flex justify-center items-center space-x-8 text-2xl font-bold">
            <span class="text-indigo-300">Indigo2</span>
            <span class="text-gray-500">→</span>
            <span class="text-purple-300">Indigo3</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom scrollbar for chat */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(99, 102, 241, 0.5);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(99, 102, 241, 0.7);
}
</style>
