import { BaseMiddleware } from './BaseMiddleware';

export class AuthorizationMiddleware extends BaseMiddleware {
    async handle(request) {
        const validation = {
            isValid: false,
            layer: 'authorization',
            details: {
                roles: {
                    isValid: false,
                    message: 'Role validation'
                },
                permissions: {
                    isValid: false,
                    message: 'Permission validation'
                },
                resource: {
                    isValid: false,
                    message: 'Resource access validation'
                },
                action: {
                    isValid: false,
                    message: 'Action permission validation'
                }
            },
            error: null
        };

        try {
            // Get user context
            const userRoles = JSON.parse(
                document.querySelector('meta[name="user-roles"]')?.getAttribute('content') || '[]'
            );
            const userPermissions = JSON.parse(
                document.querySelector('meta[name="user-permissions"]')?.getAttribute('content') || '{}'
            );

            // Role Check
            validation.details.roles.isValid = !request.requiredRole || 
                userRoles.includes(request.requiredRole);

            // Permission Check
            validation.details.permissions.isValid = this.checkPermissions(
                userPermissions,
                request.requiredPermissions
            );

            // Resource Access Check
            validation.details.resource.isValid = this.checkResourceAccess(
                userPermissions,
                request.resource
            );

            // Action Permission Check
            validation.details.action.isValid = this.checkActionPermission(
                userPermissions,
                request.resource,
                request.action
            );

            // Overall validation
            validation.isValid = Object.values(validation.details)
                                     .every(check => check.isValid);

            if (!validation.isValid) {
                validation.error = 'Authorization failed';
                return validation;
            }

            return await super.handle(request);

        } catch (error) {
            validation.error = `Authorization error: ${error.message}`;
            return validation;
        }
    }

    checkPermissions(userPermissions, requiredPermissions = []) {
        if (!requiredPermissions.length) return true;
        return requiredPermissions.every(perm => 
            Object.values(userPermissions).some(perms => perms.includes(perm))
        );
    }

    checkResourceAccess(userPermissions, resource) {
        return !resource || !!userPermissions[resource];
    }

    checkActionPermission(userPermissions, resource, action) {
        return !action || userPermissions[resource]?.includes(action);
    }
} 