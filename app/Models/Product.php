<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    public $timestamps = true;

    protected $casts = [
        'price' => 'float'
    ];

    protected $fillable = [
        'name',
        'article',        
        'price',
        'description',
        'created_at'
    ];

    public function images()
    {
        return $this->hasMany('App\Models\AttachmentsImage');
    }
}
