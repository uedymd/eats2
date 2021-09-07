<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rakuten extends Model
{
    use HasFactory;

    public function rakuten_items()
    {
        return $this->hasMany(RakutenItem::class);
    }
}
