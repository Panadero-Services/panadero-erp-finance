export class BaseMiddleware {
    constructor() {
        this.nextMiddleware = null;
    }

    setNext(middleware) {
        this.nextMiddleware = middleware;
        return middleware;
    }

    async handle(request) {
        if (this.nextMiddleware) {
            return await this.nextMiddleware.handle(request);
        }
        return { isValid: true };
    }

    async validateAndContinue(request, validation) {
        if (!validation.isValid) {
            return validation;
        }
        return await this.handle(request);
    }
} 