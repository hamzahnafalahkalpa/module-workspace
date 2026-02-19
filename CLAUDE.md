# CLAUDE.md - Module Workspace

This file provides guidance for working with the `hanafalah/module-workspace` Laravel package.

## Module Overview

Module Workspace is a Laravel package that provides workspace and facility management functionality for multi-tenant healthcare applications. It handles workspace creation, settings, address management, and integration with external systems like Satu Sehat (Indonesian health system).

**Namespace:** `Hanafalah\ModuleWorkspace`

## Dependencies

- `hanafalah/laravel-support` - Base support package (provides BaseServiceProvider, PackageManagement, Data classes)
- `hanafalah/module-regional` - Regional/address management (provides HasAddress trait, AddressData)

## Directory Structure

```
src/
├── Commands/
│   ├── EnvironmentCommand.php      # Base command class
│   └── InstallMakeCommand.php      # Installation command
├── Concerns/
│   └── Building/
│       └── HasRoom.php             # Trait for room relationships
├── Contracts/
│   ├── Data/                       # Data contract interfaces
│   ├── Schemas/                    # Schema contract interfaces
│   └── ModuleWorkspace.php         # Main module contract
├── Data/
│   ├── WorkspaceData.php           # Main workspace DTO
│   ├── WorkspacePropsData.php      # Props nested DTO
│   ├── WorkspaceSettingData.php    # Settings nested DTO
│   └── StakeholderData.php         # Stakeholder nested DTO
├── Enums/
│   └── Workspace/
│       └── Status.php              # ACTIVE, INACTIVE, SUSPENDED, DRAFT
├── Events/
│   ├── Contracts/
│   │   └── WorkspaceEvent.php      # Base event class
│   ├── CreatingWorkspace.php
│   ├── WorkspaceCreated.php
│   ├── SavingWorkspace.php
│   ├── WorkspaceSaved.php
│   ├── UpdatingWorkspace.php
│   ├── WorkspaceUpdated.php
│   ├── DeletingWorkspace.php
│   └── WorkspaceDeleted.php
├── Facades/
│   └── Workspace.php               # Facade for ModuleWorkspace
├── Models/
│   └── Workspace/
│       └── Workspace.php           # Main Eloquent model
├── Providers/
│   └── CommandServiceProvider.php  # Registers artisan commands
├── Resources/
│   └── Workspace/
│       ├── ViewWorkspace.php       # Basic API resource
│       ├── ShowWorkspace.php       # Detailed API resource
│       └── SettingWorkspace.php    # Settings API resource
├── Schemas/
│   └── Workspace.php               # Business logic/repository
├── ModuleWorkspace.php             # Main module class
├── ModuleWorkspaceServiceProvider.php
└── helper.php                      # Global helper functions

assets/
├── config/
│   └── config.php                  # Package configuration
├── database/
│   └── migrations/
│       └── 0000_00_00_000000_create_workspaces_table.php
└── stubs/
    └── WorkspaceServiceProvider.stub.php
```

## Key Components

### Workspace Model

The `Workspace` model (`src/Models/Workspace/Workspace.php`) uses:
- **ULID primary keys** (not auto-increment)
- **Soft deletes** for data retention
- **HasProps** trait for JSON props storage
- **HasAddress** trait from module-regional
- **HasFileUpload** for logo management

**Database columns:**
- `id` (ULID, primary key)
- `uuid` (string, 36 chars)
- `name` (string, 50 chars)
- `owner_id` (foreign key to User)
- `props` (JSON - stores settings, integrations)
- `status` (enum: ACTIVE, INACTIVE, SUSPENDED, DRAFT)
- `created_at`, `updated_at`, `deleted_at`

**Relationships:**
- `tenant()` - morphOne to Tenant model
- `owner()` - belongsTo User model
- `installedFeature()` / `installedFeatures()` - morphOne/morphMany to InstalledFeature

### Workspace Schema

The schema class (`src/Schemas/Workspace.php`) contains business logic:
- `prepareStoreWorkspace()` - Create/update workspace with address and logo handling
- Uses caching with tags: `workspace`, `workspace-show`
- Cache duration: 24 hours

### Data Transfer Objects (DTOs)

