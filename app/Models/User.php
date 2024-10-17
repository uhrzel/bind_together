<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_number',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'birthdate',
        'gender',
        'address',
        'contact',
        'email',
        'year_level',
        'sport_id',
        'course_id',
        'campus_id',
        'program_id',
        'organization_id',
        'avatar',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(UserTypeEnum::SUPERADMIN);
    }
    public function isAdminSport()
    {
        return $this->hasRole(UserTypeEnum::ADMINSPORT);
    }
    public function isAdminOrg()
    {
        return $this->hasRole(UserTypeEnum::ADMINORG);
    }
    public function isCoach()
    {
        return $this->hasRole(UserTypeEnum::COACH);
    }
    public function isAdviser()
    {
        return $this->hasRole(UserTypeEnum::ADVISER);
    }
    public function isStudent()
    {
        return $this->hasRole(UserTypeEnum::STUDENT);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    public function likedComments(): BelongsToMany
    {
        return $this->belongsToMany(Comments::class, 'comment_likes', 'user_id', 'comments_id');
    }

}
