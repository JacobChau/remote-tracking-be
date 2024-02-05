<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\ResetPasswordQueued;
use App\Notifications\VerifyEmailQueued;
use App\Traits\HasCreatedBy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Meeting extends Model
{
    use HasFactory, Notifiable, HasCreatedBy, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title', 'start_date', 'end_date',
        'offer', 'answer', 'ice_candidates',
    ];

    /**
     * The attributes that should be cast.
     *
 * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'ice_candidates' => 'array',
    ];

    // ------------------ Relationships ------------------
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_meetings')->withTimestamps();
    }

    public function linkSetting(): HasMany
    {
        return $this->hasMany(LinkSetting::class);
    }
}
