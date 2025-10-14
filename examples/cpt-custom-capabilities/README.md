# Custom Capabilities CPT Example

This example demonstrates creating custom post types with custom capability mapping for advanced permission control and role-based access management.

## What This Example Shows

- Custom capability mapping
- Granular permission control
- Role-based access control
- Professional user management
- Security through capability checking

## The Code

```php
<?php
use Pedalcms\WpCmf\CPT\CustomPostType;

function register_projects_cpt() {
    $cpt = CustomPostType::from_array( 'project', [
        'singular' => 'Project',
        'plural'   => 'Projects',
        'public'   => true,
        'supports' => [ 'title', 'editor', 'thumbnail' ],
        'capability_type' => 'project',
        'map_meta_cap'    => true,
        'capabilities'    => [
            'edit_post'          => 'edit_project',
            'read_post'          => 'read_project',
            'delete_post'        => 'delete_project',
            'edit_posts'         => 'edit_projects',
            'edit_others_posts'  => 'edit_others_projects',
            'publish_posts'      => 'publish_projects',
            'read_private_posts' => 'read_private_projects',
        ],
    ] );
    
    $cpt->register();
}

add_action( 'init', 'register_projects_cpt' );
```

## Result

Creates a Projects CPT with completely custom capabilities, allowing for:
- Fine-grained permission control
- Role-based access management
- Enhanced security
- Professional user workflows

## Custom Capabilities Mapping

| WordPress Default | Project Custom | Purpose |
|------------------|----------------|---------|
| `edit_post` | `edit_project` | Edit individual projects |
| `read_post` | `read_project` | View individual projects |
| `delete_post` | `delete_project` | Delete individual projects |
| `edit_posts` | `edit_projects` | Access project list |
| `edit_others_posts` | `edit_others_projects` | Edit projects by other users |
| `publish_posts` | `publish_projects` | Publish projects |
| `read_private_posts` | `read_private_projects` | View private projects |

## Granting Capabilities

```php
function grant_project_capabilities() {
    // Full access for administrators
    $admin_role = get_role( 'administrator' );
    if ( $admin_role ) {
        $admin_role->add_cap( 'edit_project' );
        $admin_role->add_cap( 'read_project' );
        $admin_role->add_cap( 'delete_project' );
        $admin_role->add_cap( 'edit_projects' );
        $admin_role->add_cap( 'edit_others_projects' );
        $admin_role->add_cap( 'publish_projects' );
        $admin_role->add_cap( 'read_private_projects' );
    }

    // Limited access for editors
    $editor_role = get_role( 'editor' );
    if ( $editor_role ) {
        $editor_role->add_cap( 'edit_project' );
        $editor_role->add_cap( 'read_project' );
        $editor_role->add_cap( 'edit_projects' );
        $editor_role->add_cap( 'publish_projects' );
    }
}
```

## Key Configuration Options

- **`capability_type`**: Base capability name (`project`)
- **`map_meta_cap`**: Enable WordPress meta capability mapping
- **`capabilities`**: Complete mapping of all capabilities

## Use Cases

Perfect for:
- Client project management
- Restricted content systems
- Multi-user workflows
- Enterprise applications
- Systems requiring detailed access control

## Security Benefits

- **Principle of Least Privilege**: Users only get necessary permissions
- **Role Separation**: Different roles have different access levels
- **Audit Trail**: WordPress logs capability checks
- **Extensible**: Easy to add new roles or modify permissions