<?php

namespace Hanafalah\ModuleWorkspace\Resources\Workspace;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Hanafalah\ModuleRegional\Resources\Address\ShowAddress;

class ViewWorkspace extends ApiResource
{

    /**
     * Transform the resource into an array.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'id'        => $this->id,
            'uuid'      => $this->uuid,
            'name'      => $this->name,
            'phone'     => $this->phone,
            'owner_id'  => $this->owner_id,
            'owner'     => $this->prop_owner,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $arr;
    }
}
