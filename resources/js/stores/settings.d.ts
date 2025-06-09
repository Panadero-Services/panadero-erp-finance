declare module '@/stores/settings' {
  export interface SettingsStore {
    // Add your settings store properties and methods here
    [key: string]: any;
  }

  export function useSettingsStore(): SettingsStore;
} 