<?php
USE PDO;
class Database
{
    public function conectar(): PDO
    {
        $host = 'db';
        $nombreBD = 'crudclientes';
        $usuario = 'db';
        $clave = 'db';

        $dsn = "mysql:host=$host;dbname=$nombreBD;charset=utf8mb4";

        $conexion = new PDO($dsn, $usuario, $clave, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $conexion;
    }
}