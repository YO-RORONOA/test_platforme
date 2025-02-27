<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'name',
        'username',
        'email',
        'password',
        'is_verified',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
    
    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
    
    public function hasPermission($permission)
    {
        // Logique pour vérifier les permissions
        // Pour simplifier, on considère que certains rôles ont certaines permissions
        $rolePermissions = [
            'Admin' => ['*'],
            'CME' => ['view_candidates', 'evaluate_tests'],
            'Coach' => ['view_candidates', 'evaluate_tests'],
            'candidate' => ['submit_documents', 'take_quiz'],
        ];
        
         
        foreach ($this->roles as $role) {
            if (isset($rolePermissions[$role->name])) {
                if (in_array('*', $rolePermissions[$role->name]) || 
                    in_array($permission, $rolePermissions[$role->name])) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
        

