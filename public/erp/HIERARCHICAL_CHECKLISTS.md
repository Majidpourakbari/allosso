# Hierarchical Checklists Feature

## Overview
This feature allows users to create parent-child relationships between checklist items within tasks, enabling a hierarchical structure for better organization and task management.

## Features

### 1. Parent-Child Relationships
- Checklists can have parent checklists
- Unlimited nesting levels (though UI is optimized for 3-4 levels)
- Visual indicators show hierarchy levels

### 2. Visual Hierarchy
- Indented display with colored left borders
- Icons indicate parent (📁) and child (📄) items
- Different colors for different hierarchy levels

### 3. Smart Parent Selection
- Dropdown shows all available parent options
- Prevents circular references
- Excludes current item when editing

### 4. Cascading Deletion
- Deleting a parent automatically deletes all children
- Proper cleanup of associated files and audio

## Database Changes

### New Column
- `parent_id` (INT, NULL) - References the parent checklist ID
- Foreign key constraint with CASCADE DELETE
- Index for performance

### Migration
Run the migration script to add the new column:
```bash
php run_migration.php
```

## Usage

### Adding a Checklist Item
1. Click "Add Item" in the task details
2. Fill in the content and other details
3. Select a parent from the dropdown (optional)
4. Save the item

### Editing a Checklist Item
1. Click the edit button on any checklist item
2. Modify content, dates, or parent selection
3. Save changes

### Visual Indicators
- **No indent**: Main/root level items
- **Indented with colored border**: Child items
- **📁 icon**: Items with children
- **📄 icon**: Child items

## Technical Implementation

### Files Modified
- `tasks.php` - Main UI and JavaScript
- `add_checklist_item.php` - Add new items with parent support
- `edit_checklist_item.php` - Edit items with parent validation
- `delete_checklist_item.php` - Cascading deletion
- `get_task_details.php` - Hierarchical data structure
- `get_checklist_parents.php` - Parent dropdown data

### Key Functions
- `populateParentDropdown()` - Load parent options
- `renderHierarchicalChecklists()` - Display tree structure
- `renderChecklistItem()` - Render individual items
- `flattenChecklists()` - Convert hierarchy to flat list

### Validation Rules
- Cannot set item as its own parent
- Cannot create circular references
- Parent must belong to the same task
- Cascading deletion prevents orphaned children

## Benefits
1. **Better Organization**: Group related tasks under main items
2. **Clear Dependencies**: Show task relationships visually
3. **Improved Workflow**: Break down complex tasks into subtasks
4. **Enhanced UX**: Intuitive tree-like interface

## Future Enhancements
- Drag and drop reordering
- Collapse/expand tree nodes
- Bulk operations on parent items
- Progress calculation per hierarchy level 