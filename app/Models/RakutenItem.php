<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RakutenItem extends Model
{
    use HasFactory;

    public function rakuten()
    {
        return $this->belongsTo(Rakuten::class);
    }
}
