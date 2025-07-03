<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
    'nama_servis',
    'interval_hari', 
    'deskripsi',
    ];
}
