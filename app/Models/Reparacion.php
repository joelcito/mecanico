<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reparacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reparaciones';

    protected $fillable = [
        'orden_servicio_id',
        'tecnico_id',

        'fecha_inicio',
        'fecha_fin',

        'trabajos_realizados',
        'observaciones',

        'estado',

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'deleted_at',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(
            OrdenServicio::class,
            'orden_servicio_id'
        );
    }

    public function tecnico()
    {
        return $this->belongsTo(
            User::class,
            'tecnico_id'
        );
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(
            User::class,
            'usuario_creador_id'
        );
    }

    public function usuarioModificador()
    {
        return $this->belongsTo(
            User::class,
            'usuario_modificador_id'
        );
    }

    public function usuarioEliminador()
    {
        return $this->belongsTo(
            User::class,
            'usuario_eliminador_id'
        );
    }


    public function detalles()
    {
        return $this->hasMany(ReparacionDetalle::class, 'reparacion_id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'reparacion_id');
    }
}