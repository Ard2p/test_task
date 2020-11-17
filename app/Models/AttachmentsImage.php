<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentsImage extends Model
{
    use HasFactory;

    protected $table = 'attachment_image';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'image_path'
    ];


    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
