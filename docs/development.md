# Development Guide

## Dependency Management

### Troubleshooting Module Issues

If you encounter issues with modules, dependencies, or functionality (especially audio/sound):

1. **Clear and reinstall modules:**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

2. **Clear browser cache and hard refresh:**
   - Press `Ctrl+Shift+R` (or `Cmd+Shift+R` on Mac)
   - Or open DevTools → Network tab → check "Disable cache"

3. **Check for dependency conflicts:**
   ```bash
   npm ls
   npm audit
   ```

4. **Verify sound files exist:**
   - Check that all sound files are in `/public/sounds/`
   - Ensure file permissions are correct

### Audio System Troubleshooting

The game uses Web Audio API which requires user interaction to initialize:

- **First click/input** unlocks the audio context
- **Browser autoplay policies** may block audio until user interaction
- **HTTPS required** for audio in production

### Common Issues and Solutions

| Issue | Solution |
|-------|----------|
| Sound not playing | Check browser console for audio errors, ensure user has interacted with page |
| Module not found | Clear node_modules and reinstall |
| Audio context errors | Check browser compatibility, ensure HTTPS in production |
| Dependency conflicts | Use `npm ls` to identify conflicts, update or pin versions |

## The Minimal Impact Approach

This document expands on our default development methodology, providing examples and detailed explanations of our approach.

[Include the full guidelines here with specific examples from your codebase]

## Example: Delete Functionality Fix

Here's a real example of our Minimal Impact Approach in action:

### The Issue
Delete functionality wasn't working properly in DynamicTable component.

### The Solution
```javascript
const confirmDelete = async () => {
    if (!recordToDelete.value) return;
    
    try {
        isDeleting.value = true;
        deleteError.value = null;
        
        router.delete(`/api/${props.table}/${recordToDelete.value.id}`, {
            onSuccess: (page) => {
                if (page?.props?.flash?.success) {
                    showDeleteDialog.value = false;
                    recordToDelete.value = null;
                }
                deleteError.value = page?.props?.flash?.error || null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
                deleteError.value = errors.message || 'An error occurred while deleting';
            },
            preserveScroll: true
        });
    } catch (error) {
        deleteError.value = error.message;
    } finally {
        isDeleting.value = false;
    }
};
```

### Why This is a Good Example
1. Fixed only the broken functionality (delete response handling)
2. Kept existing error handling and logging
3. Worked with existing flash message system
4. Didn't modify working code
5. Maintained consistent patterns 