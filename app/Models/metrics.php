<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class metrics extends Model
{
    protected $fillable = ['shop_id', 'date', 'gmv_usd', 'followers', 'ctor'];

    public function shop()
    {
        return $this->belongsTo(shops::class, 'shop_id', 'shop_id');
    }
}
