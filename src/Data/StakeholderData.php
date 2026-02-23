<?php

namespace Hanafalah\ModuleWorkspace\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleWorkspace\Contracts\Data\StakeholderData as DataStakeholderData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Support\Str;

class StakeholderData extends Data implements DataStakeholderData{
    #[MapName('props')] 
    #[MapInputName('props')] 
    public ?array $props = null;

    public static function after(self $data): self{
        $props = &$data->props;
        $new   = static::new();
        $model_name = config('module-workspace.stakeholder');
        if (isset($model_name)){
            $new_stakeholder = $props;
            if (isset($new_stakeholder)){
                foreach ($props as $key => $prop) {
                    if (!is_array($prop) && Str::endsWith($key, '_id') && isset($prop)){
                        $name = Str::replace('_id','',$key);
                        $model = $new->{$model_name.'Model'}()->findOrFail($prop);
                        $new_stakeholder[$name] = [
                            'id'     => $prop,
                            'status' => null,
                            'name'   => $model->name ?? null,
                            'at'     => $model->at ?? null
                        ];
                    }
                }
            }
            $props = $new_stakeholder;
        }
        return $data;
    }
}