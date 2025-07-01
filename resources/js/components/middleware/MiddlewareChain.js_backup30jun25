import { AuthenticationMiddleware } from './AuthenticationMiddleware';
import { AuthorizationMiddleware } from './AuthorizationMiddleware';
import { RequestValidationMiddleware } from './RequestValidationMiddleware';

export class MiddlewareChain {
    constructor() {
        // Create middleware chain
        const authentication = new AuthenticationMiddleware();
        const authorization = new AuthorizationMiddleware();
        const requestValidation = new RequestValidationMiddleware();

        // Link middleware chain
        authentication.setNext(authorization);
        authorization.setNext(requestValidation);

        this.chain = authentication;
    }

    async process(request) {
        try {
            return await this.chain.handle(request);
        } catch (error) {
            return {
                isValid: false,
                layer: 'chain',
                error: `Middleware chain error: ${error.message}`
            };
        }
    }
} 