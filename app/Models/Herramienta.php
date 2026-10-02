<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Herramienta extends Model
{
    protected $table = 'herramientas';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'marca_id',
        'unidad_medida',
        'cantidad',
        'stock_minimo',
        'imagen',
        'estado',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
        'deleted_at',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    public function usuarioModificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_modificador_id');
    }

    public function usuarioEliminador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_eliminador_id');
    }

    public function asignacionesHerramientas(): HasMany
    {
        return $this->hasMany(AsignacionHerramienta::class, 'herramienta_id');
    }
}