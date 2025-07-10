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
  featuredPosts: {
    data: Post[];
  };
}

defineProps<Props>();

const _set = useSettingsStore();
const _contract = useContractStore();

// Animation states (keep the same structure)
const isVisible = ref<VisibilityState>({
  hero: false,
  stats: false,
  features: false,
  testimonials: false,
  contact: false,
  aiFeatures: false,
  enterpriseModules: false
});

// Logistics-specific features
const features = [
  {
    title: 'Real-time Fleet Management',
    description: 'Track and optimize your fleet operations with real-time GPS tracking, route optimization, and vehicle analytics.',
    icon: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
    color: 'text-blue-500',
    gradient: 'from-blue-500 to-blue-600'
  },
  {
    title: 'Warehouse Management',
    description: 'Optimize inventory placement, picking routes, and storage utilization with AI-powered warehouse management.',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    color: 'text-green-500',
    gradient: 'from-green-500 to-green-600'
  },
  {
    title: 'Supply Chain Visibility',
    description: 'End-to-end visibility of your supply chain with real-time tracking and predictive analytics.',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    color: 'text-purple-500',
    gradient: 'from-purple-500 to-purple-600'
  },
  {
    title: 'Smart Inventory Control',
    description: 'AI-driven inventory management with predictive restocking and demand forecasting.',
    icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    color: 'text-orange-500',
    gradient: 'from-orange-500 to-orange-600'
  },
  {
    title: 'Digital Documentation',
    description: 'Paperless operations with smart contracts and blockchain-verified documentation.',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    color: 'text-indigo-500',
    gradient: 'from-indigo-500 to-indigo-600'
  },
  {
    title: 'Analytics & Reporting',
    description: 'Comprehensive analytics dashboard with KPIs, performance metrics, and custom reports.',
    icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    color: 'text-pink-500',
    gradient: 'from-pink-500 to-pink-600'
  }
];

