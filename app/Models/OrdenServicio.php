<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}