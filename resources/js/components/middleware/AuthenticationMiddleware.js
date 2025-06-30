import { BaseMiddleware } from './BaseMiddleware';

export class AuthenticationMiddleware extends BaseMiddleware {
    async handle(request) {
        const validation = {
            isValid: false,
            layer: 'authentication',
            details: {
                session: {
                    isValid: false,
                    message: 'Session validation'
                },
                csrf: {
                    isValid: false,
                    message: 'CSRF token validation'
                },
                token: {
                    isValid: false,
                    message: 'API token validation'
                },
                access: {
                    isValid: false,
                    message: 'Access validation'
                }
            },
            error: null
        };

        try {
            // Session Check
            const hasSession = document.cookie.includes('laravel_session');
            validation.details.session.isValid = hasSession;

            // CSRF Token Check
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            validation.details.csrf.isValid = !!csrfToken;

            // API Token Check (if applicable)
            const apiToken = request.headers?.['Authorization'];
            validation.details.token.isValid = !request.requiresToken || !!apiToken;

            // Access Check (Basic user access)
            const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
            validation.details.access.isValid = !!userId;

            // Overall validation
            validation.isValid = Object.values(validation.details)
                                     .every(check => check.isValid);

            if (!validation.isValid) {
                validation.error = 'Authentication failed';
                return validation;
            }

            return await super.handle(request);

        } catch (error) {
            validation.error = `Authentication error: ${error.message}`;
            return validation;
        }
    }
} 