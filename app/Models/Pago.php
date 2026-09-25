<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'orden_servicio_id',
        'caja_id',
        'sucursal_id',

        'monto',
        'cambio',
        'monto_recibido',

        'fecha',
        'descripcion',
        'tipo_pago',

        'estado',
        'deleted_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'cambio' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'fecha' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(
            OrdenServicio::class,
            'orden_servicio_id'
        );
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function movimientoCaja()
    {
        return $this->hasOne(MovimientoCaja::class);
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