<?php

namespace Hanafalah\ModuleWorkspace\Contracts\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Hanafalah\ModuleWorkspace\Contracts\Data\WorkspaceData;

/**
 * @see \Hanafalah\ModuleWorkspace\Schemas\Workspace
 * @method bool deleteWorkspace()
 * @method bool prepareDeleteWorkspace(? array $attributes = null)
 * @method mixed getWorkspace()
 * @method ?Model prepareShowWorkspace(?Model $model = null, ?array $attributes = null)
 * @method array showWorkspace(?Model $model = null)
 * @method Collection prepareViewWorkspaceList()
 * @method array viewWorkspaceList()
 * @method array storeWorkspace(?WorkspaceData $workspace_dto = null)
 * @method LengthAwarePaginator prepareViewWorkspacePaginate(PaginateData $paginate_dto)
 * @method array viewWorkspacePaginate(?PaginateData $paginate_dto = null)
 * @method Builder workspace(mixed $conditionals = null)
 */
interface Workspace extends DataManagement
{
    public function prepareStoreWorkspace(WorkspaceData $workspace_dto): Model;
}
