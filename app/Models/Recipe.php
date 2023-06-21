<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient');
    }
}
