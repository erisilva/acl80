<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
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
     * Perfis dessa permissão
     *
     * @var Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
