import { BaseMiddleware } from './BaseMiddleware';

export class RequestValidationMiddleware extends BaseMiddleware {
    async handle(request) {
        const validation = {
            isValid: false,
            layer: 'request',
            details: {
                headers: {
                    isValid: false,
                    message: 'Headers validation'
                },
                integrity: {
                    isValid: false,
                    message: 'Data integrity validation'
                },
                schema: {
                    isValid: false,
                    message: 'Schema validation'
                },
                sanitization: {
                    isValid: false,
                    message: 'Data sanitization'
                }
            },
            error: null
        };

        try {
            // Headers Validation
            validation.details.headers.isValid = this.validateHeaders(request.headers);

            // Data Integrity Check
            validation.details.integrity.isValid = this.checkDataIntegrity(request.data);

            // Schema Validation
            validation.details.schema.isValid = await this.validateSchema(
                request.data,
                request.schema
            );

            // Data Sanitization
            validation.details.sanitization.isValid = this.sanitizeData(request.data);

            // Overall validation
            validation.isValid = Object.values(validation.details)
                                     .every(check => check.isValid);

            if (!validation.isValid) {
                validation.error = 'Request validation failed';
                return validation;
            }

            return await super.handle(request);

        } catch (error) {
            validation.error = `Request validation error: ${error.message}`;
            return validation;
        }
    }

    validateHeaders(headers = {}) {
        const requiredHeaders = [
            'X-Requested-With',
            'Accept',
            'Content-Type'
        ];

        return requiredHeaders.every(header => headers[header]);
    }

    checkDataIntegrity(data) {
        if (!data) return true;
        return typeof data === 'object' && !Array.isArray(data);
    }

    async validateSchema(data, schema) {
        if (!schema) return true;
        // Implement your schema validation logic here
        return true;
    }

    sanitizeData(data) {
        if (!data) return true;
        // Implement your data sanitization logic here
        return true;
    }
} 