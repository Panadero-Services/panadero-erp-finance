export class ConditionalMiddleware extends BaseMiddleware {
    constructor(condition, middleware) {
        super();
        this.condition = condition;
        this.middleware = middleware;
    }

    async handle(request) {
        if (this.condition(request)) {
            return await this.middleware.handle(request);
        }
        return await super.handle(request);
    }
} 