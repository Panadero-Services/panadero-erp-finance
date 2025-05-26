// utils/validators.js

export function parseRules(ruleString) {
  return ruleString.split('|').reduce((acc, rule) => {
    if (rule.includes(':')) {
      const [key, val] = rule.split(':');
      acc[key] = val;
    } else {
      acc[rule] = true;
    }
    return acc;
  }, {});
}

export function validateField(value, rules) {
  if (!rules) return true;

  for (const [rule, param] of Object.entries(rules)) {
    switch (rule) {
      case 'required':
        if (value === null || value === undefined || value.toString().trim() === '') return false;
        break;
      case 'numeric':
        if (isNaN(value)) return false;
        break;
      case 'email':
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) return false;
        break;
      case 'alpha':
        const alphaRegex = /^[A-Za-z]+$/;
        if (!alphaRegex.test(value)) return false;
        break;
      case 'min':
        if (typeof value === 'string' && value.length < +param) return false;
        if (typeof value === 'number' && value < +param) return false;
        break;
      case 'max':
        if (typeof value === 'string' && value.length > +param) return false;
        if (typeof value === 'number' && value > +param) return false;
        break;
      case 'confirmed':
        // Confirmed field should be handled separately if needed
        return true;
      case 'unique':
        // Can't validate unique on client
        continue;
    }
  }

  return true;
}
