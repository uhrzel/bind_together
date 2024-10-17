<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsfeed extends Model
{
    /** @use HasFactory<\Database\Factories\NewsfeedFactory> */
    use HasFactory;

    protected $fillable = [
        'description',
        'user_id',
        'status'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newsfeedFiles() : HasMany
    {
        return $this->hasMany(NewsfeedFile::class);
    } 

    public function comments() : HasMany
    {
        return $this->hasMany(Comments::class);
    }

}
