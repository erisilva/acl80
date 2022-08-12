<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description',
    ];

    public function scopeFilter($query, array $filters){
        if($filters['name'] ?? false) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        if($filters['description'] ?? false) {
            $query->where('description', 'like', '%' . request('description') . '%');
        }
    }

    /**
     * Operadores desse perfil
     *
     * @var User
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Permissões desse perfil
     *
     * @var Permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    } 
}
