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
        'herramienta_id',
        'reparacion_id',
        'mecanico_id',
        'cantidad',
        'fecha_asignacion',
        'fecha_devolucion',
        'estado',
        'observaciones',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_devolucion_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function productoHistorico()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class, 'herramienta_id');
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class, 'reparacion_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function usuarioAsignador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function usuarioDevolucion()
    {
        return $this->belongsTo(User::class, 'usuario_devolucion_id');
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