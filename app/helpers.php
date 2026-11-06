<?php

function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verificar_csrf(): void
{
    $esperado = $_SESSION['csrf'] ?? '';
    $recibido = $_POST['csrf'] ?? '';

    if ($esperado === '' || !is_string($recibido) || !hash_equals($esperado, $recibido)) {
        http_response_code(403);
        echo 'Solicitud no válida.';
        exit;
    }
}

function poner_mensaje(string $texto): void
{
    $_SESSION['mensaje'] = $texto;
}

function sacar_mensaje(): ?string
{
    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje']);
    return $mensaje;
}