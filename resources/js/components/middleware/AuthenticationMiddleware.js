import { BaseMiddleware } from '@/components/middleware/BaseMiddleware';
import { usePage } from '@inertiajs/vue3';

export class AuthenticationMiddleware extends BaseMiddleware {
    constructor() {
        super();
        this.name = 'Authentication';
    }

    getChecks(request) {
        const page = usePage();
        const auth = page.props.auth;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const xsrfToken = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        return {
            userValid: !!auth?.user,
            csrfValid: !!csrf,
            xsrfValid: !!xsrfToken,
            sessionValid: !!auth?.user,
        };
    }

    getAdditionalData(request) {
        const page = usePage();
        const auth = page.props.auth;
        
        return {
            user: auth?.user || null,
            session: auth?.user ? 'active' : 'expired',
            token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
    }

    async handle(request) {
        const checks = this.getChecks(request);
        const additionalData = this.getAdditionalData(request);

        return {
            isValid: Object.values(checks).every(Boolean),
            checks,
            ...additionalData
        };
    }
}

export default AuthenticationMiddleware; 