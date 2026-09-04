<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class OrgScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (App::bound('active_org')) {
            $builder->where('organization_id', App::make('active_org')->id);
        }
    }
}