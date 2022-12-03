<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function configurations(){
        return $this->hasMany(Configuration::class);
    }
}
