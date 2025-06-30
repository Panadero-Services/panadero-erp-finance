export class MiddlewareRegistry {
    constructor() {
        this.middlewareMap = new Map();
        this.orderMap = new Map();
    }

    register(name, middleware, order = 100) {
        this.middlewareMap.set(name, middleware);
        this.orderMap.set(name, order);
    }

    unregister(name) {
        this.middlewareMap.delete(name);
        this.orderMap.delete(name);
    }

    getOrderedMiddleware() {
        return Array.from(this.middlewareMap.entries())
            .sort((a, b) => this.orderMap.get(a[0]) - this.orderMap.get(b[0]))
            .map(([_, middleware]) => middleware);
    }
} 