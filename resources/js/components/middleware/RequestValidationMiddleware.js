import { BaseMiddleware } from '@/components/middleware/BaseMiddleware';

export class RequestValidationMiddleware extends BaseMiddleware {
    constructor() {
        super();
        this.name = 'Request';
    }

    getChecks(request) {
        return {
            dataFormat: this.validateDataFormat(request),
            requiredFields: this.validateRequiredFields(request),
            methodAllowed: this.validateMethod(request),
            contentType: this.validateContentType(request)
        };
    }

    getAdditionalData(request) {
        return {
            validatedData: this.sanitizeData(request),
            method: request.method,
            path: request.path,
            headers: this.getValidatedHeaders(request)
        };
    }

    validateDataFormat(request) {
        if (!request.data) return true;
        return typeof request.data === 'object' && !Array.isArray(request.data);
    }

    validateRequiredFields(request) {
        const requiredFields = request.context?.requiredFields || [];
        if (requiredFields.length === 0) return true;
        
        return requiredFields.every(field => 
            request.data && Object.prototype.hasOwnProperty.call(request.data, field)
        );
    }

    validateMethod(request) {
        const allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        return allowedMethods.includes(request.method?.toUpperCase());
    }

    validateContentType(request) {
        const contentType = request.headers?.['Content-Type'];
        if (!contentType) return false;
        return contentType.includes('application/json');
    }

    getValidatedHeaders(request) {
        const requiredHeaders = [
            'Content-Type',
            'X-Requested-With',
            'Accept'
        ];

        const validatedHeaders = {};
        requiredHeaders.forEach(header => {
            validatedHeaders[header] = {
                present: !!request.headers?.[header],
                value: request.headers?.[header] || null
            };
        });

        return validatedHeaders;
    }

    sanitizeData(request) {
        if (!request.data) return null;
        const sanitized = JSON.parse(JSON.stringify(request.data));
        
        // Preserve meta information if it exists
        if (sanitized.meta) {
            return {
                ...sanitized,
                meta: request.data.meta // Keep original meta data
            };
        }
        
        Object.keys(sanitized).forEach(key => {
            if (typeof sanitized[key] === 'string') {
                sanitized[key] = sanitized[key].trim();
            }
        });
        return sanitized;
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