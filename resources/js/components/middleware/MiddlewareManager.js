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
    }

    register(middleware) {
        //this.registry.set(middleware.name, middleware);
    }

    async processRequest(request) {
        const results = [];
        
        for (const middleware of this.registry.values()) {
            try {
                const result = await this.processMiddleware(middleware, request);
                results.push({
                    middleware: middleware.name,
                    isValid: result.isValid,
                    checks: result.checks || {},
                    validatedData: result.validatedData
                });
            } catch (error) {
                console.error(`Error in ${middleware.name}:`, error);
                results.push({
                    middleware: middleware.name,
                    isValid: false,
                    checks: {},
                    error: error.message
                });
            }
        }

        return results;
    }

    async processMiddleware(middleware, request) {
        // Default implementation - override in specific middleware classes
        return {
            isValid: true,
            checks: {
                'basic-check': true
            },
            validatedData: request
        };
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