<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenDiagnosticoDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden_diagnostico_detalles';

    protected $fillable = [
        'orden_diagnostico_id',
        'descripcion',
        'tipo',
        'prioridad',
        'observacion',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function ordenDiagnostico()
    {
        return $this->belongsTo(
            OrdenDiagnostico::class,
            'orden_diagnostico_id'
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