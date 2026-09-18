<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenFoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_fotos';

    protected $fillable = [
        'orden_servicio_id',
        'orden_inspeccion_id',
        'tipo',
        'ruta',
        'descripcion',
        'estado',

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];


    public function ordenServicio()
    {
        return $this->belongsTo(
            OrdenServicio::class,
            'orden_servicio_id'
        );
    }

    public function ordenInspeccion()
    {
        return $this->belongsTo(
            OrdenInspeccion::class,
            'orden_inspeccion_id'
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