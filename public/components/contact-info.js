// Contact Information Component
class ContactInfo {
    constructor() {
        this.styles = `
            .contact-section { 
                padding: 4rem 0; 
                background: rgba(0, 0, 0, 0.3); 
            }
            .contact-content { 
                max-width: 48rem; 
                margin: 0 auto; 
                text-align: center; 
            }
            .contact-grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 3rem; 
                margin-top: 3rem; 
                text-align: left; 
            }
            .contact-info, .contact-cta { 
                background: rgba(255, 255, 255, 0.05); 
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 1rem; 
                padding: 2rem;
            }
            .contact-item { 
                display: flex; 
                align-items: flex-start; 
                margin-bottom: 1.5rem; 
                color: #d1d5db; 
            }
            .contact-icon { 
                margin-right: 1rem; 
                font-size: 1.25rem; 
                width: 2rem; 
            }
            .section-title {
                color: white;
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                text-align: center;
            }
            .btn-primary {
                background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(13, 148, 136, 0.4);
            }
            @media (max-width: 768px) { 
                .contact-grid { 
                    grid-template-columns: 1fr; 
                }
                .section-title {
                    font-size: 2rem;
                }
            }
        `;
        
        this.html = `
            <section class="contact-section" id="contact">
                <div class="container">
                    <div class="contact-content">
                        <h2 class="section-title">Contact Information</h2>
                        <div class="contact-grid">
                            <div class="contact-info">
                                <h3 style="color: white; margin-bottom: 1.5rem; font-size: 1.25rem;">Panadero Services</h3>
                                <div class="contact-item">
                                    <span class="contact-icon">👤</span>
                                    <span>Luis Panadero</span>
                                </div>
                                <div class="contact-item">
                                    <span class="contact-icon">📍</span>
                                    <span>Stationsstraat 10<br>4571LB Axel, Netherlands</span>
                                </div>
                                <div class="contact-item">
                                    <span class="contact-icon">📞</span>
                                    <a href="tel:+31655328495" style="color: #5eead4; text-decoration: none;">+31 655 328 495</a>
                                </div>
                                <div class="contact-item">
                                    <span class="contact-icon">💼</span>
                                    <span>lbakker@me.com</span>
                                </div>
                                <div class="contact-item">
                                    <span class="contact-icon">🔗</span>
                                    <a href="https://www.linkedin.com/in/lieuwe-b-172065168/" target="_blank" style="color: #5eead4; text-decoration: none;">LinkedIn Profile</a>
                                </div>
                            </div>
                            <div class="contact-cta">
                                <h3 style="color: white; margin-bottom: 1.5rem;">Get In Touch</h3>

                                <div class="contact-item">
                                    <span class="contact-icon">👤</span>
                                    <span>Eric Scott</span>
                                </div>

                                <div class="contact-item">
                                    <span class="contact-icon">💼</span>
                                    <span>Ericscott2027@yahoo.com</span>
                                </div>

                                <p style="color: #d1d5db; margin-bottom: 2rem; margin-top: 3rem;">
                                    Interested in our AI technologies? 
                                    Contact us for a free consultation and demo.
                                </p>

                                <button class="btn-primary" onclick="window.location.href='tel:+31655328495'">
                                    <span>📞 Call Now</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;
    }

    // Method to inject styles into the page
    injectStyles() {
        if (!document.getElementById('contact-info-styles')) {
            const styleSheet = document.createElement('style');
            styleSheet.id = 'contact-info-styles';
            styleSheet.textContent = this.styles;
            document.head.appendChild(styleSheet);
        }
    }

    // Method to render the contact section
    render(containerId) {
        this.injectStyles();
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = this.html;
        } else {
            console.error(`Container with id "${containerId}" not found`);
        }
    }

    // Method to append the contact section to the end of the body
    appendToBody() {
        this.injectStyles();
        document.body.insertAdjacentHTML('beforeend', this.html);
    }
}

// Auto-initialize if loaded directly
if (typeof window !== 'undefined') {
    window.ContactInfo = ContactInfo;
    
    // Auto-append if data attribute is present
    document.addEventListener('DOMContentLoaded', function() {
        if (document.body.hasAttribute('data-include-contact-info')) {
            const contactInfo = new ContactInfo();
            contactInfo.appendToBody();
        }
    });
}
