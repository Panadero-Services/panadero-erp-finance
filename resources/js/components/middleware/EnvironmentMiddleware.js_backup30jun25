export class EnvironmentMiddleware extends BaseMiddleware {
    constructor(environment, middleware) {
        super();
        this.environment = environment;
        this.middleware = middleware;
    }

    async handle(request) {
        if (process.env.NODE_ENV === this.environment) {
            return await this.middleware.handle(request);
        }
        return await super.handle(request);
    }
} 