<?php

namespace Hanafalah\ModuleWorkspace\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleWorkspace\Contracts\Schemas\Workspace as ContractsWorkspace;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspaceData;

class Workspace extends PackageManagement implements ContractsWorkspace
{
    protected string $__entity      = 'Workspace';
    public $workspace_model;

    protected array $__cache = [
        'show' => [
            'name'     => 'workspace',
            'tags'     => ['workspace', 'workspace-show'],
            'duration' => 24*60 
        ]
    ];

    protected function prepareUpdateCreate(WorkspaceData $workspace_dto){
        $add = [
            'name'   => $workspace_dto->name, 
            'status' => $workspace_dto->status,
            'owner_id' => $workspace_dto->owner_id,
        ];
        if (isset($workspace_dto->uuid)){
            $guard = ['uuid' => $workspace_dto->uuid];
            $create = [$guard,$add];
        }else{
            $create = [$add];
        }
        return $this->usingEntity()->updateOrCreate(...$create);
    }

    public function prepareStoreWorkspace(WorkspaceData $workspace_dto): Model{
        $model = $this->prepareUpdateCreate($workspace_dto);
        if (isset($workspace_dto->props->setting->address) && isset($workspace_dto->props->setting->address->name)) {
            $this->prepareStoreAddressWorkspace($model, $workspace_dto);
        }
        if (isset($workspace_dto->props->setting->logo)) {
            $logo = &$workspace_dto->props->setting->logo;
            $logo = $model->setupFile($logo);
        }
        $this->fillingProps($model,$workspace_dto->props);
        $model->save();
        return $this->workspace_model = $model;
    }

    protected function prepareStoreAddressWorkspace(Model $workspace, WorkspaceData &$workspace_dto): void{
        $address_dto = &$workspace_dto->props->setting->address;
        $address_dto->model_type = $workspace->getMorphClass();
        $address_dto->model_id   = $workspace->getKey(); 
        $workspace->setAddress('OTHER',$address_dto);
    }
}
