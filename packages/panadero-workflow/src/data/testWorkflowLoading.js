/**
 * Test Workflow Loading
 * Verify that the new workflow structure loads correctly
 */

import { getAllWorkflowTemplates, getWorkflowTemplateById } from './workflowTemplatesConfig.js';
import { useWorkflowValidation } from '../composables/useWorkflowValidation.js';

async function testWorkflowLoading() {
  console.debug('🧪 Testing Workflow Loading...');
  
  const { validateWorkflow, validateStepTypes } = useWorkflowValidation();
  
  try {
    // Test 1: Get all templates
    console.debug('\n📋 Test 1: Getting all workflow templates...');
    const allTemplates = getAllWorkflowTemplates();
    console.debug(`✅ Found ${allTemplates.length} workflow templates`);
    
    // Test 2: Test vendor onboarding workflow specifically
    console.debug('\n🔍 Test 2: Testing Vendor Onboarding workflow...');
    const vendorTemplate = getWorkflowTemplateById('vendor-onboarding');
    if (vendorTemplate) {
      console.debug(`✅ Vendor template found: ${vendorTemplate.name}`);
      
      // Test 3: Load the actual steps
      console.debug('\n📝 Test 3: Loading workflow steps...');
      const steps = await vendorTemplate.getSteps();
      console.debug(`✅ Loaded ${steps.length} steps for vendor onboarding`);
      
      // Test 4: Verify step types match new WORKFLOW_STEPTYPES
      console.debug('\n🎯 Test 4: Verifying step types...');
      const stepTypes = steps.map(step => step.type);
      console.debug('Step types found:', stepTypes);
      
      // Test 5: Validate workflow structure using validation system
      console.debug('\n✅ Test 5: Validating workflow structure...');
      const workflowWithSteps = { ...vendorTemplate, steps };
      const validation = validateWorkflow(workflowWithSteps);
      
      if (validation.isValid) {
        console.debug('✅ Workflow structure is valid');
      } else {
        console.error('❌ Workflow structure validation failed:');
        validation.errors.forEach(error => console.error(`  - ${error}`));
      }
      
      // Test 6: Validate step types specifically
      console.debug('\n🎯 Test 6: Validating step types...');
      const stepTypeValidation = validateStepTypes(steps);
      
      if (stepTypeValidation.isValid) {
        console.debug('✅ All step types are valid');
      } else {
        console.error('❌ Invalid step types found:');
        stepTypeValidation.invalidTypes.forEach(invalid => {
          console.error(`  - Step ${invalid.stepIndex + 1} (${invalid.stepName}): Invalid type '${invalid.invalidType}'`);
          console.error(`    Valid types: ${invalid.validTypes.join(', ')}`);
        });
      }
      
    } else {
      console.error('❌ Vendor template not found');
    }
    
    console.debug('\n🎉 All tests completed successfully!');
    
  } catch (error) {
    console.error('❌ Test failed:', error);
  }
}

// Export for use in browser console
if (typeof window !== 'undefined') {
  window.testWorkflowLoading = testWorkflowLoading;
}

export { testWorkflowLoading };
