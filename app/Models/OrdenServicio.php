<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrdenInspeccion;
use App\Models\OrdenFoto;
use App\Models\OrdenCotizacion;
use App\Models\OrdenDiagnostico;
use App\Models\Pago;

class OrdenServicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_servicios';

    protected $fillable = [
        'numero_orden',
        'vehiculo_id',
        'fecha_recepcion',
        'kilometraje',
        'motivo_ingreso',
        'nivel_combustible',
        'observaciones',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
        'nivel_combustible' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

   

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    

    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

   

    public function usuarioModificador()
    {
        return $this->belongsTo(User::class, 'usuario_modificador_id');
    }

   

    public function usuarioEliminador()
    {
        return $this->belongsTo(User::class, 'usuario_eliminador_id');
    }


    public function inspecciones()
    {
        return $this->hasMany(
            OrdenInspeccion::class,
            'orden_servicio_id'
        );
    }

    public function fotos()
    {
        return $this->hasMany(
            OrdenFoto::class,
            'orden_servicio_id'
        );
    }

    public function inspeccionActual()
    {
        return $this->hasOne(
            OrdenInspeccion::class,
            'orden_servicio_id'
        )->latestOfMany();
    }

    public function diagnosticos()
    {
        return $this->hasMany(
            OrdenDiagnostico::class,
            'orden_servicio_id'
        );
    }

    public function diagnosticoActual()
    {
        return $this->hasOne(
            OrdenDiagnostico::class,
            'orden_servicio_id'
        )->latestOfMany();
    }



    public function cotizaciones()
    {
        return $this->hasMany(
            OrdenCotizacion::class,
            'orden_servicio_id'
        );
    }

    public function cotizacionActual()
    {
        return $this->hasOne(
            OrdenCotizacion::class,
            'orden_servicio_id'
        )->latestOfMany();
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_servicio_id');
    }

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()
            ->where('estado', 'ACTIVO')
            ->sum('monto');
    }
    public function getSaldoPendienteAttribute()
    {
        $total = $this->cotizacionActual?->total ?? 0;
        return max(
            0,
            $total - $this->total_pagado
        );
    }
    public function getEstadoPagoAttribute()
    {
        $total = $this->cotizacionActual?->total ?? 0;
        $pagado = $this->total_pagado;
        if ($pagado <= 0) {
            return 'SIN_PAGO';
        }
        if ($pagado < $total) {
            return 'PARCIAL';
        }
        return 'PAGADO';
    }
        
    }