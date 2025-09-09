// Compliance Footer Component
class ComplianceFooter {
    constructor() {
        this.styles = `
            .compliance-footer {
                background: rgba(15, 23, 42, 0.95); 
                padding: 3rem 0; 
                text-align: center;
                border-top: 1px solid rgba(13, 148, 136, 0.2);
            }
            .compliance-footer .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 1.5rem;
            }
            .footer-text {
                color: #94a3b8; 
                margin-bottom: 1rem;
            }
            .footer-links {
                display: flex; 
                justify-content: center; 
                gap: 2rem; 
                margin-top: 1rem;
            }
            .footer-link {
                color: #5eead4; 
                text-decoration: none; 
                transition: color 0.3s ease;
            }
            .footer-link:hover { 
                color: #14b8a6; 
            }
            @media (max-width: 768px) {
                .footer-links { 
                    flex-direction: column; 
                    gap: 1rem; 
                }
            }
        `;
        
        this.html = `
            <div class="compliance-footer">
                <div class="container footer-text">
                    <p>&copy; 2025 Indigo3 Exceptional ERP Solutions. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="/indigo3-compliance.html" class="footer-link">Compliance </a>
                        <a href="/indigo3-finance.html" class="footer-link">Finance </a>
                        <a href="/indigo3-hr.html" class="footer-link">HR </a>
                        <a href="/indigo3-inventory.html" class="footer-link">Inventory </a>
                        <a href="/ai-training-dashboard" class="footer-link">Training</a>
                    </div>
                </div>
            </div>
        `;
    }



    // Method to inject styles into the page
    injectStyles() {
        if (!document.getElementById('compliance-footer-styles')) {
            const styleSheet = document.createElement('style');
            styleSheet.id = 'compliance-footer-styles';
            styleSheet.textContent = this.styles;
            document.head.appendChild(styleSheet);
        }
    }

    // Method to render the footer
    render(containerId) {
        this.injectStyles();
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = this.html;
        } else {
            console.error(`Container with id "${containerId}" not found`);
        }
    }

    // Method to append the footer to the end of the body
    appendToBody() {
        this.injectStyles();
        document.body.insertAdjacentHTML('beforeend', this.html);
    }
}

// Auto-initialize if loaded directly
if (typeof window !== 'undefined') {
    window.ComplianceFooter = ComplianceFooter;
    
    // Auto-append if data attribute is present
    document.addEventListener('DOMContentLoaded', function() {
        if (document.body.hasAttribute('data-include-compliance-footer')) {
            const footer = new ComplianceFooter();
            footer.appendToBody();
        }
    });
}
