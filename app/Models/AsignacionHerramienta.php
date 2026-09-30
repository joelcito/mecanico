<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsignacionHerramienta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asignaciones_herramientas';

    protected $fillable = [
        'producto_id',
        'mecanico_id',
        'cantidad',
        'fecha_asignacion',
        'fecha_devolucion',
        'estado',
        'observaciones',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
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