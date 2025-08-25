<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shops extends Model
{
    protected $fillable = ['shop_id', 'shop_name', 'region'];  

    public function metrics()
    {
        return $this->hasMany(metrics::class, 'shop_id', 'shop_id');
    }
}
