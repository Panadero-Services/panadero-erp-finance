import { middlewareManager } from './MiddlewareManager';
import { AuthenticationMiddleware } from './AuthenticationMiddleware';
import { AuthorizationMiddleware } from './AuthorizationMiddleware';
import { RequestValidationMiddleware } from './RequestValidationMiddleware';

// Initialize all middleware
export function initializeMiddleware() {
    // Register authentication middleware
    const authMiddleware = new AuthenticationMiddleware();
    middlewareManager.registerMiddleware(
        authMiddleware.name,
        authMiddleware,
        1, // order
        '#4CAF50' // color (optional)
    );

    // Register other middleware as needed...
} 