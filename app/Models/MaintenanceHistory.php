<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\MaintenanceStatus;

class MaintenanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'truck_id',
        'maintenance_schedule_id',
        'tanggal_servis',
        'catatan',
        'tanggal_servis_berikutnya',
        'status',
        'total_biaya_spare_part',
    ];

    protected $casts = [
        'tanggal_servis' => 'date',
        'tanggal_servis_berikutnya' => 'date',
        'status' => \App\Enums\MaintenanceStatus::class,
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    // --- METHOD YANG HILANG ADA DI SINI ---
    public function spareParts(): BelongsToMany
    {
        return $this->belongsToMany(SparePart::class, 'maintenance_history_spare_part')
                    ->withPivot('jumlah');
    }
    // ------------------------------------
}