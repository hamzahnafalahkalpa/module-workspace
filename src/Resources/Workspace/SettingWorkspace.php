<?php

namespace Hanafalah\ModuleWorkspace\Resources\Workspace;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Http\Request;

class SettingWorkspace extends ApiResource{
    public function toArray(Request $request): array{
        $arr = [            
            "email"    => $this['email'] ?? null,
            'stakeholder' => $this['stakeholder'] ?? null,
            "phone"              => $this['phone'] ?? null,
            "timezone"           => $this['timezone'] ?? 'Asia/Jakarta',
            "address"            => [
                "id"             => $this['address']['id'] ?? null,
                "flag"           => $this['address']['flag'] ?? null,
                "name"           => $this['address']['name'] ?? null,
                "rt"             => $this['address']['props']['rt'] ?? null,
                "rw"             => $this['address']['props']['rw'] ?? null,
                "zip_code"       => $this['address']['props']['zip_code'] ?? null,
                "model_id"       => $this['address']['model_id'] ?? null,
                "model_type"     => $this['address']['model_type'] ?? null,
                "village_id"     => $this['address']['village_id'] ?? null,
                "district_id"    => $this['address']['district_id'] ?? null,
                "province_id"    => $this['address']['province_id'] ?? null,
                "subdistrict_id" => $this['address']['subdistrict_id'] ?? null
            ],
            'logo'  => isset($this['logo']) ? workspace_url($this['logo']) : null
        ];
        return $arr;
    }
}