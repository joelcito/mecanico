<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
       

        $automovil = [
            'Cenicero',
            'Encendedor',
            'Manual del vehículo',
            'Espejos',
            'Radio',
            'Pisos',
            'Control de alarma',
            'Limpiaparabrisas',
            'Herramientas',
            'Gato',
            'Llave de rueda',
            'Arresta llamas',
            'Llanta de auxilio',
            'Varilla',
            'Tapacubos',
            'Halógenos',
            'Antena',
            'Tapa de combustible',
            'Extintor',
            'Triángulo de seguridad',
            'Llave de seguridad',
            'Placas',
            'Botiquín',
            'USB',
        ];

        foreach ($automovil as $orden => $nombre) {
            ChecklistItem::create([
                'nombre' => $nombre,
                'tipo_vehiculo' => 'AUTOMOVIL',
                'tipo_propulsion' => null,
                'orden' => $orden + 1,
                'estado' => 'ACTIVO',
            ]);
        }


        $motocicletaCombustion = [
            'Espejos',
            'Manubrio',
            'Puños',
            'Maneta de embrague',
            'Maneta de freno',
            'Tablero',
            'Llave de contacto',
            'Faro delantero',
            'Luz trasera',
            'Direccionales delanteras',
            'Direccionales traseras',
            'Luz de freno',
            'Bocina',
            'Freno delantero',
            'Freno trasero',
            'Cadena',
            'Piñón',
            'Corona',
            'Neumático delantero',
            'Neumático trasero',
            'Suspensión delantera',
            'Suspensión trasera',
            'Caballete',
            'Batería',
            'Escape',
            'Tanque de combustible',
            'Tapa del tanque',
            'Nivel de combustible',
            'Herramientas',
        ];

        foreach ($motocicletaCombustion as $orden => $nombre) {
            ChecklistItem::create([
                'nombre' => $nombre,
                'tipo_vehiculo' => 'MOTOCICLETA',
                'tipo_propulsion' => 'COMBUSTION',
                'orden' => $orden + 1,
                'estado' => 'ACTIVO',
            ]);
        }


        $motocicletaElectrica = [
            'Espejos',
            'Manubrio',
            'Puños',
            'Maneta de freno',
            'Tablero',
            'Llave de contacto',
            'Faro delantero',
            'Luz trasera',
            'Direccionales delanteras',
            'Direccionales traseras',
            'Luz de freno',
            'Bocina',
            'Freno delantero',
            'Freno trasero',
            'Neumático delantero',
            'Neumático trasero',
            'Suspensión delantera',
            'Suspensión trasera',
            'Caballete',
            'Batería',
            'Estado de batería',
            'Cargador',
            'Cable de carga',
            'Puerto de carga',
            'Indicador de batería',
            'Motor eléctrico',
            'Controlador',
            'Herramientas',
        ];

        foreach ($motocicletaElectrica as $orden => $nombre) {
            ChecklistItem::create([
                'nombre' => $nombre,
                'tipo_vehiculo' => 'MOTOCICLETA',
                'tipo_propulsion' => 'ELECTRICO',
                'orden' => $orden + 1,
                'estado' => 'ACTIVO',
            ]);
        }
    }
}