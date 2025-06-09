declare module '@/stores/contracts' {
  export interface ContractStore {
    // Add your contracts store properties and methods here
    [key: string]: any;
  }

  export function useContractStore(): ContractStore;
} 