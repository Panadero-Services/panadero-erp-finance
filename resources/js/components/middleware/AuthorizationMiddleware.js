import { BaseMiddleware } from '@/components/middleware/BaseMiddleware';

export class AuthorizationMiddleware extends BaseMiddleware {
    constructor() {
        super();
        this.name = 'Authorization';
    }

    getChecks(request) {
        return {
            hasAccess: request.context?.hasAccess || false,
            hasPermissions: request.context?.permissions?.length > 0 || false,
            requiredPermissions: this.validateRequiredPermissions(request),
            hasRole: this.validateRole(request)
        };
    }

    getAdditionalData(request) {
        return {
            permissions: request.context?.permissions || [],
            roles: request.context?.roles || [],
            resource: request.path
        };
    }

    validateRequiredPermissions(request) {
        const requiredPermissions = request.context?.requiredPermissions || [];
        const userPermissions = request.context?.permissions || [];
        
        if (requiredPermissions.length === 0) return true;
        return requiredPermissions.every(permission => 
            userPermissions.includes(permission)
        );
    }

    validateRole(request) {
        const requiredRole = request.context?.requiredRole;
        const userRoles = request.context?.roles || [];
        
        if (!requiredRole) return true;
        return userRoles.includes(requiredRole);
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
  //  hasAccess, hasSelf, hasPermissions, requiredPermissions