<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenInspeccion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_inspecciones';

    protected $fillable = [
        'orden_servicio_id',
        'usuario_inspector_id',
        'fecha',
        'observaciones',
        'estado',

        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',

        'deleted_at',
    ];

    protected $casts = [
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

    public function inspector()
    {
        return $this->belongsTo(
            User::class,
            'usuario_inspector_id'
        );
    }

    public function detalles()
    {
        return $this->hasMany(
            OrdenInspeccionDetalle::class,
            'orden_inspeccion_id'
        );
    }

    public function fotos()
    {
        return $this->hasMany(
            OrdenFoto::class,
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
