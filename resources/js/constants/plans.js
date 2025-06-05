// data/plans.js
export default [
  {
    title: 'Free',
    price: '$0',
    period: 'per month',
    description: 'Ideal for getting started',
    features: [
      '2-week Pro trial',
      '25 prompt credits/month',
      'All premium models',
      { text: 'Optional zero data retention', tooltip: 'Your data won’t be stored on our servers.' },
    ],
    popular: false,
    cta: {
      label: 'Download',
      href: '/download',
    },
  },
  {
    title: 'Pro',
    price: '$15',
    period: 'per month',
    description: 'For professionals needing more',
    features: [
      'Everything in Free',
      '500 prompt credits/month',
      { text: 'Add-on credits at $10/250 credits', tooltip: 'Buy more credits as needed.' },
    ],
    popular: true,
    cta: {
      label: 'Select Plan',
      href: '/signup?plan=pro',
    },
  },
  {
    title: 'Enterprise',
    price: 'Custom',
    period: 'per month',
    description: 'Tailored features for large teams',
    features: [
      'Everything in Pro',
      '1,000+ credits/user',
      'Dedicated account manager',
      { text: 'Priority support', tooltip: 'Receive support within 1 hour.' },
    ],
    popular: false,
    cta: {
      label: 'Contact Sales',
      href: '/contact-sales',
    },
  },
];
