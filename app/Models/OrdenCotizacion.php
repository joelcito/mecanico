<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCotizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_cotizaciones';

    protected $fillable = [
        'orden_servicio_id',
        'usuario_cotizador_id',
        'fecha',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'observaciones',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(
            OrdenServicio::class,
            'orden_servicio_id'
        );
    }

    public function detalles()
    {
        return $this->hasMany(
            OrdenCotizacionDetalle::class,
            'orden_cotizacion_id'
        );
    }

    public function cotizador()
    {
        return $this->belongsTo(
            User::class,
            'usuario_cotizador_id'
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
}