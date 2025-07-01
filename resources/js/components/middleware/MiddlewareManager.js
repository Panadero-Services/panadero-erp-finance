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

        console.log('MiddlewareManager: Initialized middleware:', Array.from(this.registry.keys()));
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
}

// Create and export a single instance
export const middlewareManager = new MiddlewareManager(); 