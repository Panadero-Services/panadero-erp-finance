export const RequestValidator = {
    validateRequest(requestData = {}) {
        // Request Validation Section
        const validation = {
            isValid: false,
            details: {
                methodCheck: {
                    isValid: false,
                    message: 'HTTP method validation'
                },
                headerCheck: {
                    isValid: false,
                    message: 'Headers validation'
                },
                contentCheck: {
                    isValid: false,
                    message: 'Content-Type validation'
                }
            },
            error: null
        };

        try {
            // Method Validation
            const method = (requestData.method || 'GET').toUpperCase();
            validation.details.methodCheck.isValid = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(method);

            // Headers Validation
            const headers = requestData.headers || {};
            validation.details.headerCheck.isValid = headers['X-Requested-With'] === 'XMLHttpRequest';

            // Content-Type Validation
            const contentType = headers['Content-Type'];
            validation.details.contentCheck.isValid = !contentType || 
                contentType.includes('application/json') || 
                contentType.includes('multipart/form-data');

            // Overall validation status
            validation.isValid = Object.values(validation.details)
                                     .every(check => check.isValid);

            if (!validation.isValid) {
                validation.error = 'Request validation failed';
            }
        } catch (error) {
            validation.error = 'Request validation error: ' + error.message;
        }

        return validation;
    }
}; 