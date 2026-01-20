<?php

namespace Hanafalah\ModuleWorkspace;

use Hanafalah\LaravelSupport\{
    Supports\PackageManagement
};
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleWorkspace\Contracts\ModuleWorkspace as ContractsModuleWorkspace;

class ModuleWorkspace extends PackageManagement implements ContractsModuleWorkspace {
    public ?Model $workspace_model = null;

    public function setModelWorkspace(Model $workspace_model): self{
        $this->workspace_model = $workspace_model;
        return $this;
    }

    public function getModelWorkspace(): ?Model{
        return $this->workspace_model;
    }
}
