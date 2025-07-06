import { usePage } from '@inertiajs/vue3';

export class PermissionMiddleware {
    constructor() {
        this.requiredPermissions = [];
        this.requiredRoles = [];
    }

    // Set required permissions for current route
    requirePermissions(permissions = []) {
        this.requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];
        return this;
    }

    // Set required roles for current route
    requireRoles(roles = []) {
        this.requiredRoles = Array.isArray(roles) ? roles : [roles];
        return this;
    }

    // Check if user has required permissions
    checkPermissions() {
        const page = usePage();
        const user = page.props.auth?.user;
        
        if (!user) return false;

        const userPermissions = user.permissions || [];
        const userRoles = user.roles || [];

        // Check permissions
        const hasPermissions = this.requiredPermissions.length === 0 || 
            this.requiredPermissions.every(permission => 
                userPermissions.includes(permission)
            );

        // Check roles
        const hasRoles = this.requiredRoles.length === 0 || 
            this.requiredRoles.some(role => 
                userRoles.includes(role)
            );

        return hasPermissions && hasRoles;
    }

    // Guard route with permissions
    guard() {
        if (!this.checkPermissions()) {
            console.log('Access denied: Insufficient permissions');
            window.location.href = '/unauthorized';
            return false;
        }
        return true;
    }
}

export const permissionMiddleware = new PermissionMiddleware(); 