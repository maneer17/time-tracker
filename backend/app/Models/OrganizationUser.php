<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\OrganizationRole;
class OrganizationUser extends Model
{
    use HasFactory;
    protected $table = 'organization_user';
    protected $fillable = ['user_id', 'organization_id', 'role'];
    protected $casts = [
        'role' => OrganizationRole::class,
    ];
    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function organization():BelongsTo{
        return $this->belongsTo(Organization::class);
    }
}