Uses Spatie Laravel Data for structured data:

```php
// WorkspaceData structure
{
    "uuid": "optional-uuid",
    "name": "Workspace Name",
    "owner_id": "user-ulid",
    "status": "ACTIVE",
    "props": {
        "setting": {
            "address": { /* AddressData */ },
            "email": "workspace@example.com",
            "phone": "08123456789",
            "logo": "path/to/logo",
            "license": { /* license data */ },
            "stakeholder": { /* StakeholderData */ }
        },
        "integration": {
            "satu_sehat": { /* sync data */ }
        }
    }
}
```

### Events

Full lifecycle events are dispatched:
- `CreatingWorkspace` / `WorkspaceCreated`
- `SavingWorkspace` / `WorkspaceSaved`
- `UpdatingWorkspace` / `WorkspaceUpdated`
- `DeletingWorkspace` / `WorkspaceDeleted`

## Installation

```bash
php artisan module-workspace:install
```

This publishes migrations and also installs `module-regional` dependency.

## Important Warning: BaseServiceProvider

**CRITICAL:** This module extends `Hanafalah\LaravelSupport\Providers\BaseServiceProvider`.

When modifying the service provider:
1. **DO NOT** override `register()` without calling parent methods or using `registers()` helper
2. The `registers(['*'])` call auto-registers configs, migrations, models, schemas, and contracts
3. The provider validates that `App\Providers\WorkspaceServiceProvider` exists in the application

```php
// Correct pattern - use registers() helper
public function register()
{
    $this->registerMainClass(ModuleWorkspace::class)
        ->registerCommandService(Providers\CommandServiceProvider::class)
        ->registers([
            '*',
            'Provider' => function () {
                $this->validProviders([...]);
            },
        ]);
}
```

## Configuration

Published to `config/module-workspace.php`:

```php
return [
    'namespace' => 'Hanafalah\\ModuleWorkspace',
    'stakeholder' => 'Employee',  // Default stakeholder model
    'commands' => [
        Commands\InstallMakeCommand::class
    ],
];
```

## Usage Examples

### Creating a Workspace

```php
use Hanafalah\ModuleWorkspace\Schemas\Workspace;
use Hanafalah\ModuleWorkspace\Data\WorkspaceData;

$schema = app(Workspace::class);
$workspace = $schema->prepareStoreWorkspace(WorkspaceData::from([
    'name' => 'My Clinic',
    'owner_id' => $user->id,
    'props' => [
        'setting' => [
            'email' => 'clinic@example.com',
            'phone' => '08123456789',
            'address' => [
                'name' => '123 Main Street',
                // ... address fields
            ]
        ]
    ]
]));
```

### Using the HasRoom Trait

For models that belong to a room/facility:

```php
use Hanafalah\ModuleWorkspace\Concerns\Building\HasRoom;

class Equipment extends Model
{
    use HasRoom;

    // Adds room() relationship
    // Auto-adds room foreign key to fillable
}
```

### Accessing via Facade

```php
use Hanafalah\ModuleWorkspace\Facades\Workspace;

$moduleWorkspace = Workspace::setModelWorkspace($workspace);
$current = Workspace::getModelWorkspace();
```

## Satu Sehat Integration

The `ShowWorkspace` resource includes special handling for Satu Sehat integration data:
- Calculates sync progress from individual sync entries
- Limits logs to 20 entries (newest first)
- Computes total from/to values across all syncs

## Testing

When testing workspace functionality:
1. Create workspace with required fields (name is required)
2. Status defaults to DRAFT if not provided
3. Address is stored via morphOne relationship through HasAddress trait
4. Logo files are stored in `WORKSPACES/{uuid}/` path

## Common Pitfalls

1. **Missing WorkspaceServiceProvider** - The module validates `App\Providers\WorkspaceServiceProvider` exists
2. **UUID vs ID confusion** - Model uses ULID for `id`, separate `uuid` field for external reference
3. **Props structure** - Always use nested DTOs (WorkspacePropsData -> WorkspaceSettingData)
4. **Status enum typo** - Note: `SUPSENDED` (typo in enum, should be SUSPENDED but kept for backward compatibility)
