                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <!-- Field Selection Button -->
                        <div class="relative">
                            <button 
                                @click.stop="showFieldPopup = !showFieldPopup"
                                class="px-3 py-1.5 text-xs rounded-md ring-1 ring-inset text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 hover:text-gray-700 dark:hover:ring-indigo-400 transition-colors"
                            >
                                Fields: {{ selectedFields.length }} selected
                            </button>
                            
                            <!-- Field Selection Popup -->
                            <div v-if="showFieldPopup" @click.stop class="absolute bottom-full mb-2 left-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg p-2 z-50 min-w-[150px]">
                                <div class="space-y-1">
                                    <div v-for="field in searchFields" :key="field.value" class="flex items-center px-2 py-1">
                                        <input 
                                            :id="`field-${field.value}`"
                                            v-model="selectedFields"
                                            :value="field.value"
                                            type="checkbox"
                                            :disabled="isSearching"
                                            class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded disabled:opacity-50"
                                        />
                                        <label 
                                            :for="`field-${field.value}`" 
                                            class="ml-2 text-xxs text-gray-700 dark:text-gray-300 cursor-pointer"
                                        >
                                            {{ field.label }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keyboard Shortcuts Info -->
                        <p class="text-xxs text-gray-500 dark:text-gray-400 hidden sm:block">
                            Toggle fields: <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃T</kbd> Title, 
                            <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃C</kbd> Content, 
                            <kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xxs">⌘⌃A</kbd> Author
                        </p>
                    </div>
                </div> 