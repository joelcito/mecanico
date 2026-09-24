<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cajas';

    protected $fillable = [
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'usuario_id',
        'sucursal_id',

        'total_ingresos',
        'total_egresos',

        'monto_apertura',
        'monto_cierre',

        'fecha_apertura',
        'fecha_cierre',

        'estado',
        'deleted_at',
    ];

    protected $casts = [
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
}