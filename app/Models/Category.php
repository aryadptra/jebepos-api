<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Definisikan mutator untuk atribut created_at
    public function getCreatedAtAttribute($value)
    {
        // Ubah format atribut created_at menggunakan Carbon
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    // Definisikan mutator untuk atribut created_at
    public function getUpdateddAtAttribute($value)
    {
        // Ubah format atribut created_at menggunakan Carbon
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
