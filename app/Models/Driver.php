<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; 

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'nomor_telepon',
        'nomor_sim',
        'sim_tanggal_kadaluarsa',
        'status',
    ];

    protected $casts = [
        'sim_tanggal_kadaluarsa' => 'date', // <-- Tambahkan ini
    ];
    
    public function truck(): HasOne
    {
        return $this->hasOne(Truck::class);
    }
}