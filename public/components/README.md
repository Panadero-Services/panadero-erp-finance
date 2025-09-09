# Reusable Components

Reusable components that can be imported into any HTML page.

## Components

### Compliance Footer Component
- `compliance-footer.html` - Standalone HTML with embedded CSS
- `compliance-footer.js` - JavaScript class component
- `compliance-footer-include.html` - Simple include script

### Contact Information Component
- `contact-info.js` - JavaScript class component
- `contact-info-include.html` - Simple include script

### Panadero Header Component
- `panadero-header.js` - JavaScript class component
- `panadero-header-include.html` - Simple include script

## Usage Methods

### Method 1: JavaScript Class (Recommended)

#### Compliance Footer
```html
<!-- Include the script -->
<script src="components/compliance-footer.js"></script>

<!-- Add container where footer should appear -->
<div id="compliance-footer-container"></div>

<script>
// Initialize the footer
document.addEventListener('DOMContentLoaded', function() {
    if (window.ComplianceFooter) {
        const footer = new ComplianceFooter();
        footer.render('compliance-footer-container');
    }
});
</script>
```

#### Contact Information
```html
<!-- Include the script -->
<script src="components/contact-info.js"></script>

<!-- Add container where contact section should appear -->
<div id="contact-info-container"></div>

<script>
// Initialize the contact info
document.addEventListener('DOMContentLoaded', function() {
    if (window.ContactInfo) {
        const contactInfo = new ContactInfo();
        contactInfo.render('contact-info-container');
    }
});
</script>
```

#### Panadero Header
```html
<!-- Include the script -->
<script src="components/panadero-header.js"></script>


<!-- Add container where header should appear -->
<div id="panadero-header-container"></div>

<script>
// Initialize the header
document.addEventListener('DOMContentLoaded', function() {
    if (window.PanaderoHeader) {
        const header = new PanaderoHeader();
        header.render('panadero-header-container');
    }
});
</script>
```

### Method 2: Simple Include

#### Compliance Footer
```html
<!-- Add placeholder -->
<div id="compliance-footer-placeholder"></div>

<!-- Include the component -->
<script src="components/compliance-footer-include.html"></script>
```

#### Contact Information
```html
<!-- Add placeholder -->
<div id="contact-info-placeholder"></div>

<!-- Include the component -->
<script src="components/contact-info-include.html"></script>
```

#### Panadero Header
```html
<!-- Add placeholder -->
<div id="panadero-header-placeholder"></div>

<!-- Include the component -->
<script src="components/panadero-header-include.html"></script>
```

### Method 3: Direct HTML Include

```html
<!-- Include the entire component -->
<script src="components/compliance-footer.html"></script>
```

## Features

- ✅ Responsive design
- ✅ Hover effects on links
- ✅ Mobile-friendly (links stack vertically)
- ✅ Consistent styling across all pages
- ✅ Easy to maintain and update
- ✅ No external dependencies

## Customization

To modify the footer content or styling, edit the respective component files. Changes will automatically apply to all pages using the component.

## Integration

These components are currently integrated in:
- `public/ai-engine-landing.html` (header, contact info, and compliance footer)
- Can be easily added to any other landing page

## Benefits

- ✅ **Reusable** - Can be imported into any HTML file
- ✅ **Maintainable** - Update once, applies everywhere
- ✅ **Responsive** - Mobile-friendly design
- ✅ **Consistent** - Same styling across all pages
- ✅ **Easy Integration** - Just include the script and add a container div
- ✅ **Modular** - Use only the components you need
