<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class InvitationLog extends Model
{
    protected $fillable = ['invitation_id', 'user_id', 'action'];
    use HasFactory;
    public function invitation(): BelongsTo{
        return $this->belongsTo(Invitation::class);
    }
    public function changed_by(): BelongsTo{
        return $this->belongsTo(User::class);
    }

}
