<?php

namespace Hanafalah\ModuleWorkspace\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspaceData as DataWorkspaceData;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspacePropsData;
use Hanafalah\ModuleWorkspace\Enums\Workspace\Status;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class WorkspaceData extends Data implements DataWorkspaceData{
    #[MapInputName('uuid')]
    #[MapName('uuid')]
    public ?string $uuid = null;

    #[MapInputName('workspace_model')]
    #[MapName('workspace_model')]
    public ?object $workspace_model = null;
    
    #[MapInputName('name')]
    #[MapName('name')]
    public string $name;

    #[MapInputName('owner_id')]
    #[MapName('owner_id')]
    public ?string $owner_id = null;

    #[MapInputName('status')]
    #[MapName('status')]
    public ?string $status = null;
        
    #[MapInputName('props')]
    #[MapName('props')]
    public ?WorkspacePropsData $props = null;

    public static function after(WorkspaceData $data): WorkspaceData{
        if (isset($data->uuid) && !isset($data->status)){
            $workspace = self::new()->WorkspaceModel()->uuid($data->uuid)->first();
            $data->status = $workspace->status ?? Status::DRAFT->value;
        }else{
            $data->status = Status::DRAFT->value;
        }
        
        return $data;
    }
}