<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenDiagnostico extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'orden_diagnosticos';
    protected $fillable = [
        'orden_servicio_id',
        'usuario_diagnostico_id',
        'fecha',
        'descripcion',
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

    public function diagnostico()
    {
        return $this->belongsTo(
            User::class,
            'usuario_diagnostico_id'
        );
    }

    public function detalles()
    {
        return $this->hasMany(
            OrdenDiagnosticoDetalle::class,
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