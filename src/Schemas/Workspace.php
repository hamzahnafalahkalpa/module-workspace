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
        if (isset($workspace_dto->props->setting->address)) {
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
        $address             = &$workspace_dto->props->setting->address;
        $address->model_type = $workspace->getMorphClass();
        $address->model_id   = $workspace->getKey(); 
        $address_model       = $this->schemaContract('address')->prepareStoreAddress($address);
        $address->id         = $address_model->getKey();
        unset($address->props);
    }

    // public function prepareShowWorkspace(?Model $model = null, ? array $attributes = null): ?Model{
    //     $attributes ??= request()->all();

    //     $model ??= $this->getWorkspace();
    //     if (!isset($model)){
    //         $uuid = request()->uuid;
    //         if (!isset($uuid)) throw new \Exception('No uuid provided', 422);
    //         $model = $this->workspace()->with($this->showUsingRelation())->uuid($uuid)->firstOrFail();
    //     }else{
    //         $model->load($this->showUsingRelation());
    //     }
    //     return $this->workspace_model = $model;
    // }
}
