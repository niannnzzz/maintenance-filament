<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Truck extends Model
{
    use HasFactory;

    protected $fillable = [
        'nopol',
        'merek',
        'model',
        'tahun_pembuatan',
        'status',
        'driver_id',
        'kir_tanggal_kadaluarsa',
        'pajak_tanggal_kadaluarsa',
    ];

    protected $casts = [
    'kir_tanggal_kadaluarsa' => 'date', 
    'pajak_tanggal_kadaluarsa' => 'date',
    ];

    // Mendefinisikan bahwa satu Truk 'milik' satu Driver
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
