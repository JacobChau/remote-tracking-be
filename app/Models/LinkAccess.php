<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'link_id',
        'is_allowed',
    ];

    public function linkSetting(): BelongsTo
    {
        return $this->belongsTo(LinkSetting::class, 'link_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
