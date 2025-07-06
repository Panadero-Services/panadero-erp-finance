import { AuthenticationMiddleware } from './AuthenticationMiddleware';
import { AuthorizationMiddleware } from '@/components/middleware/AuthorizationMiddleware';
import { RequestValidationMiddleware } from '@/components/middleware/RequestValidationMiddleware';
import { middlewareRegistry } from '@/components/middleware/Registry';

export class MiddlewareManager {
    constructor() {
        this.registry = new Map();
        
        // Register all middleware with proper names
        this.registry.set('Authentication', new AuthenticationMiddleware());
        this.registry.set('Authorization', new AuthorizationMiddleware());
        this.registry.set('Request', new RequestValidationMiddleware());
        this.registry.set('Request2', new RequestValidationMiddleware());

        console.log('MiddlewareManager: Initialized middleware:', Array.from(this.registry.keys()));
        
        // Store singleton instance
        MiddlewareManager.instance = this;
    }

    // Initialize the middleware system (only once)
    init() {
        if (this.isInitialized) {
            console.log('MiddlewareManager: Already initialized, skipping...');
            return;
        }
        
        this.initCount++;
        console.log(`MiddlewareManager: Initializing (attempt ${this.initCount})`);
        
        // Start session checking for auth middleware
        const authMiddleware = this.registry.get('Authentication');
        if (authMiddleware && authMiddleware.startSessionCheck) {
            authMiddleware.startSessionCheck();
        }
        
        this.isInitialized = true;
        console.log('Middleware system initialized successfully');
    }

    register(middleware) {
        //this.registry.set(middleware.name, middleware);
    }

    async processRequest(request) {
        console.log('MiddlewareManager: Processing request with registry size:', this.registry.size);
        const results = [];
        
        // Process only registered middleware
        for (const [name, middleware] of this.registry) {
            console.log(`MiddlewareManager: Processing ${name} middleware`);
            const result = await middleware.handle(request);
            results.push({ 
                middleware: name,  // Ensure middleware name is included
                result 
            });
            console.log(`MiddlewareManager: ${name} result:`, result);
        }

        console.log('MiddlewareManager: All results:', results);
        return results;
    }

    getMiddlewareChain() {
        return Array.from(this.registry.values());
    }

    setMiddlewareActive(name, active) {
        const middleware = this.registry.get(name);
        if (middleware) {
            middleware.active = active;
        }
    }

    registerMiddleware(name, middleware, order, color) {
        middleware.order = order;
        middleware.color = color;
        this.register(middleware);
    }

    unregisterMiddleware(name) {
        this.registry.delete(name);
    }

    // New convenience methods
    getAuth() {
        return this.registry.get('Authentication');
    }

    getPermissions() {
        return this.registry.get('Authorization');
    }

    checkAuth() {
        const authMiddleware = this.getAuth();
        return authMiddleware ? authMiddleware.getChecks({}) : { userValid: false };
    }

    // Cleanup method
    cleanup() {
        const authMiddleware = this.getAuth();
        if (authMiddleware && authMiddleware.cleanup) {
            authMiddleware.cleanup();
        }
        this.isInitialized = false;
    }
}

// Create and export a single instance
export const middlewareManager = new MiddlewareManager(); 