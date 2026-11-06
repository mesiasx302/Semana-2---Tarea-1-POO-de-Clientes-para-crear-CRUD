<?php

session_start();

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/Cliente.php';
require_once __DIR__ . '/../app/controllers/ClienteController.php';

$accion = $_GET['accion'] ?? 'index';

$controlador = new ClienteController();

switch ($accion) {
    case 'crear':
        $controlador->crear();
        break;
    case 'guardar':
        $controlador->guardar();
        break;
    case 'editar':
        $controlador->editar();
        break;
    case 'actualizar':
        $controlador->actualizar();
        break;
    case 'eliminar':
        $controlador->eliminar();
        break;
    default:
        $controlador->index();
}