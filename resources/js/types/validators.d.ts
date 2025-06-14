declare module '@/utils/validators' {
    export function validateField(value: any, rules: any): boolean;
    export function parseRules(rules: string): any;
} 