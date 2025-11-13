<?php

namespace Hanafalah\ModuleWorkspace\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspacePropsData as DataWorkspacePropsData;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspaceSettingData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class WorkspacePropsData extends Data implements DataWorkspacePropsData{
    #[MapInputName('setting')]
    #[MapName('setting')]
    public ?WorkspaceSettingData $setting = null;

    #[MapInputName('integration')]
    #[MapName('integration')]
    public ?array $integration = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;
}