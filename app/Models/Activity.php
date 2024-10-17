<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'content',
        'type',
        'sport_id',
        'organization_id',
        'start_date',
        'end_date',
        'venue',
        'address',
        'attachment',
        'target_player',
        'status',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sport() : BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function organization() : BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

}
