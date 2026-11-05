<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Link extends Model
{

    public const RESERVED_CODES = [
        'login', 'logout', 'register', 'dashboard', 'links', 'profile',
        'forgot-password', 'reset-password', 'verify-email', 'confirm-password',
        'password', 'email', 'up', 'api', 'admin', 'storage', 'build', 'vendor',
    ];

    protected $fillable = ['original_url', 'code', 'expires_at', 'max_clicks'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public static function generateCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (
            in_array(strtolower($code), self::RESERVED_CODES, true)
            || static::where('code', $code)->exists()
        );

        return $code;
    }
}