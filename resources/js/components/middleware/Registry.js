export class MiddlewareRegistry {
    constructor() {
        this.middlewareMap = new Map();
        this.orderMap = new Map();
        this.colorMap = new Map();
    }

    register(name, middleware, order = 100, color = '#64748b') {
        this.middlewareMap.set(name, {
            instance: middleware,
            active: true
        });
        this.orderMap.set(name, order);
        this.colorMap.set(name, color);
        return this;
    }

    unregister(name) {
        this.middlewareMap.delete(name);
        this.orderMap.delete(name);
        this.colorMap.delete(name);
    }

    getOrderedMiddleware() {
        return Array.from(this.middlewareMap.entries())
            .map(([name, data]) => ({
                name,
                ...data,
                order: this.orderMap.get(name),
                color: this.colorMap.get(name)
            }))
            .sort((a, b) => a.order - b.order);
    }

    setActive(name, active) {
        const middleware = this.middlewareMap.get(name);
        if (middleware) {
            middleware.active = active;
        }
    }

    isActive(name) {
        return this.middlewareMap.get(name)?.active ?? false;
    }

    getMiddleware(name) {
        return this.middlewareMap.get(name)?.instance;
    }

    getAllMiddleware() {
        return this.getOrderedMiddleware();
    }
}

// Create and export a singleton instance
export const middlewareRegistry = new MiddlewareRegistry(); 