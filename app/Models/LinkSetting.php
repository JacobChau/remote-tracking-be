<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'access_type',
        'is_enabled',
        'start_date',
        'end_date',
        'hash',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(LinkAccess::class, 'link_id');
    }
}
