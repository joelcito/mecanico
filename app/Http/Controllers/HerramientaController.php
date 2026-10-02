<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Herramienta;
use App\Models\Marca;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HerramientaController extends Controller
{
    public function listado()
    {
        $categorias = Categoria::whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->where('tipo', 'HERRAMIENTA')
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->where('tipo', 'HERRAMIENTA')
            ->orderBy('nombre')
            ->get();

        return view('herramienta.listado', compact('categorias', 'marcas'));
    }

    public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(
                Respuesta::error(null, 'Error al obtener los datos')->toArray(),
                400
            );
        }

        $herramientas = Herramienta::with(['categoria', 'marca'])
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        $listado = view('herramienta.ajaxListado', compact('herramientas'))
            ->render();

        return response()->json([
            'estado' => true,
            'data' => ['listado' => $listado],
        ]);
    }

    public function guardarHerramienta(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'max:100'],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')
                    ->where('tipo', 'HERRAMIENTA')
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at'),
            ],
            'marca_id' => [
                'nullable',
                Rule::exists('marcas', 'id')
                    ->where('tipo', 'HERRAMIENTA')
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at'),
            ],
            'unidad_medida' => ['required', 'string', 'max:50'],
            'cantidad' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'in:ACTIVO,INACTIVO'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'categoria_id.exists' => 'La categoría debe ser de tipo Herramienta.',
            'marca_id.exists' => 'La marca seleccionada debe ser de tipo Herramienta.',
        ]);

        $herramienta = $request->id
            ? Herramienta::whereNull('deleted_at')->findOrFail($request->id)
            : new Herramienta();

        $herramienta->codigo = $request->codigo;
        $herramienta->nombre = $request->nombre;
        $herramienta->descripcion = $request->descripcion;
        $herramienta->categoria_id = $request->categoria_id;
        $herramienta->marca_id = $request->marca_id;
        $herramienta->unidad_medida = $request->unidad_medida;
        $herramienta->cantidad = $request->cantidad;
        $herramienta->stock_minimo = $request->stock_minimo;
        $herramienta->estado = $request->estado ?? 'ACTIVO';

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $imagen = $request->file('imagen');
            $nombreImagen = uniqid() . '_' . preg_replace(
                '/[^A-Za-z0-9._-]/',
                '_',
                $imagen->getClientOriginalName()
            );

            Storage::disk('public')->putFileAs(
                'uploads/herramientas',
                $imagen,
                $nombreImagen
            );

            $herramienta->imagen = 'storage/uploads/herramientas/' . $nombreImagen;
        }

        if ($herramienta->exists) {
            $herramienta->usuario_modificador_id = Auth::id();
            $mensaje = 'Herramienta actualizada correctamente.';
        } else {
            $herramienta->usuario_creador_id = Auth::id();
            $mensaje = 'Herramienta registrada correctamente.';
        }

        $herramienta->save();

        return response()->json([
            'estado' => true,
            'mensaje' => $mensaje,
            'data' => $herramienta,
        ]);
    }

    public function eliminarHerramienta(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $herramienta = Herramienta::whereNull('deleted_at')
            ->findOrFail($request->id);
        $herramienta->usuario_eliminador_id = Auth::id();
        $herramienta->deleted_at = now();
        $herramienta->estado = 'INACTIVO';
        $herramienta->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Herramienta eliminada correctamente.',
        ]);
    }
}