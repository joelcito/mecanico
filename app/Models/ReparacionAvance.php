<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReparacionAvance extends Model
{
    protected $table = 'reparacion_avances';

    protected $fillable = [
        'reparacion_id',
        'usuario_id',
        'tecnico_id',
        'fecha',
        'descripcion',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class, 'reparacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}