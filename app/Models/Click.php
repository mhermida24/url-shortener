<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    public $timestamps = false;

    protected $fillable = ['link_id', 'ip', 'user_agent', 'browser', 'os', 'device', 'referer'];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}