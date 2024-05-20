<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image', 'expired_at', 'modified_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
