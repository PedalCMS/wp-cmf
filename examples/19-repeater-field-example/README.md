# Example 19: Repeater Field Example

This example demonstrates the **RepeaterField** - a powerful container field type that allows users to create repeatable sets of fields.

## Overview

The RepeaterField is perfect for:
- Team member lists
- Gallery images with captions
- FAQ sections (question/answer pairs)
- Price tables
- Social media links
- Any dynamic list of structured data

## Features

- **Add/Remove Rows**: Dynamically add and remove items
- **Drag-and-Drop Sorting**: Reorder items with jQuery UI Sortable
- **Collapsible Rows**: Collapse/expand rows for easier management
- **Min/Max Rows**: Set minimum and maximum number of rows
- **Custom Labels**: Configure button and row labels
- **Nested Fields**: Any field type can be nested inside a repeater

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `fields` | array | `[]` | Array of sub-field configurations |
| `min_rows` | int | `0` | Minimum number of rows required |
| `max_rows` | int | `0` | Maximum number of rows (0 = unlimited) |
| `button_label` | string | `'Add Row'` | Label for the add button |
| `row_label` | string | `'Row'` | Label displayed in row header |
| `collapsible` | bool | `true` | Whether rows can be collapsed |
| `collapsed` | bool | `false` | Whether rows start collapsed |
| `sortable` | bool | `true` | Whether rows can be reordered |

## Example Configurations

### Team Members (CPT Example)

```php
[
    'name'        => 'team_members',
    'type'        => 'repeater',
    'label'       => 'Team Members',
    'description' => 'Add team members to display on this page',
    'min_rows'    => 1,
    'max_rows'    => 20,
    'button_label' => 'Add Team Member',
    'row_label'   => 'Team Member',
    'collapsible' => true,
    'collapsed'   => false,
    'sortable'    => true,
    'fields'      => [
        [
            'name'  => 'name',
            'type'  => 'text',
            'label' => 'Name',
            'required' => true,
        ],
        [
            'name'  => 'title',
            'type'  => 'text',
            'label' => 'Job Title',
        ],
        [
            'name'  => 'bio',
            'type'  => 'textarea',
            'label' => 'Biography',
            'rows'  => 3,
        ],
        [
            'name'  => 'email',
            'type'  => 'email',
            'label' => 'Email Address',
        ],
    ],
]
```

### FAQ Section (Settings Page Example)

```php
[
    'name'        => 'faq_items',
    'type'        => 'repeater',
    'label'       => 'FAQ Items',
    'description' => 'Add frequently asked questions',
    'button_label' => 'Add FAQ',
    'row_label'   => 'Question',
    'fields'      => [
        [
            'name'  => 'question',
            'type'  => 'text',
            'label' => 'Question',
            'required' => true,
        ],
        [
            'name'  => 'answer',
            'type'  => 'textarea',
            'label' => 'Answer',
            'rows'  => 4,
            'required' => true,
        ],
    ],
]
```

## Data Structure

Repeater fields store data as a serialized array. When retrieved, you get:

```php
$team_members = get_post_meta($post_id, 'team_members', true);

// Returns:
[
    [
        'name'  => 'John Doe',
        'title' => 'CEO',
        'bio'   => 'John founded the company...',
        'email' => 'john@example.com',
    ],
    [
        'name'  => 'Jane Smith',
        'title' => 'CTO',
        'bio'   => 'Jane leads our technology...',
        'email' => 'jane@example.com',
    ],
]
```

## Frontend Display Example

```php
$team_members = get_post_meta(get_the_ID(), 'team_members', true);

if (!empty($team_members) && is_array($team_members)) {
    echo '<div class="team-grid">';
    foreach ($team_members as $member) {
        echo '<div class="team-member">';
        echo '<h3>' . esc_html($member['name']) . '</h3>';
        if (!empty($member['title'])) {
            echo '<p class="job-title">' . esc_html($member['title']) . '</p>';
        }
        if (!empty($member['bio'])) {
            echo '<p class="bio">' . esc_html($member['bio']) . '</p>';
        }
        if (!empty($member['email'])) {
            echo '<p class="email"><a href="mailto:' . esc_attr($member['email']) . '">' . esc_html($member['email']) . '</a></p>';
        }
        echo '</div>';
    }
    echo '</div>';
}
```

## Validation

The RepeaterField validates:
- Minimum number of rows (if `min_rows` is set)
- Maximum number of rows (if `max_rows` is set)
- Each sub-field's validation rules (required, patterns, etc.)

## Files in This Example

- `example.php` - Complete plugin with Team and Event CPTs using repeater fields
- `settings-example.php` - Settings page with FAQ and social links repeaters
- `README.md` - This documentation

## Installation

1. Copy the example folder to your `wp-content/plugins/` directory
2. Activate the plugin in WordPress admin
3. Visit the "Teams" or "Events" CPT to see the repeater fields in action
4. Visit Settings → Site Options to see repeater fields in settings context
