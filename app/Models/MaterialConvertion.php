<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialConvertion extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
