<?php

namespace Hanafalah\ModuleWorkspace\Models\Workspace;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Concerns\Support\HasFileUpload;
use Hanafalah\ModuleWorkspace\Events;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleRegional\Concerns\HasAddress;
use Hanafalah\ModuleWorkspace\Enums;
use Hanafalah\ModuleWorkspace\Resources\Workspace\SettingWorkspace;
use Hanafalah\ModuleWorkspace\Resources\Workspace\ShowWorkspace;
use Hanafalah\ModuleWorkspace\Resources\Workspace\ViewWorkspace;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Workspace extends BaseModel
{
    use HasUlids, SoftDeletes, HasProps, HasAddress, HasFileUpload;
    
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list = [
        'id', 'uuid', 'name', 'owner_id', 'status', 'props'
    ];  

    protected $casts = [
        'uuid' => 'string',
        'name' => 'string',
        'owner_id' => 'string',
    ];

    protected static function booted(): void{
        parent::booted();
        static::creating(function ($query) {
            if (!isset($query->status)) $query->status = Enums\Workspace\Status::ACTIVE->value;
        });
    }

    protected function getFileNameAttribute(): string|callable{
        return function(){
            return $this->setting['logo'];
        };
    }

    protected function getFilePath(? string $path = null): string{
        $path ??= 'WORKSPACES/'.$this->uuid;
        return $this->storagePath($path);
    }

    public function showUsingRelation(): array{
        return ['address'];
    }

    public function getShowResource(){
        return ShowWorkspace::class;
    }

    public function getViewResource(){
        return ViewWorkspace::class;
    }

    public function getSettingResource(){
        return SettingWorkspace::class;
    }

    public function tenant(){return $this->morphOneModel('Tenant','reference');}
    public function owner(){return $this->belongsToModel('User','owner_id');}
    public function installedFeature(){return $this->morphOneModel('InstalledFeature','model');}
    public function installedFeatures(){return $this->morphManyModel('InstalledFeature','model');}

    public function toSettingApi(){
        return ($this->getSettingResource() !== null)
            ? new ($this->getSettingResource())($this->setting)
            : $this->toArray();
    }

    protected $dispatchesEvents = [
        'saving'   => Events\SavingWorkspace::class,
        'saved'    => Events\WorkspaceSaved::class,
        'creating' => Events\CreatingWorkspace::class,
        'created'  => Events\WorkspaceCreated::class,
        'updating' => Events\UpdatingWorkspace::class,
        'updated'  => Events\WorkspaceUpdated::class,
        'deleting' => Events\DeletingWorkspace::class,
        'deleted'  => Events\WorkspaceDeleted::class,
    ];
}
