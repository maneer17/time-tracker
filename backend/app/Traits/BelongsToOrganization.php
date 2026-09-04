<?php

namespace App\Traits;

use App\Models\Scopes\OrgScope;
use Illuminate\Support\Facades\App;

trait BelongsToOrganization
{
    protected static function booted(): void
    {
        static::addGlobalScope(new OrgScope);

        static::creating(function ($model) {
            if (! $model->organization_id && App::bound('active_org')) {
                $model->organization_id = App::make('active_org')->id;
            }
        });
    }
}