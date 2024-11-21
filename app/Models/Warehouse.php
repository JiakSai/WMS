<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Warehouse extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'warehouse_id',
        'created_by',
        'updated_by'
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }
}
