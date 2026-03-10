<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        //updated by users row conditions
        'name',
        'surname',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'cap',
        'province',
        'bio',
        'image',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // get the roles that belong to the user model
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')->withPivot('status', 'approved_at', 'approved_by', 'rejection_reason')->withTimestamps();
    }

    public function hasRole(string|array $roles): bool{
        return $this->roles()
            ->whereIn('name', (array) $roles)
            ->wherePivot('status', ['auto_approved', 'manually_approved'])
            ->exists();
    }

    public function hasPendingRoleRequest(string|array $roles): bool{
        return $this->roles()
            ->whereIn('name', (array) $roles)
            ->wherePivot('status', 'pending')
            ->exists();
    }
    public function assignRole(string $roleName, array $pivotData = []): void{
        $roleId = \App\Models\Role::where('name', $roleName)->value('id');

        $this->roles()
            ->syncWithoutDetaching([$roleId => $pivotData]);
    }

    public function activeRoles(){
        return $this->roles()
            ->wherePivotIn('status', ['auto_approved', 'manually_approved']);
    }

    public function pendingRoles(){
        return $this->roles()
            ->wherePivot('status', 'pending');
    }

    public function isAdmin(): bool{
        return $this->hasRole('admin');
    }

    public function profile(){
        return $this->hasOne(UserProfile::class);
    }
    
}