// Logistics-specific stats
const stats = [
  { label: 'Shipments Processed', value: '1M+', icon: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' },
  { label: 'Route Optimization', value: '30%↑', icon: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' },
  { label: 'Delivery Success Rate', value: '99.8%', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' },
  { label: 'Cost Reduction', value: '25%↓', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }
];

// Logistics-specific testimonials
const testimonials = [
  {
    content: "The real-time fleet management system has revolutionized our delivery operations. We've seen a 30% increase in efficiency.",
    author: "David Chen",
    role: "Operations Director, Global Logistics Co.",
    image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  },
  {
    content: "The warehouse management system has eliminated picking errors and reduced our fulfillment time by 40%.",
    author: "Sarah Thompson",
    role: "Warehouse Manager, FastTrack Logistics",
    image: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  },
  {
    content: "The supply chain visibility tools have helped us identify and resolve bottlenecks before they impact operations.",
    author: "Michael Rodriguez",
    role: "Supply Chain Director, Express Delivery",
    image: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
  }
];

// Logistics-specific AI features
const aiFeatures = [
  {
    title: 'Predictive Analytics',
    description: 'AI-powered demand forecasting and route optimization for efficient logistics operations.',
    icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    gradient: 'from-purple-500 to-indigo-600',
    items: [
      'Demand forecasting',
      'Route optimization',
      'Capacity planning',
      'Resource allocation',
      'Performance prediction'
    ]
  },
  {
    title: 'Smart Documentation',
    description: 'Blockchain-verified documentation and smart contracts for secure logistics operations.',
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    gradient: 'from-blue-500 to-cyan-600',
    items: [
      'Digital BOLs',
      'Smart contracts',
      'Automated compliance',
      'Document verification',
      'Audit trails'
    ]
  },
  {
    title: 'IoT Integration',
    description: 'Real-time tracking and monitoring through IoT devices and sensors.',
    icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
    gradient: 'from-green-500 to-emerald-600',
    items: [
      'GPS tracking',
      'Temperature monitoring',
      'Asset tracking',
      'Condition monitoring',
      'Predictive maintenance'
    ]
  }
];

// Keep the rest of the script section the same...
</script>

<template>
  <!-- Enterprise Hero Section -->
  <div class="relative bg-gray-900 dark:bg-black">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-dots-darker dark:bg-dots-lighter opacity-20"></div>

    <!-- Content Container -->
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-24">
      <!-- Main Heading -->
      <div class="text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white dark:text-gray-100">
          INDIGO1 Enterprise Hub
        </h1>
        <p class="mt-6 text-lg text-gray-300 dark:text-gray-400 max-w-3xl mx-auto">
          Trusted enterprise logistics platform powering multinational operations since 2006
        </p>
      </div>

      <!-- Feature Grid -->
      <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Order Management -->
        <div class="bg-gray-800/50 dark:bg-gray-800 backdrop-blur-sm rounded-lg p-6 border border-gray-700">
          <h3 class="text-xl font-semibold text-white mb-3">Order Management</h3>
          <p class="text-gray-400">Complete order lifecycle tracking with real-time status updates</p>
        </div>

        <!-- Storage & Inventory -->
        <div class="bg-gray-800/50 dark:bg-gray-800 backdrop-blur-sm rounded-lg p-6 border border-gray-700">
          <h3 class="text-xl font-semibold text-white mb-3">Storage & Inventory</h3>
          <p class="text-gray-400">Real-time inventory tracking and quality control</p>
        </div>

        <!-- Partner Network -->
        <div class="bg-gray-800/50 dark:bg-gray-800 backdrop-blur-sm rounded-lg p-6 border border-gray-700">
          <h3 class="text-xl font-semibold text-white mb-3">Partner Network</h3>
          <p class="text-gray-400">Integrated management of customers, suppliers, and transport</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Section -->
  <div class="relative bg-white dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="stat in stats" :key="stat.label" 
             class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
              {{ stat.label }}
            </dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
              {{ stat.value }}
            </dd>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Features Grid -->
  <div class="relative bg-gray-50 dark:bg-gray-800 py-16 sm:py-24 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
          Comprehensive Logistics Solutions
        </h2>
        <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-300">
          Everything you need to manage your logistics operations efficiently
        </p>
      </div>
      
      <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="feature in features" :key="feature.title" 
             class="relative group bg-white dark:bg-gray-900 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
          <div>
            <span :class="['inline-flex p-3 ring-4 ring-white dark:ring-gray-900 rounded-lg', feature.color]">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
              </svg>
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              {{ feature.title }}
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Testimonials -->
  <div class="relative bg-white dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
          Trusted by Industry Leaders
        </h2>
      </div>
      <div class="mt-12 grid gap-8 lg:grid-cols-3">
        <div v-for="testimonial in testimonials" :key="testimonial.author" 
             class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
          <p class="text-gray-500 dark:text-gray-300">{{ testimonial.content }}</p>
          <div class="mt-6 flex items-center">
            <img class="h-12 w-12 rounded-full" :src="testimonial.image" :alt="testimonial.author">
            <div class="ml-4">
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ testimonial.author }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ testimonial.role }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Features -->
  <div class="relative bg-gray-50 dark:bg-gray-800 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
          AI-Powered Logistics
        </h2>
        <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-300">
          Next-generation solutions for modern logistics challenges
        </p>
      </div>
      
      <div class="mt-12 grid gap-8 lg:grid-cols-3">
        <div v-for="feature in aiFeatures" :key="feature.title" 
             class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
          <div class="p-6">
            <div class="flex items-center">
              <div :class="['bg-gradient-to-r p-3 rounded-md', feature.gradient]">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                </svg>
              </div>
              <h3 class="ml-4 text-lg font-medium text-gray-900 dark:text-white">{{ feature.title }}</h3>
            </div>
            <p class="mt-4 text-base text-gray-500 dark:text-gray-300">{{ feature.description }}</p>
          </div>
          <div class="px-6 py-4">
            <ul class="space-y-3">
              <li v-for="item in feature.items" :key="item" 
                  class="flex items-start">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-300">{{ item }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA Section -->
  <div class="relative bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800">
    <div class="max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
      <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
        <span class="block">Ready to transform your logistics?</span>
        <span class="block">Start your journey today.</span>
      </h2>
      <div class="mt-8 flex justify-center">
        <div class="inline-flex rounded-md shadow">
          <a href="#" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
            Get started
          </a>
        </div>
        <div class="ml-3 inline-flex">
          <a href="#" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-600">
            Learn more
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Inside the Hero Section, after the buttons div -->
  <div class="mt-16 grid grid-cols-3 gap-8 max-w-3xl">
    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
      <div class="text-3xl font-bold text-white">30%</div>
      <div class="text-sm text-gray-300">Cost Reduction</div>
    </div>
    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
      <div class="text-3xl font-bold text-white">24/7</div>
      <div class="text-sm text-gray-300">Real-time Tracking</div>
    </div>
    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
      <div class="text-3xl font-bold text-white">99.9%</div>
      <div class="text-sm text-gray-300">Delivery Success</div>
    </div>
  </div>

  <!-- After the hero section -->
  <div class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
      <!-- Section Title -->
      <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-12">
        Enterprise-Grade Capabilities
      </h2>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
        <!-- Order Processing -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Order Management</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Real-time tracking, document handling (CMR), and automated workflows</p>
          </div>
        </div>

        <!-- Storage Management -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Storage Control</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Inventory levels, quality parameters, storage location tracking</p>
          </div>
        </div>

        <!-- Product Management -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Product Specifications</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Product groups, mixing ratios, quality parameters (purity, density)</p>
          </div>
        </div>

        <!-- Transport Management -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Transport Coordination</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Forwarder management, shipment tracking, weight control</p>
          </div>
        </div>

        <!-- Multi-tenant -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Multi-tenant Design</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Secure client isolation, role-based access, customizable workflows</p>
          </div>
        </div>

        <!-- International -->
        <div class="flex gap-3">
          <div class="text-indigo-600 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">International Operations</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Multi-language support, country-specific documentation, global partner network</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 