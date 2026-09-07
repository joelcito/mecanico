<?php

namespace App\Services;

use Exception;

class BackupService
{
    public function generarBackup()
    {
        // $fecha = now()->format('Y-m-d_H-i-s');

        // $carpeta = storage_path('app/backups');

        // if (!file_exists($carpeta)) {
        //     mkdir($carpeta, 0777, true);
        // }

        // $archivo = $carpeta . "/backup_$fecha.sql";

        // $database = env('DB_DATABASE');
        // $user = env('DB_USERNAME');
        // $password = env('DB_PASSWORD');
        // $host = env('DB_HOST');

        // $comando = "mysqldump -h $host -u$user -p$password $database > \"$archivo\"";

        // exec($comando);

        // return $archivo;

        // $fecha = now()->format('Y-m-d_H-i-s');

        // // $carpeta = 'D:/Backups/Repuesto';
        // $carpeta = 'C:/Backups/Repuesto';

        // if (!file_exists($carpeta)) {
        //     mkdir($carpeta, 0777, true);
        // }

        // $archivo = $carpeta . "/backup_$fecha.sql";

        // $database = env('DB_DATABASE');
        // $user = env('DB_USERNAME');
        // $password = env('DB_PASSWORD');
        // $host = env('DB_HOST');

        // $paramPassword = !empty($password) ? "-p$password" : "";
        // $comando = "mysqldump -h $host -u$user $paramPassword $database > \"$archivo\"";
        // // $comando = "mysqldump -h $host -u$user -p$password $database > \"$archivo\"";

        // exec($comando);

        // return $archivo;

        $fecha = now()->format('Y-m-d_H-i-s');

        // $carpeta = 'D:/Backups/Repuesto';
        $carpeta = 'C:/Backups/Repuesto';

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $archivo = $carpeta . "/backup_$fecha.sql";

        $database = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        // $mysqldump = '"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe"';
        $mysqldump = '"C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe"';

        $paramPassword = !empty($password) ? "-p$password" : "";

        $comando = "$mysqldump -h $host -u$user $paramPassword $database > \"$archivo\"";

        exec($comando, $output, $resultado);

        if ($resultado !== 0) {
            throw new Exception("Error ejecutando backup: " . implode("\n", $output));
        }

        return $archivo;
    }
}
