<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCotizacionDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_cotizacion_detalles';

    protected $fillable = [
        'orden_cotizacion_id',
        'tipo',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(
            OrdenCotizacion::class,
            'orden_cotizacion_id'
        );
    }

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'producto_id'
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