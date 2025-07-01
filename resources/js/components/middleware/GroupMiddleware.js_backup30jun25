export class MiddlewareGroup {
    constructor(name) {
        this.name = name;
        this.middlewares = [];
    }

    add(middleware) {
        this.middlewares.push(middleware);
    }

    remove(middleware) {
        const index = this.middlewares.indexOf(middleware);
        if (index > -1) {
            this.middlewares.splice(index, 1);
        }
    }
} 